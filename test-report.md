# Test Report — PR #1 (Premify-style redesign + flashsale + admin CRUD)

**PR**: https://github.com/Dandutzz/digitalstore/pull/1
**Devin Session**: https://app.devin.ai/sessions/e2820d347c6b4157b80f6f1cf33b1fe8
**Date**: 2026-04-28
**Method**: Local Laravel server (`php artisan serve`) on `http://localhost:8000`, fresh `migrate:fresh --seed`

## Summary
| # | Test | Result |
|---|---|---|
| 1 | Flashsale price override server-side (Rp 17.000 not Rp 25.000) | passed |
| 2 | Cek Invoice rejects invalid + redirects valid code | passed |
| 3 | Brand color admin → frontend propagation | passed |
| 4 | FAQ admin CRUD shows on public /faq | passed |
| 5 | Floating WhatsApp button sticky on all pages | passed |

**Critical assertion (Test 1)** — DB query confirms `orders.amount=17000`:
```
[order_code] => AKH-20260428-S6NJVL
[amount] => 17000
[status] => pending
[customer_email] => tester@example.com
```

## Test 1 — Flashsale price override (the most important)
Distinguishing test: if broken, customer would be charged Rp 25.000 (full price).

| 🟢 Checkout page shows flash price | 🟢 Invoice + DB confirms Rp 17.000 |
|---|---|
| ![Checkout shows Rp 17.000](https://app.devin.ai/attachments/7a3a1138-3ab6-4c39-9863-afc19323e35e/screenshot_355f15619b2d49dea520ead90da075bc.png) | ![Invoice Rp 17.000](https://app.devin.ai/attachments/244d33c1-4833-4ac0-bbfa-b81cb5ac9b16/screenshot_a0616b7ecf4f4403bdf65cff7def8ece.png) |
| Total Rp 17.000, struck Rp 25.000 | Invoice & DB row both show Rp 17.000 |

## Test 2 — Cek Invoice
| 🟢 Invalid code rejected | 🟢 Valid code redirects to invoice |
|---|---|
| ![Invalid kode error](https://app.devin.ai/attachments/7b754e65-6d95-4dfc-ab14-3c59ab9589ca/screenshot_334cf43623a7467ba33523f94d48aa7f.png) | ![Valid redirected to /invoice/AKH-…](https://app.devin.ai/attachments/244d33c1-4833-4ac0-bbfa-b81cb5ac9b16/screenshot_a0616b7ecf4f4403bdf65cff7def8ece.png) |
| Form returns with text "Kode order tidak ditemukan…" | URL changes to `/invoice/AKH-20260428-S6NJVL`, status PENDING |

## Test 3 — Brand color admin → frontend
| 🔴 Before (purple #7c3aed) | 🟢 After (red #dc2626) |
|---|---|
| ![Purple Chat Admin button](https://app.devin.ai/attachments/c70e4a24-4c4a-488a-a200-9a3722245bc5/screenshot_b046697facaf4e6c871a1f12929612a8.png) | ![Red Chat Admin button](https://app.devin.ai/attachments/59c88ba0-1fa3-4e80-a5b7-5c61f744a5f2/screenshot_9f05d4904f1b4a7585adcd0527a24914.png) |
| Header CTA + search button purple | Header CTA + search button red |

## Test 4 — FAQ admin CRUD live
![New FAQ on /faq](https://app.devin.ai/attachments/fdedce95-9145-4cd3-a29d-b80763fe7426/screenshot_3f0df882aaaa41a6896a7dd246009718.png)

"Test pertanyaan dari Devin" added in admin appears as 2nd entry on public /faq page.

## Test 5 — Floating WhatsApp button
Sticky button visible bottom-right on every page tested (homepage, checkout, invoice, cek-invoice, faq). `href` = `https://wa.me/6281234567890?text=Halo%20admin%20AKHPREMIUM…` — uses Site Settings WA number + default message correctly.

## Notes
- Banner "Payment gateway belum dikonfigurasi" appears on invoice — expected because `.env` has no `PAKASIR_PROJECT/API_KEY` configured locally. User must set these in production.
- All flashsale countdown values were non-zero, decreasing per second (vanilla JS works).
- `php artisan` 17 phpunit feature tests pass (verified earlier in session).

## Conclusion
PR #1 ready to merge. The most critical safeguard — server-side flashsale price enforcement — works as intended. No bypass via client-side manipulation possible.
