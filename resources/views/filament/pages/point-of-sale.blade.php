<x-filament-panels::page>
    {{-- Custom Styles for Modern POS --}}
    <style>
        .pos-gradient {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        
        .pos-card {
            backdrop-filter: blur(10px);
            transition: all 0.3s ease;
        }
        
        .pos-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }
        
        .product-card {
            position: relative;
            overflow: hidden;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        .product-card:hover {
            transform: scale(1.05);
            z-index: 10;
        }
        
        .product-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(135deg, rgba(102, 126, 234, 0.1) 0%, rgba(118, 75, 162, 0.1) 100%);
            opacity: 0;
            transition: opacity 0.3s;
        }
        
        .product-card:hover::before {
            opacity: 1;
        }
        
        .cart-item {
            animation: slideIn 0.3s ease-out;
        }
        
        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateX(20px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }
        
        .pulse-animation {
            animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
        }
        
        @keyframes pulse {
            0%, 100% {
                opacity: 1;
            }
            50% {
                opacity: 0.5;
            }
        }
        
        .number-pad-btn {
            @apply h-14 text-lg font-bold rounded-xl transition-all duration-200;
        }
        
        .number-pad-btn:hover {
            transform: scale(1.05);
        }
        
        .number-pad-btn:active {
            transform: scale(0.95);
        }
    </style>

    <div x-data="{
        showPayment: false,
        showReceipt: @entangle('showReceipt').live,
        openPayment() {
            const cartCount = {{ count($cart) }};
            const totalAmount = {{ $total }};
    
            if (cartCount > 0) {
                $wire.paidAmount = totalAmount;
                this.showPayment = true;
                $nextTick(() => {
                    if ($refs.paidAmountInput) {
                        $refs.paidAmountInput.focus();
                        $refs.paidAmountInput.select();
                    }
                });
            } else {
                alert('⚠️ Keranjang masih kosong! Silakan tambahkan produk terlebih dahulu.');
            }
        },
        closePayment() {
            this.showPayment = false;
        },
        async processPaymentNow() {
            const paidAmount = parseFloat($wire.paidAmount) || 0;
            const totalAmount = {{ $total }};
    
            if (paidAmount < totalAmount) {
                alert('⚠️ Jumlah bayar kurang dari total!');
                return;
            }
    
            this.showPayment = false;
            await $wire.processPayment();
        }
    }" @keydown.f4.window.prevent="openPayment()"
        @keydown.f2.window.prevent="$refs.searchInput?.focus(); $refs.searchInput?.select()"
        @keydown.escape.window.prevent="if (confirm('Hapus semua item dari keranjang?')) { $wire.resetCart() }">
        
        {{-- Header Bar with Store Info --}}
        <div class="mb-6 p-4 rounded-xl pos-gradient text-white shadow-xl">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <div class="p-3 bg-white/20 rounded-xl backdrop-blur-sm">
                        <x-filament::icon icon="heroicon-o-shopping-cart" class="w-8 h-8" />
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold">Point of Sale</h1>
                        <p class="text-white/80 text-sm">{{ config('app.name', 'Bakery ERP') }} - Kasir Aktif</p>
                    </div>
                </div>
                <div class="text-right">
                    <p class="text-sm text-white/80">Kasir</p>
                    <p class="font-bold text-lg">{{ auth()->user()->name }}</p>
                    <p class="text-xs text-white/60">{{ now()->format('d M Y, H:i') }}</p>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            {{-- LEFT: Products Grid (2/3) --}}
            <div class="lg:col-span-2 space-y-4">

                {{-- Search Bar - Modern Design --}}
                <div class="relative">
                    <div class="absolute inset-0 bg-gradient-to-r from-blue-400 to-purple-500 rounded-2xl blur opacity-20"></div>
                    <div class="relative bg-white dark:bg-gray-800 rounded-2xl shadow-xl border-2 border-gray-200 dark:border-gray-700 p-6">
                        <div class="flex items-center gap-4">
                            <div class="p-3 bg-gradient-to-br from-blue-500 to-purple-600 rounded-xl shadow-lg">
                                <x-filament::icon icon="heroicon-o-magnifying-glass" class="w-6 h-6 text-white" />
                            </div>
                            <div class="flex-1">
                                <x-filament::input.wrapper>
                                    <x-filament::input 
                                        x-ref="searchInput" 
                                        type="text"
                                        wire:model.live.debounce.300ms="searchProduct"
                                        placeholder="🔍 Cari produk dengan nama atau SKU... (F2)"
                                        class="text-lg font-medium border-0 focus:ring-2 focus:ring-purple-500" />
                                </x-filament::input.wrapper>
                            </div>
                            @if ($searchProduct)
                                <x-filament::button 
                                    wire:click="$set('searchProduct', '')" 
                                    color="danger" 
                                    icon="heroicon-o-x-circle" 
                                    size="lg"
                                    class="shadow-lg">
                                    Bersihkan
                                </x-filament::button>
                            @endif
                        </div>
                        <div class="mt-3 flex items-center gap-2 text-sm text-gray-500 dark:text-gray-400">
                            <x-filament::icon icon="heroicon-o-information-circle" class="w-4 h-4" />
                            <span>Gunakan F2 untuk fokus pencarian, F4 untuk pembayaran, ESC untuk reset keranjang</span>
                        </div>
                    </div>
                </div>

                {{-- Products Grid - Modern Card Design --}}
                <div class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-4 gap-4">
                    @php
                        $products = \App\Models\Product::where('is_sellable', true)
                            ->where('type', 'finished')
                            ->where('is_active', true)
                            ->when($searchProduct, function ($query) {
                                $query->where(function ($q) {
                                    $q->where('name', 'like', '%' . $this->searchProduct . '%')->orWhere(
                                        'sku',
                                        'like',
                                        '%' . $this->searchProduct . '%',
                                    );
                                });
                            })
                            ->orderBy('name')
                            ->limit(50)
                            ->get();
                    @endphp

                    @forelse($products as $product)
                        <button 
                            wire:click="addToCart({{ $product->id }})"
                            class="product-card group bg-white dark:bg-gray-800 rounded-2xl shadow-lg hover:shadow-2xl border-2 border-gray-200 dark:border-gray-700 p-4 {{ $product->current_stock <= 0 ? 'opacity-50 cursor-not-allowed' : 'hover:border-purple-500' }}"
                            @if ($product->current_stock <= 0) disabled @endif>
                            
                            {{-- Product Image --}}
                            <div class="relative aspect-square bg-gradient-to-br from-gray-50 to-gray-100 dark:from-gray-700 dark:to-gray-800 rounded-xl mb-3 overflow-hidden shadow-inner">
                                @if ($product->image_url)
                                    <img src="{{ $product->image_url }}" 
                                         alt="{{ $product->name }}"
                                         class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                                @else
                                    <div class="w-full h-full flex items-center justify-center">
                                        <x-filament::icon icon="heroicon-o-photo" class="w-20 h-20 text-gray-300" />
                                    </div>
                                @endif

                                {{-- Stock Badge --}}
                                <div class="absolute top-2 right-2">
                                    @if ($product->current_stock > 0)
                                        <div class="bg-green-500 text-white px-3 py-1 rounded-full text-xs font-bold shadow-lg">
                                            {{ number_format((float) $product->current_stock, 0) }} pcs
                                        </div>
                                    @else
                                        <div class="bg-red-500 text-white px-3 py-1 rounded-full text-xs font-bold shadow-lg">
                                            Habis
                                        </div>
                                    @endif
                                </div>
                                
                                {{-- Hover Effect Overlay --}}
                                <div class="absolute inset-0 bg-gradient-to-t from-purple-600/20 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                            </div>

                            {{-- Product Info --}}
                            <div class="text-left space-y-2">
                                <h3 class="font-bold text-sm line-clamp-2 min-h-[2.5rem] text-gray-900 dark:text-gray-100 group-hover:text-purple-600 dark:group-hover:text-purple-400 transition-colors">
                                    {{ $product->name }}
                                </h3>
                                <p class="text-xs text-gray-500 dark:text-gray-400 font-mono bg-gray-100 dark:bg-gray-700 px-2 py-1 rounded">
                                    {{ $product->sku }}
                                </p>

                                {{-- Price --}}
                                <div class="pt-2 border-t border-gray-200 dark:border-gray-600">
                                    <div class="flex items-center justify-between">
                                        <span class="text-xs text-gray-500">Harga</span>
                                        <div class="text-right">
                                            <div class="text-lg font-extrabold bg-gradient-to-r from-purple-600 to-blue-600 bg-clip-text text-transparent">
                                                Rp {{ number_format((float) $product->selling_price, 0, ',', '.') }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            {{-- Add to Cart Icon --}}
                            <div class="mt-3 pt-3 border-t border-gray-200 dark:border-gray-600">
                                <div class="flex items-center justify-center gap-2 text-purple-600 dark:text-purple-400 font-semibold text-sm">
                                    <x-filament::icon icon="heroicon-o-plus-circle" class="w-5 h-5" />
                                    <span>Tambah ke Keranjang</span>
                                </div>
                            </div>
                        </button>
                    @empty
                        <div class="col-span-full">
                            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg border-2 border-dashed border-gray-300 dark:border-gray-600 p-12">
                                <div class="text-center">
                                    <div class="mx-auto w-24 h-24 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mb-4">
                                        <x-filament::icon icon="heroicon-o-magnifying-glass"
                                            class="w-12 h-12 text-gray-400" />
                                    </div>
                                    <h3 class="text-lg font-bold text-gray-600 dark:text-gray-400 mb-2">Produk Tidak Ditemukan</h3>
                                    <p class="text-sm text-gray-500 dark:text-gray-500">
                                        @if($searchProduct)
                                            Tidak ada produk yang cocok dengan pencarian "{{ $searchProduct }}"
                                        @else
                                            Tidak ada produk yang tersedia saat ini
                                        @endif
                                    </p>
                                        class="w-16 h-16 mx-auto text-gray-400 mb-4" />
                                    <p class="text-gray-500 dark:text-gray-400">Tidak ada produk ditemukan</p>
                                    @if ($searchProduct)
                                        <p class="text-sm text-gray-400 mt-2">Coba kata kunci lain</p>
                                    @endif
                                </div>
                            </x-filament::section>
                        </div>
                    @endforelse
                </div>
            </div>

            {{-- RIGHT: Cart & Checkout (1/3) --}}
            <div class="lg:col-span-1 space-y-4">

                {{-- Customer --}}
                <x-filament::section class="border-2 border-primary-300 dark:border-primary-700 shadow-md">
                    <x-slot name="heading">
                        <div class="flex items-center gap-2">
                            <div class="p-1.5 bg-primary-500 rounded-lg">
                                <x-filament::icon icon="heroicon-o-user-circle" class="w-4 h-4 text-white" />
                            </div>
                            <span class="font-extrabold text-gray-900 dark:text-gray-100">Customer</span>
                        </div>
                    </x-slot>

                    <x-filament::input.wrapper>
                        <x-filament::input.select wire:model.live="selectedCustomerId"
                            wire:change="selectCustomer($event.target.value)" class="text-base font-medium">
                            <option value="">🚶 Walk-in Customer (Umum)</option>
                            @foreach (\App\Models\Customer::where('is_active', true)->orderBy('name')->get() as $customer)
                                <option value="{{ $customer->id }}">
                                    {{ $customer->name }}
                                    @if ($customer->tier)
                                        - {{ $customer->tier->name }}
                                    @endif
                                </option>
                            @endforeach
                        </x-filament::input.select>
                    </x-filament::input.wrapper>

                    @if ($selectedCustomer)
                        <div
                            class="mt-4 p-4 bg-gradient-to-br from-primary-50 via-primary-100 to-blue-50 dark:from-primary-900/30 dark:to-primary-800/20 rounded-xl border-2 border-primary-300 dark:border-primary-600 shadow-sm">
                            <div class="flex items-start justify-between">
                                <div>
                                    <p class="font-extrabold text-lg text-gray-900 dark:text-gray-100">
                                        {{ $selectedCustomer['name'] }}</p>
                                    <div class="flex items-center gap-2 mt-2">
                                        <x-filament::badge color="primary" size="md">
                                            <x-filament::icon icon="heroicon-o-star" class="w-3 h-3 mr-1" />
                                            {{ $selectedCustomer['tier'] ?? 'No Tier' }}
                                        </x-filament::badge>
                                        <span
                                            class="text-sm font-bold text-primary-700 dark:text-primary-300 bg-white/50 dark:bg-gray-800/50 px-2 py-1 rounded-lg">
                                            🎁 {{ number_format((int) ($selectedCustomer['points'] ?? 0), 0) }} Poin
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                </x-filament::section>

                {{-- Cart --}}
                <x-filament::section class="border-2 border-gray-300 dark:border-gray-600 shadow-md">
                    <x-slot name="heading">
                        <div class="flex items-center justify-between w-full">
                            <div class="flex items-center gap-2">
                                <div class="p-1.5 bg-primary-500 rounded-lg">
                                    <x-filament::icon icon="heroicon-o-shopping-cart" class="w-4 h-4 text-white" />
                                </div>
                                <span class="font-extrabold text-gray-900 dark:text-gray-100">Keranjang Belanja</span>
                            </div>
                            @if (!empty($cart))
                                <x-filament::badge color="success" size="lg" class="animate-pulse">
                                    {{ count($cart) }} Item
                                </x-filament::badge>
                            @endif
                        </div>
                    </x-slot>

                    <div class="space-y-3 max-h-[400px] overflow-y-auto pr-1 custom-scrollbar">
                        @forelse($cart as $index => $item)
                            <div
                                class="p-4 rounded-xl bg-gradient-to-br from-white to-gray-50 dark:from-gray-800 dark:to-gray-900 border-2 border-gray-200 dark:border-gray-700 hover:border-primary-400 dark:hover:border-primary-600 transition-all shadow-sm hover:shadow-lg">
                                <div class="flex items-start justify-between mb-3">
                                    <div class="flex-1 min-w-0 pr-2">
                                        <h4 class="font-bold text-sm text-gray-900 dark:text-gray-100 leading-tight">
                                            {{ $item['name'] }}</h4>
                                        <p class="text-xs text-primary-600 dark:text-primary-400 mt-1 font-semibold">
                                            Rp {{ number_format($item['price'], 0, ',', '.') }} / pcs
                                        </p>
                                    </div>
                                    <x-filament::icon-button icon="heroicon-o-x-circle" color="danger" size="sm"
                                        wire:click="removeFromCart({{ $index }})" tooltip="Hapus item" />
                                </div>

                                <div class="flex items-center gap-2 bg-gray-100 dark:bg-gray-900/70 p-2.5 rounded-lg">
                                    <x-filament::button icon="heroicon-o-minus-circle" color="danger" size="sm"
                                        wire:click="updateQuantity({{ $index }}, {{ $item['quantity'] - 1 }})" />

                                    <x-filament::input.wrapper class="flex-none w-20">
                                        <x-filament::input type="number"
                                            wire:model.blur="cart.{{ $index }}.quantity"
                                            wire:change="updateQuantity({{ $index }}, $event.target.value)"
                                            min="1" max="{{ $item['stock'] }}"
                                            class="text-center font-bold text-base" />
                                    </x-filament::input.wrapper>

                                    <x-filament::button icon="heroicon-o-plus-circle" color="success" size="sm"
                                        wire:click="updateQuantity({{ $index }}, {{ $item['quantity'] + 1 }})"
                                        :disabled="$item['quantity'] >= $item['stock']" />

                                    <div class="ml-auto text-right">
                                        <p class="text-xs text-gray-500 dark:text-gray-400 font-medium">Subtotal</p>
                                        <p class="font-extrabold text-base text-primary-600 dark:text-primary-400">
                                            Rp {{ number_format($item['price'] * $item['quantity'], 0, ',', '.') }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div
                                class="text-center py-16 bg-gradient-to-br from-gray-50 to-gray-100 dark:from-gray-800/50 dark:to-gray-900/50 rounded-xl border-2 border-dashed border-gray-300 dark:border-gray-700">
                                <div
                                    class="inline-block p-6 bg-gradient-to-br from-gray-100 to-gray-200 dark:from-gray-700 dark:to-gray-800 rounded-full mb-4 shadow-lg">
                                    <x-filament::icon icon="heroicon-o-shopping-cart"
                                        class="w-16 h-16 text-gray-400" />
                                </div>
                                <p class="text-lg font-bold text-gray-600 dark:text-gray-400">Keranjang Masih
                                    Kosong</p>
                                <p class="text-sm text-gray-500 dark:text-gray-500 mt-2">Pilih produk dari katalog
                                    untuk memulai transaksi
                                </p>
                            </div>
                        @endforelse
                    </div>
                </x-filament::section>

                {{-- Summary --}}
                <x-filament::section
                    class="bg-gradient-to-br from-gray-50 via-gray-100 to-gray-50 dark:from-gray-800 dark:via-gray-900 dark:to-gray-800 border-2 border-gray-300 dark:border-gray-600 shadow-lg">
                    <x-slot name="heading">
                        <div class="flex items-center gap-2">
                            <div class="p-1.5 bg-primary-500 rounded-lg">
                                <x-filament::icon icon="heroicon-o-calculator" class="w-4 h-4 text-white" />
                            </div>
                            <span class="font-extrabold text-gray-900 dark:text-gray-100">Ringkasan Pembayaran</span>
                        </div>
                    </x-slot>

                    <div class="space-y-3">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600 dark:text-gray-400 font-medium">Subtotal</span>
                            <span class="font-bold text-gray-900 dark:text-gray-100">Rp
                                {{ number_format($subtotal, 0, ',', '.') }}</span>
                        </div>

                        @if ($discountAmount > 0)
                            <div
                                class="flex justify-between text-sm bg-gradient-to-r from-success-50 to-success-100 dark:from-success-900/20 dark:to-success-800/20 p-3 rounded-lg border border-success-300 dark:border-success-700 shadow-sm">
                                <div class="flex items-center gap-2">
                                    <x-filament::icon icon="heroicon-o-tag" class="w-5 h-5 text-success-600" />
                                    <div>
                                        <span class="text-success-700 dark:text-success-300 font-bold">Diskon</span>
                                        @if ($discountName)
                                            <br><span
                                                class="text-xs text-success-600 dark:text-success-400 font-semibold">{{ $discountName }}</span>
                                        @endif
                                    </div>
                                </div>
                                <span class="font-extrabold text-success-600 dark:text-success-400">- Rp
                                    {{ number_format($discountAmount, 0, ',', '.') }}</span>
                            </div>
                        @endif

                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600 dark:text-gray-400 font-medium">PPN
                                ({{ $taxPercentage }}%)</span>
                            <span class="font-bold text-gray-900 dark:text-gray-100">Rp
                                {{ number_format($taxAmount, 0, ',', '.') }}</span>
                        </div>

                        <div
                            class="flex justify-between text-2xl font-extrabold pt-4 border-t-2 border-gray-400 dark:border-gray-500">
                            <span class="text-gray-800 dark:text-gray-200">TOTAL</span>
                            <span class="text-primary-600 dark:text-primary-400">Rp
                                {{ number_format($total, 0, ',', '.') }}</span>
                        </div>
                    </div>
                </x-filament::section>

                {{-- Actions --}}
                <div class="space-y-3">
                    <x-filament::button x-on:click="openPayment()" color="success" size="xl"
                        icon="heroicon-o-credit-card"
                        class="w-full justify-center text-lg font-bold shadow-lg hover:shadow-xl transition-all duration-200 transform hover:scale-[1.02]"
                        :disabled="empty($cart)">
                        <span class="flex items-center gap-3">
                            <span class="text-2xl">💳</span>
                            <span>BAYAR SEKARANG</span>
                            <span class="text-xs opacity-75 bg-white/20 px-2 py-1 rounded">(F4)</span>
                        </span>
                    </x-filament::button>

                    <x-filament::button wire:click="resetCart" color="danger" outlined icon="heroicon-o-trash"
                        size="lg"
                        class="w-full justify-center hover:bg-red-50 dark:hover:bg-red-900/20 transition-all"
                        :disabled="empty($cart)">
                        <span class="flex items-center gap-2">
                            <span>🗑️</span>
                            <span>Kosongkan Keranjang</span>
                            <span class="text-xs opacity-75">(ESC)</span>
                        </span>
                    </x-filament::button>
                </div>
            </div>
        </div>

        {{-- Payment Modal --}}
        <div x-show="showPayment" x-cloak class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
            <div class="flex items-center justify-center min-h-screen px-4">
                <div x-show="showPayment" x-transition:enter="ease-out duration-300"
                    x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                    x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100"
                    x-transition:leave-end="opacity-0" @click="showPayment = false"
                    class="fixed inset-0 bg-gray-500 dark:bg-gray-900 bg-opacity-75 dark:bg-opacity-75 transition-opacity">
                </div>

                <div x-show="showPayment" x-transition:enter="ease-out duration-300"
                    x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                    x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 scale-100"
                    x-transition:leave-end="opacity-0 scale-95"
                    class="relative bg-white dark:bg-gray-800 rounded-xl shadow-xl max-w-2xl w-full p-6 z-10">

                    {{-- Header --}}
                    <div class="flex items-center justify-between mb-6">
                        <div class="flex items-center gap-2">
                            <x-filament::icon icon="heroicon-o-credit-card" class="w-6 h-6 text-primary-500" />
                            <h3 class="text-xl font-bold">Pembayaran</h3>
                        </div>
                        <button @click="showPayment = false"
                            class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                            <x-filament::icon icon="heroicon-o-x-mark" class="w-6 h-6" />
                        </button>
                    </div>

                    {{-- Content --}}
                    <div class="space-y-6">
                        {{-- Total Display --}}
                        <div
                            class="bg-gradient-to-br from-primary-50 to-primary-100 dark:from-primary-900/30 dark:to-primary-800/20 p-6 rounded-xl border-2 border-primary-200 dark:border-primary-700 text-center shadow-lg">
                            <p class="text-sm font-medium text-primary-700 dark:text-primary-300 mb-2">Total Pembayaran
                            </p>
                            <p class="text-4xl font-extrabold text-primary-600 dark:text-primary-400 tracking-tight">
                                Rp {{ number_format($total, 0, ',', '.') }}
                            </p>
                        </div>

                        {{-- Payment Method --}}
                        <div class="space-y-2">
                            <label for="paymentMethod"
                                class="block text-sm font-semibold text-gray-700 dark:text-gray-300">
                                Metode Pembayaran
                            </label>
                            <x-filament::input.wrapper>
                                <x-filament::input.select wire:model.live="paymentMethod" id="paymentMethod"
                                    class="text-base">
                                    <option value="cash">💵 Tunai (Cash)</option>
                                    <option value="debit_card">💳 Kartu Debit</option>
                                    <option value="credit_card">💳 Kartu Kredit</option>
                                    <option value="bank_transfer">🏦 Transfer Bank</option>
                                    <option value="e_wallet">📱 E-Wallet (GoPay, OVO, Dana)</option>
                                    <option value="qris">📲 QRIS</option>
                                </x-filament::input.select>
                            </x-filament::input.wrapper>
                        </div>

                        {{-- Paid Amount --}}
                        <div class="space-y-2">
                            <label for="paidAmount"
                                class="block text-sm font-semibold text-gray-700 dark:text-gray-300">
                                Jumlah Bayar
                            </label>
                            <x-filament::input.wrapper>
                                <x-filament::input type="number" id="paidAmount" x-ref="paidAmountInput"
                                    wire:model.live="paidAmount" step="1000" min="{{ $total }}"
                                    placeholder="Masukkan jumlah bayar" class="text-lg font-semibold" />
                            </x-filament::input.wrapper>
                            <div class="flex gap-2 flex-wrap mt-3">
                                <x-filament::button wire:click="$set('paidAmount', {{ $total }})"
                                    size="sm" color="gray" outlined>
                                    Pas
                                </x-filament::button>
                                <x-filament::button
                                    wire:click="$set('paidAmount', {{ ceil($total / 50000) * 50000 }})"
                                    size="sm" color="gray" outlined>
                                    {{ number_format(ceil($total / 50000) * 50000, 0) }}
                                </x-filament::button>
                                <x-filament::button
                                    wire:click="$set('paidAmount', {{ ceil($total / 100000) * 100000 }})"
                                    size="sm" color="gray" outlined>
                                    {{ number_format(ceil($total / 100000) * 100000, 0) }}
                                </x-filament::button>
                            </div>
                        </div>

                        {{-- Change Amount --}}
                        @if ($changeAmount > 0)
                            <div
                                class="bg-gradient-to-br from-success-50 to-success-100 dark:from-success-900/30 dark:to-success-800/20 p-5 rounded-xl border-2 border-success-200 dark:border-success-700 text-center shadow-md">
                                <p class="text-sm font-medium text-success-700 dark:text-success-300 mb-2">💰 Kembalian
                                </p>
                                <p
                                    class="text-3xl font-extrabold text-success-600 dark:text-success-400 tracking-tight">
                                    Rp {{ number_format($changeAmount, 0, ',', '.') }}
                                </p>
                            </div>
                        @elseif ($paidAmount > 0 && $paidAmount < $total)
                            <div
                                class="bg-danger-50 dark:bg-danger-900/20 p-4 rounded-lg border border-danger-200 dark:border-danger-700 text-center">
                                <p class="text-sm text-danger-600 dark:text-danger-400 font-medium">
                                    ⚠️ Jumlah bayar kurang Rp {{ number_format($total - $paidAmount, 0, ',', '.') }}
                                </p>
                            </div>
                        @endif
                    </div>

                    {{-- Footer Actions --}}
                    <div class="flex gap-3 mt-6 pt-6 border-t dark:border-gray-700">
                        <x-filament::button x-on:click="closePayment()" color="gray" outlined class="flex-1"
                            size="lg">
                            <x-filament::icon icon="heroicon-o-x-mark" class="w-5 h-5 mr-1" />
                            Batal
                        </x-filament::button>

                        <x-filament::button x-on:click="processPaymentNow()" color="success" class="flex-1"
                            size="lg" x-bind:disabled="parseFloat($wire.paidAmount) < {{ $total }}">
                            <x-filament::icon icon="heroicon-o-check-circle" class="w-5 h-5 mr-1" />
                            <span class="font-bold">PROSES PEMBAYARAN</span>
                        </x-filament::button>
                    </div>
                </div>
            </div>
        </div>

        {{-- Receipt Modal --}}
        <div x-show="showReceipt" x-cloak class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
            <div class="flex items-center justify-center min-h-screen px-4">
                <div x-show="showReceipt" x-transition:enter="ease-out duration-300"
                    x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                    x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100"
                    x-transition:leave-end="opacity-0" @click="showReceipt = false"
                    class="fixed inset-0 bg-gray-500 dark:bg-gray-900 bg-opacity-75 dark:bg-opacity-75 transition-opacity">
                </div>

                <div x-show="showReceipt" x-transition:enter="ease-out duration-300"
                    x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                    x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 scale-100"
                    x-transition:leave-end="opacity-0 scale-95"
                    class="relative bg-white dark:bg-gray-800 rounded-xl shadow-xl max-w-lg w-full p-6 z-10">

                    {{-- Header --}}
                    <div class="flex items-start gap-3 mb-6">
                        <div class="p-2 bg-success-100 dark:bg-success-900/30 rounded-full">
                            <x-filament::icon icon="heroicon-o-check-circle" class="w-8 h-8 text-success-600" />
                        </div>
                        <div class="flex-1">
                            <h3 class="text-xl font-bold text-success-600 dark:text-success-400">Transaksi Berhasil!
                            </h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Pembayaran telah diproses</p>
                        </div>
                        <button @click="showReceipt = false"
                            class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                            <x-filament::icon icon="heroicon-o-x-mark" class="w-6 h-6" />
                        </button>
                    </div>

                    @if ($lastOrderId)
                        @php $order = \App\Models\SalesOrder::with('items.product', 'customer')->find($lastOrderId); @endphp

                        @if ($order)
                            <div
                                class="receipt-content bg-white dark:bg-gray-800 rounded-lg shadow-sm border dark:border-gray-700">
                                <div class="p-6">
                                    {{-- Header --}}
                                    <div
                                        class="text-center mb-6 pb-4 border-b-2 border-dashed border-gray-300 dark:border-gray-600">
                                        <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-200 mb-1">
                                            {{ config('app.name', 'Bakery ERP') }}</h2>
                                        <p class="text-sm text-gray-600 dark:text-gray-400">STRUK PEMBELIAN</p>
                                        <p class="text-xs text-gray-500 dark:text-gray-500 mt-2">
                                            {{ now()->format('d F Y, H:i:s') }}</p>
                                    </div>

                                    {{-- Order Info --}}
                                    <div class="mb-5 space-y-1">
                                        <div class="flex justify-between text-sm">
                                            <span class="text-gray-600 dark:text-gray-400">No. Transaksi:</span>
                                            <span
                                                class="font-bold text-gray-900 dark:text-gray-100">{{ $order->order_number }}</span>
                                        </div>
                                        @if ($order->customer)
                                            <div class="flex justify-between text-sm">
                                                <span class="text-gray-600 dark:text-gray-400">Customer:</span>
                                                <span
                                                    class="font-semibold text-gray-900 dark:text-gray-100">{{ $order->customer->name }}</span>
                                            </div>
                                        @endif
                                        <div class="flex justify-between text-sm">
                                            <span class="text-gray-600 dark:text-gray-400">Kasir:</span>
                                            <span
                                                class="font-semibold text-gray-900 dark:text-gray-100">{{ auth()->user()->name }}</span>
                                        </div>
                                    </div>

                                    {{-- Items Table --}}
                                    <div class="mb-5">
                                        <table class="w-full">
                                            <thead class="border-b-2 border-gray-300 dark:border-gray-600">
                                                <tr class="text-xs text-gray-600 dark:text-gray-400">
                                                    <th class="text-left pb-2">Item</th>
                                                    <th class="text-center pb-2">Qty</th>
                                                    <th class="text-right pb-2">Harga</th>
                                                    <th class="text-right pb-2">Total</th>
                                                </tr>
                                            </thead>
                                            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                                @foreach ($order->items as $item)
                                                    <tr class="text-sm">
                                                        <td class="py-3 text-gray-800 dark:text-gray-200">
                                                            {{ $item->product->name }}
                                                        </td>
                                                        <td class="py-3 text-center text-gray-700 dark:text-gray-300">
                                                            {{ $item->quantity }}
                                                        </td>
                                                        <td class="py-3 text-right text-gray-700 dark:text-gray-300">
                                                            {{ number_format((float) ($item->unit_price ?? 0), 0) }}
                                                        </td>
                                                        <td
                                                            class="py-3 text-right font-semibold text-gray-900 dark:text-gray-100">
                                                            {{ number_format((float) ($item->total ?? 0), 0) }}
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>

                                    {{-- Summary --}}
                                    <div
                                        class="border-t-2 border-dashed border-gray-300 dark:border-gray-600 pt-4 space-y-2">
                                        <div class="flex justify-between text-sm">
                                            <span class="text-gray-600 dark:text-gray-400">Subtotal</span>
                                            <span class="font-semibold text-gray-900 dark:text-gray-100">Rp
                                                {{ number_format((float) ($order->subtotal ?? 0), 0) }}</span>
                                        </div>
                                        @if ($order->discount_amount > 0)
                                            <div class="flex justify-between text-sm">
                                                <span class="text-success-600 dark:text-success-400">Diskon</span>
                                                <span class="font-semibold text-success-600 dark:text-success-400">- Rp
                                                    {{ number_format((float) ($order->discount_amount ?? 0), 0) }}</span>
                                            </div>
                                        @endif
                                        <div class="flex justify-between text-sm">
                                            <span class="text-gray-600 dark:text-gray-400">PPN
                                                ({{ $order->tax_percentage }}%)</span>
                                            <span class="font-semibold text-gray-900 dark:text-gray-100">Rp
                                                {{ number_format((float) ($order->tax_amount ?? 0), 0) }}</span>
                                        </div>
                                        <div
                                            class="flex justify-between text-lg font-bold pt-2 border-t border-gray-300 dark:border-gray-600">
                                            <span class="text-gray-800 dark:text-gray-200">TOTAL</span>
                                            <span class="text-primary-600 dark:text-primary-400">Rp
                                                {{ number_format((float) ($order->total ?? 0), 0) }}</span>
                                        </div>
                                        <div
                                            class="flex justify-between text-sm bg-gray-50 dark:bg-gray-700/50 p-3 rounded-lg mt-3">
                                            <span class="text-gray-600 dark:text-gray-400">Bayar
                                                ({{ ucfirst(str_replace('_', ' ', $order->payment_method)) }})</span>
                                            <span class="font-bold text-gray-900 dark:text-gray-100">Rp
                                                {{ number_format((float) ($order->paid_amount ?? 0), 0) }}</span>
                                        </div>
                                        <div
                                            class="flex justify-between text-sm bg-success-50 dark:bg-success-900/20 p-3 rounded-lg">
                                            <span class="text-success-700 dark:text-success-300">Kembalian</span>
                                            <span class="font-bold text-success-600 dark:text-success-400">Rp
                                                {{ number_format((float) ($order->change_amount ?? 0), 0) }}</span>
                                        </div>
                                    </div>

                                    {{-- Footer --}}
                                    <div
                                        class="text-center mt-6 pt-4 border-t-2 border-dashed border-gray-300 dark:border-gray-600">
                                        <p class="text-base font-bold text-gray-800 dark:text-gray-200">Terima Kasih
                                            Atas Kunjungan Anda! 🙏</p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">Barang yang sudah
                                            dibeli tidak dapat dikembalikan</p>
                                        <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">www.bakeryerp.com</p>
                                    </div>
                                </div>
                            </div>
                        @endif
                    @endif

                    {{-- Footer Actions --}}
                    <div class="flex gap-3 mt-6 pt-6 border-t dark:border-gray-700">
                        <x-filament::button onclick="window.print()" color="primary" icon="heroicon-o-printer"
                            class="flex-1" size="lg">
                            <span class="flex items-center gap-2">
                                <span class="text-xl">🖨️</span>
                                <span class="font-semibold">Print Struk</span>
                            </span>
                        </x-filament::button>

                        <x-filament::button x-on:click="showReceipt = false; $wire.finishTransaction()"
                            color="success" icon="heroicon-o-plus-circle" class="flex-1" size="lg">
                            <span class="flex items-center gap-2">
                                <span class="text-xl">✨</span>
                                <span class="font-semibold">Transaksi Baru</span>
                            </span>
                        </x-filament::button>
                    </div>
                </div>
            </div>
        </div>

        {{-- Custom Styles --}}
        <style>
            /* Custom scrollbar untuk cart */
            .custom-scrollbar::-webkit-scrollbar {
                width: 10px;
            }

            .custom-scrollbar::-webkit-scrollbar-track {
                background: rgba(0, 0, 0, 0.05);
                border-radius: 10px;
                margin: 8px 0;
            }

            .custom-scrollbar::-webkit-scrollbar-thumb {
                background: linear-gradient(180deg, rgba(99, 102, 241, 0.6), rgba(59, 130, 246, 0.6));
                border-radius: 10px;
                border: 2px solid rgba(255, 255, 255, 0.2);
            }

            .custom-scrollbar::-webkit-scrollbar-thumb:hover {
                background: linear-gradient(180deg, rgba(99, 102, 241, 0.9), rgba(59, 130, 246, 0.9));
            }

            /* Smooth animations */
            @keyframes fadeIn {
                from {
                    opacity: 0;
                    transform: translateY(-10px);
                }

                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }

            @keyframes slideIn {
                from {
                    opacity: 0;
                    transform: translateX(-20px);
                }

                to {
                    opacity: 1;
                    transform: translateX(0);
                }
            }

            /* Apply animations */
            .fi-section {
                animation: fadeIn 0.3s ease-out;
            }

            /* Print styles untuk thermal receipt */
            @media print {
                body * {
                    visibility: hidden;
                }

                .receipt-content,
                .receipt-content * {
                    visibility: visible;
                }

                .receipt-content {
                    position: absolute;
                    left: 0;
                    top: 0;
                    width: 80mm;
                    background: white;
                    color: black;
                }

                /* Hide unnecessary elements when printing */
                button,
                .fi-modal-footer,
                [x-ref],
                [wire\\:click] {
                    display: none !important;
                }
            }

            /* Hover effects with smooth transitions */
            .hover\:scale-105:hover {
                transform: scale(1.05);
                transition: transform 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            }

            .hover\:scale-110:hover {
                transform: scale(1.10);
                transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            }

            .hover\:scale-\[1\.02\]:hover {
                transform: scale(1.02);
                transition: transform 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            }

            /* Pulse animation for badge */
            @keyframes pulse {

                0%,
                100% {
                    opacity: 1;
                }

                50% {
                    opacity: 0.8;
                }
            }

            .animate-pulse {
                animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
            }

            /* Smooth transitions globally */
            * {
                transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
            }

            /* Alpine x-cloak */
            [x-cloak] {
                display: none !important;
            }

            /* Input focus styles */
            input:focus,
            select:focus {
                border-color: rgb(99, 102, 241) !important;
                box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1) !important;
            }

            /* Button active states */
            button:active {
                transform: scale(0.98);
            }

            /* Loading spinner */
            .wire-loading {
                opacity: 0.6;
                pointer-events: none;
            }
        </style>
    </div>
</x-filament-panels::page>
