{{-- Clear Cart Confirmation Modal --}}
<x-pos.modal name="showClearCartModal" maxWidth="md">
    <x-pos.modal-header>
        <div class="flex items-center gap-2 text-red-600">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
            </svg>
            Konfirmasi Hapus Keranjang
        </div>
    </x-pos.modal-header>

    <x-pos.modal-body class="text-center py-6">
        <div class="mb-4">
            <svg class="w-16 h-16 mx-auto text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
            </svg>
        </div>
        <h3 class="text-lg font-bold text-gray-900 mb-2">Yakin ingin mengosongkan keranjang?</h3>
        <p class="text-gray-600 text-sm">Semua item di keranjang akan dihapus. Tindakan ini tidak dapat dibatalkan.</p>

        @if (!empty($cart))
            <div class="mt-4 p-3 bg-gray-50 rounded-lg text-left">
                <p class="text-xs font-medium text-gray-700 mb-2">Item yang akan dihapus:</p>
                <ul class="text-xs text-gray-600 space-y-1 max-h-32 overflow-y-auto custom-scrollbar">
                    @foreach ($cart as $item)
                        <li class="flex justify-between">
                            <span>{{ $item['name'] }}</span>
                            <span class="font-medium">{{ $item['quantity'] }}x</span>
                        </li>
                    @endforeach
                </ul>
            </div>
        @endif
    </x-pos.modal-body>

    <x-pos.modal-footer>
        <x-pos.button variant="secondary" @click="close()">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
            Batal
        </x-pos.button>
        <x-pos.button variant="danger" wire:click="clearCart">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
            </svg>
            Ya, Kosongkan
        </x-pos.button>
    </x-pos.modal-footer>
</x-pos.modal>
