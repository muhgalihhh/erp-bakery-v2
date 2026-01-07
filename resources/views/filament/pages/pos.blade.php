<x-filament-panels::page>
    {{-- Simple Button to Open POS --}}
    <div class="flex items-center justify-between mb-6">
        <p class="text-sm text-gray-600 dark:text-gray-400">
            Klik tombol untuk membuka halaman kasir dalam mode fullscreen.
        </p>
        <a href="{{ route('pos.fullscreen') }}" target="_blank"
            class="fi-btn fi-btn-size-lg fi-btn-color-primary inline-flex items-center justify-center gap-2 px-5 py-2.5 text-sm font-semibold text-white bg-primary-600 rounded-lg shadow-sm hover:bg-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition">
            <x-heroicon-o-arrow-top-right-on-square class="w-5 h-5" />
            Buka Kasir / POS
        </a>
    </div>
</x-filament-panels::page>
