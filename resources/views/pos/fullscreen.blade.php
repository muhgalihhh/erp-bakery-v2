@extends('layouts.pos')

@section('title', 'POS Fullscreen')

@section('content')
    <div class="h-screen flex flex-col">
        {{-- Top Navigation Bar --}}
        <div class="pos-gradient text-white px-6 py-4 shadow-lg flex-shrink-0">
            <div class="flex justify-between items-center">
                {{-- Left: Logo & Title --}}
                <div class="flex items-center gap-4">
                    <a href="{{ url('/admin/pos') }}"
                        class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-white/90 bg-white/10 rounded-lg hover:bg-white/20 transition-all duration-200 backdrop-blur-sm border border-white/20">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                        <span>Dashboard</span>
                    </a>
                    <div class="h-8 w-px bg-white/20"></div>
                    <div class="flex items-center gap-3">
                        <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z">
                            </path>
                        </svg>
                        <div>
                            <h1 class="text-2xl font-bold leading-tight">Point of Sale</h1>
                            <p class="text-white/70 text-sm">{{ config('app.name', 'Bakery ERP') }}</p>
                        </div>
                    </div>
                </div>

                {{-- Center: Quick Info --}}
                <div class="hidden lg:flex items-center gap-6">
                    <div class="text-center px-4 py-2 bg-white/10 rounded-lg backdrop-blur-sm">
                        <p class="text-xs text-white/70">Tanggal</p>
                        <p class="text-sm font-semibold">{{ now()->translatedFormat('d F Y') }}</p>
                    </div>
                    <div class="text-center px-4 py-2 bg-white/10 rounded-lg backdrop-blur-sm">
                        <p class="text-xs text-white/70">Waktu</p>
                        <p class="text-sm font-semibold" id="current-time">{{ now()->format('H:i:s') }}</p>
                    </div>
                </div>

                {{-- Right: User Info --}}
                <div class="flex items-center gap-4">
                    <div class="flex items-center gap-3 px-4 py-2 bg-white/10 rounded-lg backdrop-blur-sm">
                        <div class="w-10 h-10 rounded-full bg-white/20 flex items-center justify-center">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                        </div>
                        <div class="text-left">
                            <p class="text-xs text-white/70">Kasir</p>
                            <p class="font-semibold text-sm">{{ auth()->user()->name }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Main POS Content --}}
        <div class="flex-1 overflow-hidden">
            @livewire('pos.point-of-sale', ['fullscreen' => true])
        </div>

        {{-- Bottom Shortcut Info --}}
        <div class="bg-gray-800 text-white px-6 py-2 flex-shrink-0">
            <div class="flex items-center justify-center gap-8 text-xs">
                <div class="flex items-center gap-2">
                    <kbd class="px-2 py-1 bg-gray-700 rounded">F9</kbd>
                    <span class="text-gray-300">Bayar</span>
                </div>
                <div class="flex items-center gap-2">
                    <kbd class="px-2 py-1 bg-gray-700 rounded">F8</kbd>
                    <span class="text-gray-300">Kosongkan</span>
                </div>
                <div class="flex items-center gap-2">
                    <kbd class="px-2 py-1 bg-gray-700 rounded">ESC</kbd>
                    <span class="text-gray-300">Tutup Modal</span>
                </div>
            </div>
        </div>
    </div>
@endsection
