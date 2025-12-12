<div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100">
    {{-- Product Grid & Cart Container --}}
    <div class="flex h-screen overflow-hidden">
        {{-- LEFT: Product Section --}}
        <div class="flex-1 flex flex-col bg-white border-r-2 border-gray-200 shadow-lg">
            {{-- Search & Filter Bar --}}
            <div class="p-4 border-b-2 border-gray-200 bg-gradient-to-r from-blue-50 to-indigo-50">
                <div class="flex gap-4">
                    <div class="flex-1 relative">
                        <svg class="absolute left-4 top-1/2 transform -translate-y-1/2 w-5 h-5 text-gray-400"
                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                        <input wire:model.live.debounce.300ms="searchProduct" type="text"
                            placeholder="🔍 Cari produk (nama, SKU, barcode)..."
                            class="w-full pl-12 pr-4 py-3 text-base border-2 border-gray-300 rounded-xl focus:ring-4 focus:ring-blue-500 focus:border-blue-500 transition-all shadow-sm" />
                    </div>
                    {{-- Barcode Input (Hidden, triggered by scanner) --}}
                    <input type="text" id="barcodeInput" wire:keydown.enter="addByBarcode($event.target.value)"
                        class="sr-only" autofocus />
                </div>
            </div>

            {{-- Active Discounts / Promo Section --}}
            @if (!empty($activeDiscounts) || !empty($birthdayCustomers))
                <div
                    class="px-4 py-3 bg-gradient-to-r from-pink-50 via-orange-50 to-yellow-50 border-b-2 border-orange-200">
                    <div class="flex items-center gap-2 mb-2">
                        <span class="text-lg">🎉</span>
                        <h3 class="font-bold text-orange-800 text-sm uppercase tracking-wide">Promo & Diskon Hari Ini
                        </h3>
                    </div>
                    <div class="flex flex-wrap gap-2">
                        {{-- Display active discounts --}}
                        @foreach ($activeDiscounts as $discount)
                            @if ($discount['is_applicable_today'])
                                <div class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-medium shadow-sm
                                    @if ($discount['is_birthday']) bg-pink-100 text-pink-700 border border-pink-200
                                    @elseif($discount['discount_type'] === 'day_specific') bg-blue-100 text-blue-700 border border-blue-200
                                    @elseif($discount['discount_type'] === 'first_purchase') bg-green-100 text-green-700 border border-green-200
                                    @elseif($discount['discount_type'] === 'tier_specific') bg-purple-100 text-purple-700 border border-purple-200
                                    @elseif($discount['discount_type'] === 'product_specific') bg-amber-100 text-amber-700 border border-amber-200
                                    @else bg-orange-100 text-orange-700 border border-orange-200 @endif"
                                    title="{{ $discount['description'] ?? $discount['name'] }}">
                                    @if ($discount['discount_label'])
                                        <span>{{ $discount['discount_label'] }}</span>
                                    @else
                                        <span>🏷️ {{ $discount['name'] }}</span>
                                    @endif
                                    @if ($discount['discount_value'])
                                        <span class="font-bold">{{ $discount['discount_value'] }}</span>
                                    @endif
                                    @if ($discount['min_subtotal'])
                                        <span class="text-[10px] opacity-75">(min Rp
                                            {{ number_format($discount['min_subtotal'], 0, ',', '.') }})</span>
                                    @endif
                                </div>
                            @endif
                        @endforeach

                        {{-- Birthday customers today --}}
                        @if (!empty($birthdayCustomers))
                            <div
                                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-medium bg-gradient-to-r from-pink-200 to-rose-200 text-pink-800 border border-pink-300 shadow-sm animate-pulse">
                                <span>🎂</span>
                                <span>{{ count($birthdayCustomers) }} Customer Ulang Tahun Hari Ini!</span>
                            </div>
                        @endif
                    </div>

                    {{-- List birthday customers if any --}}
                    @if (!empty($birthdayCustomers))
                        <div class="mt-2 flex flex-wrap gap-1">
                            @foreach ($birthdayCustomers as $bCustomer)
                                <button wire:click="selectCustomer({{ $bCustomer['id'] }})"
                                    class="inline-flex items-center gap-1 px-2 py-1 text-xs bg-white rounded-lg border border-pink-300 hover:bg-pink-50 hover:border-pink-400 transition-all shadow-sm">
                                    <span>🎂</span>
                                    <span class="font-medium text-pink-700">{{ $bCustomer['name'] }}</span>
                                    <span class="text-pink-500 text-[10px]">({{ $bCustomer['tier_name'] }})</span>
                                </button>
                            @endforeach
                        </div>
                    @endif
                </div>
            @endif

            {{-- Stock Warnings --}}
            @if (!empty($stockWarnings))
                <div class="px-4 py-2 bg-yellow-50 border-b border-yellow-200">
                    @foreach ($stockWarnings as $warning)
                        <p class="text-sm text-yellow-700 flex items-center gap-2">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                                    clip-rule="evenodd" />
                            </svg>
                            {{ $warning }}
                        </p>
                    @endforeach
                </div>
            @endif

            {{-- Stock Errors --}}
            @if (!empty($stockErrors))
                <div class="px-4 py-2 bg-red-50 border-b border-red-200">
                    @foreach ($stockErrors as $error)
                        <p class="text-sm text-red-700 flex items-center gap-2">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                    clip-rule="evenodd" />
                            </svg>
                            {{ $error }}
                        </p>
                    @endforeach
                </div>
            @endif

            {{-- Product Grid --}}
            <div class="flex-1 overflow-y-auto p-4 bg-gray-50">
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 2xl:grid-cols-6 gap-3">
                    @forelse($products as $product)
                        @php
                            $hasDiscount =
                                isset($productDiscounts[$product->id]) && !empty($productDiscounts[$product->id]);
                            $productDiscountInfo = $hasDiscount ? $productDiscounts[$product->id][0] : null;
                        @endphp
                        <button wire:click="addToCart({{ $product->id }})"
                            class="group bg-white border-2 rounded-xl p-3 hover:shadow-lg transition-all duration-200 text-left transform hover:-translate-y-0.5
                                {{ $product->current_stock <= 0 ? 'opacity-50 cursor-not-allowed border-gray-200' : '' }}
                                {{ $hasDiscount && $product->current_stock > 0 ? 'border-orange-300 hover:border-orange-500 ring-2 ring-orange-100' : 'border-gray-200 hover:border-blue-500' }}"
                            {{ $product->current_stock <= 0 ? 'disabled' : '' }}>
                            <div
                                class="aspect-square rounded-lg mb-2 flex items-center justify-center transition-all relative
                                    {{ $hasDiscount ? 'bg-gradient-to-br from-orange-50 to-amber-50 group-hover:from-orange-100 group-hover:to-amber-100' : 'bg-gradient-to-br from-blue-50 to-indigo-50 group-hover:from-blue-100 group-hover:to-indigo-100' }}">
                                @if ($product->image_url)
                                    <img src="{{ $product->image_url }}" alt="{{ $product->name }}"
                                        class="w-full h-full object-cover rounded-lg">
                                @else
                                    <svg class="w-10 h-10 {{ $hasDiscount ? 'text-orange-400 group-hover:text-orange-600' : 'text-blue-400 group-hover:text-blue-600' }} transition-colors"
                                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                    </svg>
                                @endif

                                {{-- Discount Badge --}}
                                @if ($hasDiscount)
                                    <span
                                        class="absolute top-1 left-1 bg-gradient-to-r from-orange-500 to-red-500 text-white text-[10px] px-1.5 py-0.5 rounded-full font-bold shadow-md animate-pulse">
                                        🏷️ {{ $productDiscountInfo['value'] ?? 'PROMO' }}
                                    </span>
                                @endif

                                {{-- Stock badge --}}
                                @if ($product->current_stock <= 0)
                                    <span
                                        class="absolute top-1 right-1 bg-red-500 text-white text-xs px-1.5 py-0.5 rounded-full">Habis</span>
                                @elseif($product->current_stock <= $product->minimum_stock)
                                    <span
                                        class="absolute top-1 right-1 bg-yellow-500 text-white text-xs px-1.5 py-0.5 rounded-full">
                                        {{ (int) $product->current_stock }}
                                    </span>
                                @endif
                            </div>
                            <h3
                                class="font-semibold text-gray-900 mb-1 line-clamp-2 transition-colors text-xs leading-tight
                                    {{ $hasDiscount ? 'group-hover:text-orange-600' : 'group-hover:text-blue-600' }}">
                                {{ $product->name }}
                            </h3>
                            <p class="text-sm font-bold {{ $hasDiscount ? 'text-orange-600' : 'text-blue-600' }}">
                                Rp {{ number_format($product->selling_price ?? 0, 0, ',', '.') }}
                            </p>
                            <p class="text-xs text-gray-500">SKU: {{ $product->sku }}</p>

                            {{-- Discount info below product --}}
                            @if ($hasDiscount)
                                <p class="text-[10px] text-orange-600 mt-1 font-medium truncate"
                                    title="{{ $productDiscountInfo['name'] ?? '' }}">
                                    ✨ {{ $productDiscountInfo['name'] ?? 'Diskon' }}
                                </p>
                            @endif
                        </button>
                    @empty
                        <div class="col-span-full text-center py-16 text-gray-500">
                            <svg class="w-20 h-20 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4">
                                </path>
                            </svg>
                            <p class="text-lg font-semibold mb-1">Tidak ada produk ditemukan</p>
                            <p class="text-sm text-gray-400">Coba gunakan kata kunci pencarian yang berbeda</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- RIGHT: Cart & Checkout Section --}}
        <div class="w-96 xl:w-[28rem] flex flex-col bg-white shadow-2xl">
            {{-- Customer Section --}}
            <div
                class="p-4 border-b-2 border-gray-200 {{ $isCustomerBirthday ? 'bg-gradient-to-r from-pink-100 via-rose-100 to-pink-100' : 'bg-gradient-to-r from-purple-50 to-pink-50' }}">
                <label class="block text-xs font-bold text-gray-700 mb-2 uppercase tracking-wide">👤 Customer</label>
                @if ($selectedCustomer)
                    <div
                        class="bg-white rounded-lg p-3 border-2 {{ $isCustomerBirthday ? 'border-pink-400 ring-2 ring-pink-200' : 'border-purple-200' }}">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <div class="flex items-center gap-2">
                                    <p class="font-semibold text-gray-900">{{ $selectedCustomer['name'] }}</p>
                                    {{-- Birthday Badge --}}
                                    @if ($isCustomerBirthday)
                                        <span
                                            class="inline-flex items-center gap-1 px-2 py-0.5 text-xs font-bold rounded-full bg-gradient-to-r from-pink-500 to-rose-500 text-white animate-pulse shadow-md">
                                            🎂 ULTAH!
                                        </span>
                                    @endif
                                </div>
                                <p class="text-xs text-gray-500">{{ $selectedCustomer['phone'] ?? '-' }}</p>
                                <div class="flex flex-wrap items-center gap-2 mt-1">
                                    <span
                                        class="px-2 py-0.5 text-xs rounded-full bg-{{ $selectedCustomer['tier_color'] }}-100 text-{{ $selectedCustomer['tier_color'] }}-700">
                                        {{ $selectedCustomer['tier_name'] }}
                                    </span>
                                    <span class="text-xs text-purple-600 font-medium">
                                        {{ number_format($selectedCustomer['total_points'], 0) }} poin
                                    </span>
                                </div>

                                {{-- Customer Discounts Display --}}
                                @if (!empty($customerDiscounts))
                                    <div class="mt-2 flex flex-wrap gap-1">
                                        @foreach ($customerDiscounts as $discount)
                                            <span
                                                class="inline-flex items-center gap-1 px-2 py-0.5 text-[10px] font-medium rounded-full shadow-sm
                                                @if ($discount['type'] === 'birthday') bg-pink-100 text-pink-700 border border-pink-200
                                                @elseif($discount['type'] === 'tier') bg-purple-100 text-purple-700 border border-purple-200
                                                @elseif($discount['type'] === 'first_purchase') bg-green-100 text-green-700 border border-green-200
                                                @else bg-orange-100 text-orange-700 border border-orange-200 @endif">
                                                {{ $discount['label'] }}
                                                @if ($discount['value'])
                                                    <span class="font-bold">{{ $discount['value'] }}</span>
                                                @endif
                                            </span>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                            <button wire:click="removeCustomer"
                                class="text-red-500 hover:text-red-700 p-1 hover:bg-red-50 rounded-lg transition-all">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </div>

                        {{-- Birthday Special Message --}}
                        @if ($isCustomerBirthday)
                            <div
                                class="mt-2 p-2 bg-gradient-to-r from-pink-50 to-rose-50 rounded-lg border border-pink-200">
                                <p class="text-xs text-pink-700 text-center font-medium">
                                    🎉 Selamat Ulang Tahun! Nikmati diskon spesial hari ini! 🎁
                                </p>
                            </div>
                        @endif
                    </div>
                @else
                    <button wire:click="$set('showCustomerModal', true)"
                        class="w-full px-4 py-3 border-2 border-dashed border-gray-300 rounded-xl text-gray-500 hover:border-purple-400 hover:text-purple-600 hover:bg-purple-50 transition-all flex items-center justify-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                        </svg>
                        Pilih Customer (Opsional)
                    </button>

                    {{-- Birthday customers reminder --}}
                    @if (!empty($birthdayCustomers))
                        <div class="mt-2 p-2 bg-pink-50 rounded-lg border border-pink-200">
                            <p class="text-xs text-pink-700 font-medium mb-1">🎂 Customer yang berulang tahun hari ini:
                            </p>
                            <div class="flex flex-wrap gap-1">
                                @foreach ($birthdayCustomers as $bCustomer)
                                    <button wire:click="selectCustomer({{ $bCustomer['id'] }})"
                                        class="text-xs px-2 py-0.5 bg-white rounded-full border border-pink-300 text-pink-600 hover:bg-pink-100 transition-all">
                                        {{ $bCustomer['name'] }}
                                    </button>
                                @endforeach
                            </div>
                        </div>
                    @endif
                @endif
            </div>

            {{-- Cart Items --}}
            <div class="flex-1 overflow-y-auto p-4 space-y-2 bg-gray-50">
                @forelse($cart as $index => $item)
                    <div
                        class="bg-white rounded-xl p-3 border border-gray-200 shadow-sm hover:shadow-md transition-shadow">
                        <div class="flex justify-between items-start mb-2">
                            <div class="flex-1 pr-2">
                                <h4 class="font-semibold text-gray-900 text-sm leading-tight">{{ $item['name'] }}</h4>
                                <p class="text-xs text-gray-500">{{ $item['sku'] ?? '' }}</p>
                            </div>
                            <button wire:click="removeFromCart({{ $index }})"
                                class="text-red-500 hover:text-red-700 hover:bg-red-50 p-1.5 rounded-lg transition-all"
                                title="Hapus item">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                    </path>
                                </svg>
                            </button>
                        </div>
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-1">
                                <button wire:click="updateQuantity({{ $index }}, {{ $item['quantity'] - 1 }})"
                                    class="w-7 h-7 flex items-center justify-center bg-gray-100 hover:bg-gray-200 rounded-lg font-bold text-gray-700 transition-all text-sm">−</button>
                                <input type="number"
                                    wire:change="updateQuantity({{ $index }}, $event.target.value)"
                                    value="{{ $item['quantity'] }}"
                                    class="w-12 text-center border border-gray-300 rounded-lg py-1 text-sm font-semibold text-gray-900 focus:ring-2 focus:ring-blue-500"
                                    min="1" />
                                <button wire:click="updateQuantity({{ $index }}, {{ $item['quantity'] + 1 }})"
                                    class="w-7 h-7 flex items-center justify-center bg-blue-500 hover:bg-blue-600 text-white rounded-lg font-bold transition-all text-sm">+</button>
                            </div>
                            <div class="text-right">
                                <p class="text-xs text-gray-500">@ Rp {{ number_format($item['price'], 0, ',', '.') }}
                                </p>
                                <p class="text-sm font-bold text-gray-900">Rp
                                    {{ number_format($item['price'] * $item['quantity'], 0, ',', '.') }}</p>
                            </div>
                        </div>
                        @if ($item['current_stock'] <= 5)
                            <p class="text-xs text-orange-600 mt-1">⚠️ Stok tersisa:
                                {{ number_format($item['current_stock'], 0) }}</p>
                        @endif
                    </div>
                @empty
                    <div class="text-center py-12 text-gray-400">
                        <svg class="w-16 h-16 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z">
                            </path>
                        </svg>
                        <p class="text-base font-semibold mb-1">Keranjang Kosong</p>
                        <p class="text-xs text-gray-400">Pilih produk untuk memulai transaksi</p>
                    </div>
                @endforelse
            </div>

            {{-- Points Section (if customer selected and has points) --}}
            @if ($selectedCustomer && $customerPoints > 0)
                <div class="px-4 py-3 border-t border-gray-200 bg-gradient-to-r from-amber-50 to-orange-50">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-sm font-medium text-amber-800">🎁 Poin Tersedia</span>
                        <span class="text-sm font-bold text-amber-600">{{ number_format($customerPoints, 0) }}
                            poin</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <input type="number" wire:model.lazy="pointsToUse"
                            wire:change="setPointsToUse($event.target.value)" placeholder="Poin digunakan"
                            class="flex-1 px-3 py-2 text-sm border border-amber-300 rounded-lg focus:ring-2 focus:ring-amber-400"
                            min="0" max="{{ $customerPoints }}" />
                        <button wire:click="useAllPoints"
                            class="px-3 py-2 text-sm bg-amber-500 hover:bg-amber-600 text-white rounded-lg font-medium transition-all">
                            Pakai Semua
                        </button>
                    </div>
                    @if ($pointsValue > 0)
                        <p class="text-xs text-amber-700 mt-2">Potongan: Rp
                            {{ number_format($pointsValue, 0, ',', '.') }}</p>
                    @endif
                </div>
            @endif

            {{-- Summary & Checkout --}}
            <div class="border-t-4 border-blue-500 p-4 space-y-3 bg-gradient-to-br from-blue-50 to-indigo-50">
                <div class="space-y-2 text-sm">
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600">Subtotal</span>
                        <span class="font-semibold text-gray-900">Rp
                            {{ number_format($subtotal, 0, ',', '.') }}</span>
                    </div>
                    @if ($discountAmount > 0)
                        <div class="flex justify-between items-center text-green-600">
                            <span class="flex items-center gap-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z">
                                    </path>
                                </svg>
                                Diskon {{ $discountName ? "({$discountName})" : '' }}
                            </span>
                            <span class="font-semibold">- Rp {{ number_format($discountAmount, 0, ',', '.') }}</span>
                        </div>
                    @endif
                    @if ($pointsValue > 0)
                        <div class="flex justify-between items-center text-amber-600">
                            <span class="flex items-center gap-1">🎁 Poin
                                ({{ number_format($pointsToUse, 0) }})</span>
                            <span class="font-semibold">- Rp {{ number_format($pointsValue, 0, ',', '.') }}</span>
                        </div>
                    @endif
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600">PPN ({{ $taxPercentage }}%)</span>
                        <span class="font-semibold text-gray-900">Rp
                            {{ number_format($taxAmount, 0, ',', '.') }}</span>
                    </div>
                </div>

                <div class="border-t-2 border-gray-300 pt-3 flex justify-between items-center">
                    <span class="text-xl font-bold text-gray-900">TOTAL</span>
                    <span class="text-2xl font-bold text-blue-600">Rp {{ number_format($total, 0, ',', '.') }}</span>
                </div>

                @if ($selectedCustomer && $pointsEarned > 0)
                    <div class="text-center py-2 bg-green-100 rounded-lg">
                        <p class="text-sm text-green-700">🎉 Customer akan mendapat <strong>{{ $pointsEarned }}
                                poin</strong></p>
                    </div>
                @endif

                <button wire:click="openPaymentModal" @if (!$this->canCheckout) disabled @endif
                    class="w-full py-4 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 disabled:from-gray-300 disabled:to-gray-400 disabled:cursor-not-allowed text-white font-bold text-lg rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 transform hover:scale-[1.02] disabled:transform-none">
                    @if (empty($cart))
                        ❌ Keranjang Kosong
                    @elseif(!empty($stockErrors))
                        ⚠️ Ada Masalah Stok
                    @else
                        💳 BAYAR - Rp {{ number_format($total, 0, ',', '.') }}
                    @endif
                </button>

                <div class="grid grid-cols-3 gap-2 text-xs">
                    <div class="text-center p-2 bg-white rounded-lg border border-gray-200">
                        <p class="text-gray-500">Items</p>
                        <p class="font-bold text-gray-900">{{ $this->totalItems }}</p>
                    </div>
                    <div class="text-center p-2 bg-white rounded-lg border border-gray-200">
                        <p class="text-gray-500">Qty</p>
                        <p class="font-bold text-gray-900">{{ $this->totalQuantity }}</p>
                    </div>
                    <button wire:click="clearCart"
                        class="text-center p-2 bg-red-50 hover:bg-red-100 rounded-lg border border-red-200 text-red-600 transition-all">
                        🗑️ Clear
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Customer Selection Modal --}}
    @if ($showCustomerModal)
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50"
            wire:click="$set('showCustomerModal', false)">
            <div class="bg-white rounded-2xl shadow-2xl max-w-lg w-full max-h-[80vh] overflow-hidden" wire:click.stop>
                <div class="p-4 border-b border-gray-200 bg-gradient-to-r from-purple-50 to-pink-50">
                    <h3 class="text-lg font-bold text-gray-900">Pilih Customer</h3>
                    <input type="text" wire:model.live.debounce.300ms="searchCustomer"
                        placeholder="Cari nama/telepon/kode..."
                        class="w-full mt-3 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500" />

                    {{-- Birthday customers highlight in modal --}}
                    @if (!empty($birthdayCustomers))
                        <div
                            class="mt-3 p-2 bg-gradient-to-r from-pink-100 to-rose-100 rounded-lg border border-pink-200">
                            <p class="text-xs text-pink-700 font-bold mb-1">🎂 Ulang Tahun Hari Ini - Dapat Diskon
                                Spesial!</p>
                            <div class="flex flex-wrap gap-1">
                                @foreach ($birthdayCustomers as $bCustomer)
                                    <button wire:click="selectCustomer({{ $bCustomer['id'] }})"
                                        class="text-xs px-2 py-1 bg-white rounded-full border border-pink-300 text-pink-700 hover:bg-pink-50 transition-all font-medium">
                                        🎂 {{ $bCustomer['name'] }}
                                    </button>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
                <div class="overflow-y-auto max-h-96 p-4 space-y-2">
                    <button wire:click="selectCustomer(null)"
                        class="w-full p-3 text-left bg-gray-50 hover:bg-gray-100 rounded-lg transition-all border border-gray-200">
                        <p class="font-medium text-gray-700">🚶 Walk-in Customer</p>
                        <p class="text-xs text-gray-500">Tanpa member</p>
                    </button>
                    @foreach ($customers as $customer)
                        <button wire:click="selectCustomer({{ $customer->id }})"
                            class="w-full p-3 text-left rounded-lg transition-all border
                                {{ $customer->is_birthday ? 'bg-gradient-to-r from-pink-50 to-rose-50 border-pink-300 hover:border-pink-400 ring-1 ring-pink-200' : 'bg-white border-gray-200 hover:bg-purple-50 hover:border-purple-300' }}">
                            <div class="flex justify-between items-start">
                                <div class="flex-1">
                                    <div class="flex items-center gap-2">
                                        <p class="font-medium text-gray-900">{{ $customer->name }}</p>
                                        {{-- Birthday Badge in list --}}
                                        @if ($customer->is_birthday)
                                            <span
                                                class="inline-flex items-center gap-0.5 px-1.5 py-0.5 text-[10px] font-bold rounded-full bg-gradient-to-r from-pink-500 to-rose-500 text-white animate-pulse">
                                                🎂 ULTAH
                                            </span>
                                        @endif
                                    </div>
                                    <p class="text-xs text-gray-500">{{ $customer->phone ?? '-' }} •
                                        {{ $customer->customer_code }}</p>

                                    {{-- Show discount info for birthday customer --}}
                                    @if ($customer->is_birthday)
                                        <p class="text-[10px] text-pink-600 mt-1 font-medium">✨ Dapat diskon ulang
                                            tahun spesial!</p>
                                    @endif
                                </div>
                                <div class="text-right">
                                    @if ($customer->tier)
                                        <span
                                            class="px-2 py-0.5 text-xs rounded-full bg-{{ $customer->tier->color ?? 'gray' }}-100 text-{{ $customer->tier->color ?? 'gray' }}-700">
                                            {{ $customer->tier->name }}
                                        </span>
                                    @endif
                                    <p class="text-xs text-purple-600 font-medium mt-1">
                                        {{ number_format($customer->total_points, 0) }} poin</p>
                                </div>
                            </div>
                        </button>
                    @endforeach
                </div>
                <div class="p-4 border-t border-gray-200">
                    <button wire:click="$set('showCustomerModal', false)"
                        class="w-full py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 font-medium rounded-lg transition-all">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    @endif

    {{-- Payment Modal --}}
    @if ($showPaymentModal)
        <div class="fixed inset-0 bg-black bg-opacity-60 flex items-center justify-center p-4 z-50 backdrop-blur-sm">
            <div class="bg-white rounded-2xl shadow-2xl max-w-2xl w-full max-h-[90vh] overflow-hidden">
                <div class="p-4 border-b border-gray-200 bg-gradient-to-r from-blue-600 to-indigo-600">
                    <div class="flex justify-between items-center">
                        <h3 class="text-xl font-bold text-white">💳 Pembayaran</h3>
                        <button wire:click="closePaymentModal" class="text-white/80 hover:text-white p-1">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </div>

                <div class="p-6 overflow-y-auto max-h-[60vh]">
                    {{-- Total Display --}}
                    <div class="text-center mb-6 p-4 bg-blue-50 rounded-xl">
                        <p class="text-gray-600 mb-1">Total Pembayaran</p>
                        <p class="text-4xl font-bold text-blue-600">Rp {{ number_format($total, 0, ',', '.') }}</p>
                    </div>

                    {{-- Payment Methods --}}
                    <div class="mb-6">
                        <p class="text-sm font-semibold text-gray-700 mb-3">Metode Pembayaran</p>
                        <div class="grid grid-cols-3 gap-2">
                            @foreach ($paymentMethods as $method)
                                <button wire:click="setPaymentMethod('{{ $method['code'] }}')"
                                    class="p-3 border-2 rounded-xl transition-all {{ $paymentMethod === $method['code'] ? 'border-blue-500 bg-blue-50 text-blue-700' : 'border-gray-200 hover:border-gray-300' }}">
                                    <p class="text-sm font-medium">{{ $method['name'] }}</p>
                                </button>
                            @endforeach
                        </div>
                    </div>

                    {{-- Cash Payment Section --}}
                    @if ($paymentMethod === 'cash')
                        <div class="mb-6">
                            <p class="text-sm font-semibold text-gray-700 mb-3">Jumlah Dibayar</p>
                            <input type="number" wire:model.live="paidAmount"
                                class="w-full px-4 py-3 text-2xl font-bold text-center border-2 border-gray-300 rounded-xl focus:ring-4 focus:ring-blue-500 focus:border-blue-500"
                                placeholder="0" />

                            {{-- Quick Cash Buttons --}}
                            <div class="grid grid-cols-4 gap-2 mt-3">
                                @foreach ([50000, 100000, 150000, 200000] as $amount)
                                    <button wire:click="quickCash({{ $amount }})"
                                        class="py-2 px-3 bg-gray-100 hover:bg-gray-200 rounded-lg font-medium text-gray-700 transition-all text-sm">
                                        {{ number_format($amount / 1000, 0) }}K
                                    </button>
                                @endforeach
                            </div>
                            <button wire:click="exactAmount"
                                class="w-full mt-2 py-2 bg-blue-100 hover:bg-blue-200 text-blue-700 font-medium rounded-lg transition-all">
                                Uang Pas (Rp {{ number_format($total, 0, ',', '.') }})
                            </button>

                            {{-- Change Display --}}
                            @if ($paidAmount >= $total && $changeAmount > 0)
                                <div class="mt-4 p-4 bg-green-50 rounded-xl text-center">
                                    <p class="text-gray-600">Kembalian</p>
                                    <p class="text-3xl font-bold text-green-600">Rp
                                        {{ number_format($changeAmount, 0, ',', '.') }}</p>
                                </div>
                            @endif
                        </div>
                    @else
                        {{-- Non-cash payment --}}
                        <div class="mb-6">
                            <p class="text-sm font-semibold text-gray-700 mb-3">Referensi/No. Transaksi (Opsional)</p>
                            <input type="text" wire:model="paymentReference"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                                placeholder="Nomor referensi transaksi" />
                        </div>
                    @endif
                </div>

                {{-- Action Buttons --}}
                <div class="p-4 border-t border-gray-200 bg-gray-50">
                    <button wire:click="processPayment" @if ($paymentMethod === 'cash' && $paidAmount < $total) disabled @endif
                        class="w-full py-4 bg-gradient-to-r from-green-500 to-emerald-600 hover:from-green-600 hover:to-emerald-700 disabled:from-gray-300 disabled:to-gray-400 disabled:cursor-not-allowed text-white font-bold text-lg rounded-xl shadow-lg transition-all">
                        @if ($paymentMethod === 'cash' && $paidAmount < $total)
                            Jumlah Kurang (Rp {{ number_format($total - $paidAmount, 0, ',', '.') }})
                        @else
                            ✓ Selesaikan Pembayaran
                        @endif
                    </button>
                </div>
            </div>
        </div>
    @endif

    {{-- Receipt Modal --}}
    @if ($showReceipt && !empty($receipt))
        <div class="fixed inset-0 bg-black bg-opacity-60 flex items-center justify-center p-4 z-50 backdrop-blur-sm">
            <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full max-h-[90vh] overflow-hidden" id="receipt">
                <div class="p-6">
                    {{-- Success Icon --}}
                    <div class="text-center mb-6">
                        <div
                            class="mx-auto w-20 h-20 bg-gradient-to-br from-green-400 to-green-600 rounded-full flex items-center justify-center mb-4 shadow-lg">
                            <svg class="w-12 h-12 text-white" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3"
                                    d="M5 13l4 4L19 7"></path>
                            </svg>
                        </div>
                        <h2 class="text-2xl font-bold text-gray-900 mb-1">Transaksi Berhasil!</h2>
                        <p class="text-sm text-gray-500">{{ $receipt['date'] ?? now()->format('d/m/Y H:i') }}</p>
                    </div>

                    {{-- Order Info --}}
                    <div class="bg-blue-50 rounded-xl p-3 mb-4 text-center">
                        <p class="text-xs text-gray-600">No. Order</p>
                        <p class="text-xl font-bold text-blue-600">{{ $receipt['order_number'] ?? '-' }}</p>
                    </div>

                    {{-- Customer --}}
                    <div class="mb-4 pb-4 border-b border-dashed border-gray-300">
                        <p class="text-xs text-gray-500">Customer</p>
                        <p class="font-semibold">{{ $receipt['customer_name'] ?? 'Walk-in Customer' }}</p>
                    </div>

                    {{-- Items --}}
                    <div class="mb-4 pb-4 border-b border-dashed border-gray-300 max-h-40 overflow-y-auto">
                        @foreach ($receipt['items'] ?? [] as $item)
                            <div class="flex justify-between text-sm py-1">
                                <div>
                                    <span>{{ $item['name'] }}</span>
                                    <span class="text-gray-500 text-xs">x{{ $item['quantity'] }}</span>
                                </div>
                                <span>Rp {{ number_format($item['total'], 0, ',', '.') }}</span>
                            </div>
                        @endforeach
                    </div>

                    {{-- Totals --}}
                    <div class="space-y-1 text-sm mb-4">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Subtotal</span>
                            <span>Rp {{ number_format($receipt['subtotal'] ?? 0, 0, ',', '.') }}</span>
                        </div>
                        @if (($receipt['discount_amount'] ?? 0) > 0)
                            <div class="flex justify-between text-green-600">
                                <span>Diskon</span>
                                <span>- Rp {{ number_format($receipt['discount_amount'], 0, ',', '.') }}</span>
                            </div>
                        @endif
                        @if (($receipt['points_used'] ?? 0) > 0)
                            <div class="flex justify-between text-amber-600">
                                <span>Poin</span>
                                <span>- Rp {{ number_format($receipt['points_used'], 0, ',', '.') }}</span>
                            </div>
                        @endif
                        <div class="flex justify-between">
                            <span class="text-gray-600">PPN</span>
                            <span>Rp {{ number_format($receipt['tax_amount'] ?? 0, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between font-bold text-lg pt-2 border-t border-gray-200">
                            <span>TOTAL</span>
                            <span class="text-blue-600">Rp
                                {{ number_format($receipt['total'] ?? 0, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between text-gray-600">
                            <span>Dibayar ({{ $receipt['payment_method'] ?? 'Cash' }})</span>
                            <span>Rp {{ number_format($receipt['paid_amount'] ?? 0, 0, ',', '.') }}</span>
                        </div>
                        @if (($receipt['change_amount'] ?? 0) > 0)
                            <div class="flex justify-between font-semibold text-green-600">
                                <span>Kembalian</span>
                                <span>Rp {{ number_format($receipt['change_amount'], 0, ',', '.') }}</span>
                            </div>
                        @endif
                    </div>

                    {{-- Points Earned --}}
                    @if (($receipt['points_earned'] ?? 0) > 0)
                        <div class="bg-amber-50 rounded-lg p-3 mb-4 text-center">
                            <p class="text-sm text-amber-700">🎉 Customer mendapat</p>
                            <p class="text-xl font-bold text-amber-600">+{{ $receipt['points_earned'] }} Poin</p>
                        </div>
                    @endif

                    {{-- Cashier --}}
                    <div class="text-center text-xs text-gray-500 mb-4">
                        <p>Kasir: {{ $receipt['cashier'] ?? '-' }}</p>
                        <p class="mt-1">Terima kasih atas kunjungan Anda! 🙏</p>
                    </div>

                    {{-- Action Buttons --}}
                    <div class="space-y-2">
                        <button wire:click="closeReceipt"
                            class="w-full py-3 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white font-bold rounded-xl shadow-lg transition-all">
                            ✓ Transaksi Baru
                        </button>
                        <button onclick="window.print()"
                            class="w-full py-2 bg-white border-2 border-gray-300 hover:border-blue-500 text-gray-700 hover:text-blue-600 font-medium rounded-xl transition-all">
                            🖨️ Cetak Struk
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Notification Toast --}}
    <div x-data="{ notifications: [] }"
        @notify.window="
            let notification = $event.detail;
            notifications.push(notification);
            setTimeout(() => notifications.shift(), 3000);
        "
        class="fixed bottom-4 right-4 z-50 space-y-2">
        <template x-for="notification in notifications" :key="notification">
            <div x-show="true" x-transition:enter="transform ease-out duration-300 transition"
                x-transition:enter-start="translate-y-2 opacity-0" x-transition:enter-end="translate-y-0 opacity-100"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="translate-y-0 opacity-100" x-transition:leave-end="translate-y-2 opacity-0"
                :class="{
                    'bg-green-500': notification.type === 'success',
                    'bg-red-500': notification.type === 'error',
                    'bg-blue-500': notification.type === 'info',
                    'bg-yellow-500': notification.type === 'warning'
                }"
                class="px-4 py-3 rounded-lg shadow-lg text-white font-medium max-w-sm">
                <span x-text="notification.message"></span>
            </div>
        </template>
    </div>
</div>
