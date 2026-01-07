@props([
    'closeable' => true,
])

<div class="px-6 py-4 bg-gradient-to-r from-blue-50 to-indigo-50 border-b border-gray-200">
    <div class="flex items-center justify-between">
        <h3 class="text-lg font-bold text-gray-900">
            {{ $slot }}
        </h3>

        @if ($closeable)
            <button type="button" @click="close()"
                class="text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg p-2 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        @endif
    </div>
</div>
