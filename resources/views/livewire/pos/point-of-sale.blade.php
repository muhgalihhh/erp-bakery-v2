<div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100">
    {{-- Product Grid & Cart Container --}}
    <div class="flex h-screen overflow-hidden">
        {{-- LEFT: Product Section --}}
        <div class="flex-1 flex flex-col bg-white border-r-2 border-gray-200 shadow-lg">
            {{-- Search & Filter Bar --}}
            <div class="p-6 border-b-2 border-gray-200 bg-gradient-to-r from-blue-50 to-indigo-50">
                <div class="flex gap-4">
                    <div class="flex-1 relative">
                        <svg class="absolute left-4 top-1/2 transform -translate-y-1/2 w-5 h-5 text-gray-400"
                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                        <input wire:model.live.debounce.300ms="searchProduct" type="text"
                            placeholder="🔍 Cari produk berdasarkan nama..."
                            class="w-full pl-12 pr-4 py-4 text-lg border-2 border-gray-300 rounded-xl focus:ring-4 focus:ring-blue-500 focus:border-blue-500 transition-all shadow-sm" />
                    </div>
                </div>
            </div>

            {{-- Product Grid --}}
            <div class="flex-1 overflow-y-auto p-6 bg-gray-50">
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-4">
                    @forelse($products as $product)
                        <button wire:click="addToCart({{ $product->id }})"
                            class="group bg-white border-2 border-gray-200 rounded-2xl p-5 hover:border-blue-500 hover:shadow-2xl transition-all duration-300 text-left transform hover:-translate-y-1">
                            <div
                                class="aspect-square bg-gradient-to-br from-blue-50 to-indigo-50 rounded-xl mb-4 flex items-center justify-center group-hover:from-blue-100 group-hover:to-indigo-100 transition-all">
                                <svg class="w-16 h-16 text-blue-400 group-hover:text-blue-600 transition-colors"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                </svg>
                            </div>
                            <h3
                                class="font-bold text-gray-900 mb-2 line-clamp-2 group-hover:text-blue-600 transition-colors text-sm">
                                {{ $product->name }}</h3>
                            <p class="text-xl font-bold text-blue-600">
                                Rp {{ number_format($product->selling_price ?? 0, 0, ',', '.') }}
                            </p>
                            @if ($product->current_stock && $product->current_stock < 10)
                                <p class="text-xs text-orange-600 mt-1">Stok: {{ $product->current_stock ?? 0 }}</p>
                            @endif
                        </button>
                    @empty
                        <div class="col-span-full text-center py-16 text-gray-500">
                            <svg class="w-24 h-24 mx-auto mb-6 text-gray-300" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4">
                                </path>
                            </svg>
                            <p class="text-xl font-semibold mb-2">Tidak ada produk ditemukan</p>
                            <p class="text-sm text-gray-400">Coba gunakan kata kunci pencarian yang berbeda</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- RIGHT: Cart & Checkout Section --}}
        <div class="w-96 xl:w-[32rem] flex flex-col bg-white shadow-2xl">
            {{-- Customer Selection --}}
            <div class="p-6 border-b-2 border-gray-200 bg-gradient-to-r from-purple-50 to-pink-50">
                <label class="block text-sm font-bold text-gray-700 mb-3 uppercase tracking-wide">👤 Customer</label>
                <select wire:model.live="selectedCustomerId"
                    class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:ring-4 focus:ring-purple-500 focus:border-purple-500 font-semibold transition-all shadow-sm">
                    <option value="">🚶 Walk-in Customer</option>
                    @foreach ($customers as $customer)
                        <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                    @endforeach
                </select>
                @if ($selectedCustomerId && $selectedCustomer)
                    <p class="text-xs text-purple-600 mt-2 font-medium">✅ Customer terpilih:
                        {{ $selectedCustomer['name'] ?? '' }}</p>
                @endif
            </div>

            {{-- Cart Items --}}
            <div class="flex-1 overflow-y-auto p-6 space-y-3 bg-gray-50">
                @forelse($cart as $index => $item)
                    <div
                        class="bg-white rounded-2xl p-4 border-2 border-gray-200 shadow-sm hover:shadow-md transition-shadow">
                        <div class="flex justify-between items-start mb-3">
                            <h4 class="font-bold text-gray-900 flex-1 pr-3 text-sm">{{ $item['name'] }}</h4>
                            <button wire:click="removeFromCart({{ $index }})"
                                class="text-red-500 hover:text-red-700 hover:bg-red-50 p-2 rounded-lg transition-all"
                                title="Hapus item">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </div>
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <button wire:click="updateQuantity({{ $index }}, {{ $item['quantity'] - 1 }})"
                                    class="w-9 h-9 flex items-center justify-center bg-gradient-to-br from-gray-100 to-gray-200 hover:from-gray-200 hover:to-gray-300 rounded-lg font-bold text-gray-700 transition-all shadow-sm">−</button>
                                <input type="number"
                                    wire:change="updateQuantity({{ $index }}, $event.target.value)"
                                    value="{{ $item['quantity'] }}"
                                    class="w-16 text-center border-2 border-gray-300 rounded-lg py-2 font-bold text-gray-900 focus:ring-2 focus:ring-blue-500"
                                    min="1" />
                                <button wire:click="updateQuantity({{ $index }}, {{ $item['quantity'] + 1 }})"
                                    class="w-9 h-9 flex items-center justify-center bg-gradient-to-br from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white rounded-lg font-bold transition-all shadow-sm">+</button>
                            </div>
                            <div class="text-right">
                                <p class="text-xs text-gray-500">@ Rp {{ number_format($item['price'], 0, ',', '.') }}
                                </p>
                                <p class="text-lg font-bold text-gray-900">Rp
                                    {{ number_format($item['price'] * $item['quantity'], 0, ',', '.') }}</p>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-16 text-gray-400">
                        <svg class="w-24 h-24 mx-auto mb-6 text-gray-300" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z">
                            </path>
                        </svg>
                        <p class="text-xl font-bold mb-2">Keranjang Kosong</p>
                        <p class="text-sm text-gray-400">Pilih produk untuk memulai transaksi</p>
                    </div>
                @endforelse
            </div>

            {{-- Summary & Checkout --}}
            <div class="border-t-4 border-blue-500 p-6 space-y-4 bg-gradient-to-br from-blue-50 to-indigo-50">
                <div class="space-y-3 text-base">
                    <div class="flex justify-between items-center">
                        <span class="text-gray-700 font-medium">Subtotal</span>
                        <span class="font-bold text-gray-900">Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
                    </div>
                    @if ($discountAmount > 0)
                        <div
                            class="flex justify-between items-center bg-green-100 border-2 border-green-300 rounded-xl p-3 -mx-2">
                            <div class="flex items-center gap-2">
                                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z">
                                    </path>
                                </svg>
                                <div>
                                    <span class="text-green-700 font-bold">Diskon</span>
                                    @if ($discountName)
                                        <p class="text-xs text-green-600 font-medium">{{ $discountName }}</p>
                                    @endif
                                </div>
                            </div>
                            <span class="font-bold text-green-700">- Rp
                                {{ number_format($discountAmount, 0, ',', '.') }}</span>
                        </div>
                    @else
                        <div class="flex justify-between items-center text-gray-400 text-sm">
                            <span>💡 Diskon otomatis akan diterapkan jika memenuhi syarat</span>
                        </div>
                    @endif
                    <div class="flex justify-between items-center">
                        <span class="text-gray-700 font-medium">Pajak ({{ $taxPercentage }}%)</span>
                        <span class="font-bold text-gray-900">Rp {{ number_format($taxAmount, 0, ',', '.') }}</span>
                    </div>
                </div>
                <div class="border-t-2 border-gray-300 pt-4 flex justify-between items-center">
                    <span class="text-2xl font-bold text-gray-900">TOTAL</span>
                    <span class="text-3xl font-bold text-blue-600">Rp {{ number_format($total, 0, ',', '.') }}</span>
                </div>
                <button wire:click="processPayment" @if (empty($cart)) disabled @endif
                    class="w-full py-5 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 disabled:from-gray-300 disabled:to-gray-400 disabled:cursor-not-allowed text-white font-bold text-xl rounded-2xl shadow-xl hover:shadow-2xl transition-all duration-300 transform hover:scale-105 disabled:transform-none">
                    @if (empty($cart))
                        ❌ Keranjang Kosong
                    @else
                        💳 BAYAR SEKARANG
                    @endif
                </button>
                <div class="grid grid-cols-2 gap-3 text-sm">
                    <div class="text-center p-3 bg-white rounded-lg border border-gray-200">
                        <p class="text-gray-500 text-xs">Total Item</p>
                        <p class="font-bold text-lg text-gray-900">{{ count($cart) }}</p>
                    </div>
                    <div class="text-center p-3 bg-white rounded-lg border border-gray-200">
                        <p class="text-gray-500 text-xs">Total Qty</p>
                        <p class="font-bold text-lg text-gray-900">{{ collect($cart)->sum('quantity') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Receipt Modal --}}
    @if ($showReceipt && $lastOrderId)
        <div class="fixed inset-0 bg-black bg-opacity-60 flex items-center justify-center p-4 z-50 backdrop-blur-sm"
            wire:click="$set('showReceipt', false)">
            <div class="bg-white rounded-3xl shadow-2xl max-w-md w-full p-8 transform transition-all" wire:click.stop>
                <div class="text-center mb-8">
                    <div
                        class="mx-auto w-24 h-24 bg-gradient-to-br from-green-400 to-green-600 rounded-full flex items-center justify-center mb-6 shadow-xl">
                        <svg class="w-14 h-14 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7">
                            </path>
                        </svg>
                    </div>
                    <h2 class="text-3xl font-bold text-gray-900 mb-3">Transaksi Berhasil!</h2>
                    <div class="bg-blue-50 border-2 border-blue-200 rounded-2xl p-4 mb-4">
                        <p class="text-sm text-gray-600 mb-1">Nomor Order</p>
                        <p class="text-2xl font-bold text-blue-600">#{{ $lastOrderId }}</p>
                    </div>
                    <div class="grid grid-cols-2 gap-4 mb-6">
                        <div class="bg-gray-50 rounded-xl p-4">
                            <p class="text-xs text-gray-500 mb-1">Total Pembayaran</p>
                            <p class="text-lg font-bold text-gray-900">Rp {{ number_format($total, 0, ',', '.') }}</p>
                        </div>
                        <div class="bg-gray-50 rounded-xl p-4">
                            <p class="text-xs text-gray-500 mb-1">Waktu</p>
                            <p class="text-sm font-bold text-gray-900">{{ now()->format('H:i') }}</p>
                        </div>
                    </div>
                    <p class="text-gray-600 text-sm">Terima kasih atas pembelian Anda! 🎉</p>
                </div>
                <div class="space-y-3">
                    <button wire:click="$set('showReceipt', false)"
                        class="w-full py-4 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white font-bold rounded-2xl shadow-lg hover:shadow-xl transition-all transform hover:scale-105">
                        ✓ Selesai
                    </button>
                    <button onclick="window.print()"
                        class="w-full py-3 bg-white border-2 border-gray-300 hover:border-blue-500 text-gray-700 hover:text-blue-600 font-semibold rounded-2xl transition-all">
                        🖨️ Cetak Struk
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
