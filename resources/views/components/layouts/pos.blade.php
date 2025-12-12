<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="ltr" class="h-full bg-gray-50">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'BakerySys') }} - Point of Sale</title>

    <style>
        [x-cloak] {
            display: none !important;
        }

        body {
            margin: 0;
            padding: 0;
            overflow-x: hidden;
        }
    </style>

    @filamentStyles
    @vite('resources/css/app.css')
</head>

<body class="antialiased bg-gray-50">
    {{-- Header Kasir --}}
    <div class="sticky top-0 z-50 bg-gradient-to-r from-blue-600 to-purple-600 shadow-lg">
        <div class="flex items-center justify-between px-6 py-4">
            <div class="flex items-center gap-4">
                <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z">
                    </path>
                </svg>
                <div>
                    <h1 class="text-2xl font-bold text-white">Point of Sale</h1>
                    <p class="text-sm text-blue-100">{{ config('app.name', 'BakerySys') }} - Sistem Kasir</p>
                </div>
            </div>

            <div class="flex items-center gap-6">
                {{-- Tanggal & Waktu --}}
                <div class="text-right">
                    <p class="text-sm font-semibold text-white" x-data
                        x-text="new Date().toLocaleDateString('id-ID', {
                        weekday: 'long',
                        year: 'numeric',
                        month: 'long',
                        day: 'numeric'
                    })">
                    </p>
                    <p class="text-xs text-blue-100" x-data
                        x-text="new Date().toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', second: '2-digit' })"
                        x-init="setInterval(() => { $el.textContent = new Date().toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', second: '2-digit' }) }, 1000)">
                    </p>
                </div>

                {{-- User Info --}}
                <div class="flex items-center gap-3 px-4 py-2 bg-white/10 rounded-lg backdrop-blur-sm">
                    <div
                        class="w-12 h-12 rounded-full bg-white/20 flex items-center justify-center ring-2 ring-white/30">
                        <span
                            class="text-lg font-bold text-white">{{ strtoupper(substr(auth()->user()->name, 0, 2)) }}</span>
                    </div>
                    <div class="text-left">
                        <p class="text-sm font-bold text-white">{{ auth()->user()->name }}</p>
                        <p class="text-xs text-blue-100">Kasir Aktif</p>
                    </div>
                </div>

                {{-- Back Button --}}
                <a href="{{ route('filament.admin.resources.point-of-sales.index') }}"
                    class="flex items-center gap-2 px-6 py-3 text-sm font-bold text-white transition-all duration-200 bg-white/20 rounded-lg hover:bg-white/30 shadow-lg backdrop-blur-sm">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                            d="M10 19l-7-7m0 0l7-7m-7 7h18">
                        </path>
                    </svg>
                    Dashboard
                </a>
            </div>
        </div>
    </div>

    {{-- Content Area --}}
    <div class="w-full" style="height: calc(100vh - 80px);">
        {{ $slot }}
    </div>

    @filamentScripts
    @vite('resources/js/app.js')
    @livewire('notifications')
</body>

</html>
