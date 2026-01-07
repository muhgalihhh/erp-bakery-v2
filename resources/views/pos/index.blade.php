<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>POS - Kasir | {{ config('app.name', 'Bakery ERP') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    <style>
        .pos-gradient {
            background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%);
        }
    </style>
</head>

<body class="bg-gray-100">
    {{-- Top Navigation Bar --}}
    <div class="pos-gradient text-white px-4 py-3 shadow-lg sticky top-0 z-50">
        <div class="flex justify-between items-center">
            {{-- Left: Back button & Logo --}}
            <div class="flex items-center gap-4">
                <a href="{{ url('/admin/pos') }}"
                    class="inline-flex items-center gap-2 px-3 py-2 text-sm font-medium text-white/90 bg-white/10 rounded-lg hover:bg-white/20 transition-all duration-200 backdrop-blur-sm border border-white/20">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    <span class="hidden sm:inline">Kembali ke Admin</span>
                </a>
                <div class="h-8 w-px bg-white/20 hidden sm:block"></div>
                <div class="flex items-center gap-3">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                        xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z">
                        </path>
                    </svg>
                    <div>
                        <h1 class="text-xl font-bold leading-tight">Point of Sale</h1>
                        <p class="text-white/70 text-xs">{{ config('app.name', 'Bakery ERP') }}</p>
                    </div>
                </div>
            </div>

            {{-- Right: User info & actions --}}
            <div class="flex items-center gap-4">
                {{-- Current Date/Time --}}
                <div class="hidden md:block text-right">
                    <p class="text-xs text-white/70">{{ now()->translatedFormat('l, d F Y') }}</p>
                    <p class="text-sm font-medium" id="current-time">{{ now()->format('H:i:s') }}</p>
                </div>
                <div class="h-8 w-px bg-white/20 hidden md:block"></div>
                {{-- User Info --}}
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-full bg-white/20 flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                    </div>
                    <div class="text-right hidden sm:block">
                        <p class="text-xs text-white/70">Kasir</p>
                        <p class="font-semibold text-sm">{{ auth()->user()->name }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Main POS Content --}}
    <div class="min-h-[calc(100vh-60px)] bg-gray-50">
        @livewire('pos.point-of-sale')
    </div>

    @livewireScripts

    {{-- Real-time clock script --}}
    <script>
        function updateClock() {
            const now = new Date();
            const timeString = now.toLocaleTimeString('id-ID', {
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit'
            });
            const clockElement = document.getElementById('current-time');
            if (clockElement) {
                clockElement.textContent = timeString;
            }
        }
        setInterval(updateClock, 1000);
        updateClock();
    </script>
</body>

</html>
