# Test Plan — PR #1 (Premify-style redesign + flashsale + admin CRUD)

Repo: github.com/Dandutzz/digitalstore PR #1
Local URL: http://localhost:8000 | Admin: http://localhost:8000/admin (admin@akhpremium.test / password)

Code grounding (so plan distinguishes working vs broken):
- Flashsale price override server-side: `app/Models/ProductVariant.php` `effectivePrice()` + `app/Http/Controllers/CheckoutController.php:67`
- Flashsale display + countdown: `resources/views/welcome.blade.php:54-95` (renders `Rp 17.000` for Netflix 1 Bulan, strike `Rp 25.000`, countdown computed from `end_at`)
- Brand color injection: `resources/views/layouts/app.blade.php:31-37` (CSS `--brand` from `$site->brand_color`)
- Cek invoice flow: `app/Http/Controllers/FrontController.php:111-127` (valid → redirect `/invoice/{code}`; invalid → return view with `$error` text "Kode order tidak ditemukan...")
- FAQ render: `resources/views/pages/faq.blade.php` from `Faq::active()->orderBy('sort_order')`

## Primary E2E Flow

### Test 1 — Flashsale price override is server-enforced (NOT just display)
Distinguishing: if broken, checkout would charge full Rp 25.000 instead of Rp 17.000.
1. Navigate to http://localhost:8000
2. **Assert**: Flash Sale banner visible with countdown (HH:MM:SS format, all non-zero, decreasing)
3. **Assert**: Netflix flash card shows "Rp 17.000" in rose color AND struck-through "Rp 25.000"
4. Click Netflix card → product detail page
5. Click "Beli Sekarang" on "1 Bulan" variant
6. On checkout page, fill email `tester@example.com`, submit
7. **Assert**: After submit, page (or invoice) shows total amount = **Rp 17.000** (NOT Rp 25.000)
8. **Assert**: DB `orders` table latest row has `amount = 17000` (verified via `php artisan tinker`)

### Test 2 — Cek Invoice form: invalid + valid lookup
Distinguishing: if broken, invalid code would still redirect or show no error; valid code wouldn't redirect.
1. Navigate to http://localhost:8000/cek-invoice
2. Type "AKH-99999999-XXXXXX" → submit
3. **Assert**: Same page reloads with error text containing "Kode order tidak ditemukan"
4. Type the order_code from Test 1 → submit
5. **Assert**: Browser URL changes to `/invoice/{order_code}`, page shows "PENDING" or "PAID" status badge

### Test 3 — Brand color admin override propagates to frontend
Distinguishing: if broken, changing color in admin wouldn't change visible color on homepage.
1. Login admin → go to **Site Settings** page (sidebar)
2. Note current brand color (#7c3aed purple) — confirm CTA button "Beli Sekarang" on homepage is purple
3. Change Brand Color to **#dc2626** (red), save
4. Reload homepage
5. **Assert**: Computed `--brand` CSS variable = `#dc2626` (verified via DevTools or visual — primary CTA buttons now red, NOT purple)

### Test 4 — Admin FAQ CRUD live updates frontend
Distinguishing: if broken, new FAQ wouldn't appear on /faq.
1. Admin → FAQ → New
2. Question: "Test pertanyaan dari Devin", Answer: "Jawaban tes E2E"
3. Save
4. Navigate to http://localhost:8000/faq
5. **Assert**: New FAQ entry visible with the exact question text

### Test 5 — Floating WhatsApp live chat button is sticky on all pages
Distinguishing: if broken, button missing or wa.me link malformed.
1. Visit homepage → scroll to bottom
2. **Assert**: Green WA button visible bottom-right
3. Inspect href → **Assert** matches `https://wa.me/6281234567890?text=...`
4. Navigate to /faq, /cara-pemesanan, /artikel
5. **Assert**: WA button persists on each page

## Skipped (redundant or covered by unit tests)
- Foto upload product → covered by Filament built-in FileUpload, low risk; demo screenshot only if time permits
- Article CRUD → same pattern as FAQ (covered by Test 4)
- Order fulfillment auto-stock-assign → covered by 17/17 PHPUnit tests passing

## Pass criteria
ALL 5 tests pass with screenshots; flashsale amount in DB = 17000 (not 25000) is the most critical assertion.
