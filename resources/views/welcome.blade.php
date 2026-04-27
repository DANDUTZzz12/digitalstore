<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Akhpremium - Store</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { 
            font-family: 'Plus Jakarta Sans', sans-serif; 
            background-color: #f8fafc; /* Putih keabu-abuan / Off-white */
        }
    </style>
</head>
<body class="antialiased pb-20">

    <nav class="bg-white border-b border-purple-100 sticky top-0 z-50 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <div class="flex items-center gap-2">
                    <span class="text-2xl font-extrabold bg-gradient-to-r from-purple-600 to-blue-500 text-transparent bg-clip-text tracking-tight">
                        AKHPREMIUM
                    </span>
                </div>
                <div class="flex gap-6">
                    <a href="#" class="text-slate-600 hover:text-purple-600 font-bold transition">Home</a>
                    <a href="#katalog" class="text-slate-600 hover:text-purple-600 font-bold transition">Katalog</a>
                </div>
            </div>
        </div>
    </nav>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-8">
        <div class="bg-gradient-to-r from-purple-100 via-white to-blue-100 rounded-3xl p-6 md:p-8 relative shadow-sm border border-purple-200">
            
            <div class="flex flex-col md:flex-row justify-between items-center relative z-10">
                <div>
                    <h2 class="text-3xl font-extrabold flex items-center gap-2 text-slate-800">
                        <span class="text-purple-500">⚡</span> FLASH SALE <span class="text-blue-500">⚡</span>
                    </h2>
                    <p class="text-slate-600 mt-1 font-medium">Diskon gila-gilaan, waktu terbatas!</p>
                </div>
                
                <div class="mt-4 md:mt-0 flex gap-3 text-center">
                    <div class="bg-white border border-purple-200 rounded-xl p-3 min-w-[70px] shadow-sm">
                        <span id="hours" class="text-2xl font-black text-purple-700">02</span>
                        <p class="text-xs text-slate-500 font-bold mt-1 uppercase">Jam</p>
                    </div>
                    <div class="bg-white border border-purple-200 rounded-xl p-3 min-w-[70px] shadow-sm">
                        <span id="minutes" class="text-2xl font-black text-blue-600">45</span>
                        <p class="text-xs text-slate-500 font-bold mt-1 uppercase">Mnt</p>
                    </div>
                    <div class="bg-white border border-purple-200 rounded-xl p-3 min-w-[70px] shadow-sm">
                        <span id="seconds" class="text-2xl font-black text-purple-700">30</span>
                        <p class="text-xs text-slate-500 font-bold mt-1 uppercase">Dtk</p>
                    </div>
                </div>
            </div>

            <div class="mt-8 grid grid-cols-1 md:grid-cols-3 gap-5 relative z-10">
                <div class="bg-white rounded-2xl p-5 shadow-md border border-purple-100 hover:shadow-xl hover:border-purple-300 transition duration-300 transform hover:-translate-y-1">
                    <div class="flex justify-between items-start">
                        <h3 class="text-xl font-extrabold text-slate-800">Netflix 1U1P 4K</h3>
                        <span class="bg-purple-100 text-purple-700 text-xs font-black px-3 py-1 rounded-full">-50%</span>
                    </div>
                    <p class="text-slate-500 text-sm mt-1 font-medium">Bergaransi 30 Hari Full</p>
                    <div class="mt-5">
                        <span class="text-slate-400 line-through text-sm font-semibold">Rp 40.000</span>
                        <p class="text-3xl font-black bg-gradient-to-r from-purple-600 to-blue-500 text-transparent bg-clip-text mt-1">Rp 20.000</p>
                    </div>
                    <button class="w-full mt-6 bg-gradient-to-r from-purple-500 to-blue-500 hover:from-purple-600 hover:to-blue-600 text-white font-bold py-3 rounded-xl transition shadow-md hover:shadow-lg">
                        Beli Sekarang
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div id="katalog" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-12 mb-20">
        <h2 class="text-2xl font-extrabold mb-6 text-slate-800 flex items-center gap-3">
            <span class="w-2 h-8 rounded-full bg-gradient-to-b from-purple-500 to-blue-400"></span> Katalog Premium
        </h2>
        
        <div class="grid grid-cols-2 md:grid-cols-4 gap-5">
            <div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-100 hover:border-purple-300 hover:shadow-md transition group">
                <div class="w-12 h-12 rounded-full bg-purple-50 flex items-center justify-center mb-4 group-hover:bg-purple-100 transition">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3"></path></svg>
                </div>
                <h3 class="text-lg font-bold text-slate-800">Spotify Premium</h3>
                <p class="text-slate-500 text-xs mt-1 font-medium">1 Bulan Full Garansi</p>
                <div class="mt-4">
                    <p class="text-2xl font-black text-slate-800">Rp 15.000</p>
                </div>
                <button class="w-full mt-4 bg-slate-50 border border-slate-200 text-slate-700 hover:bg-purple-50 hover:text-purple-700 hover:border-purple-200 font-bold py-2.5 rounded-xl transition">
                    Lihat Detail
                </button>
            </div>

            <div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-100 hover:border-blue-300 hover:shadow-md transition group">
                <div class="w-12 h-12 rounded-full bg-blue-50 flex items-center justify-center mb-4 group-hover:bg-blue-100 transition">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                </div>
                <h3 class="text-lg font-bold text-slate-800">Canva Pro</h3>
                <p class="text-slate-500 text-xs mt-1 font-medium">Via Invite Link</p>
                <div class="mt-4">
                    <p class="text-2xl font-black text-slate-800">Rp 10.000</p>
                </div>
                <button class="w-full mt-4 bg-slate-50 border border-slate-200 text-slate-700 hover:bg-blue-50 hover:text-blue-700 hover:border-blue-200 font-bold py-2.5 rounded-xl transition">
                    Lihat Detail
                </button>
            </div>
        </div>
    </div>

    <script>
        let time = 10000; 
        setInterval(() => {
            time--;
            let h = Math.floor(time / 3600).toString().padStart(2, '0');
            let m = Math.floor((time % 3600) / 60).toString().padStart(2, '0');
            let s = (time % 60).toString().padStart(2, '0');
            document.getElementById('hours').innerText = h;
            document.getElementById('minutes').innerText = m;
            document.getElementById('seconds').innerText = s;
        }, 1000);
    </script>
</body>
</html>