<x-filament-panels::page>
    {{-- Header with fullscreen button --}}
    <div class="flex justify-between items-center mb-4">
        <div class="flex items-center gap-3">
            <h2 class="text-xl font-bold text-gray-900 dark:text-white">Point of Sale</h2>
            <span
                class="px-2 py-1 text-xs bg-green-100 text-green-800 rounded-full dark:bg-green-800 dark:text-green-100">
                Online
            </span>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('pos.fullscreen') }}" target="_blank"
                class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4" />
                </svg>
                Fullscreen Mode
            </a>
        </div>
    </div>

    {{-- POS Component --}}
    <div
        class="w-full h-[calc(100vh-220px)] bg-gray-50 rounded-xl overflow-hidden border border-gray-200 dark:border-gray-700">
        <livewire:pos.point-of-sale />
    </div>
</x-filament-panels::page>
