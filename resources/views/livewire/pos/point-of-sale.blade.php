<div class="{{ $isFullscreen ? 'h-full' : 'min-h-screen' }} bg-gradient-to-br from-blue-50 via-indigo-50 to-blue-50">
    {{-- Product Grid & Cart Container --}}
    <div class="flex {{ $isFullscreen ? 'h-full' : 'h-screen' }} overflow-hidden">
        {{-- LEFT: Product Section --}}
        <div class="flex-1 flex flex-col bg-white border-r-2 border-blue-200 shadow-lg">
            {{-- Search & Filter Bar --}}
            <div class="p-5 border-b-2 border-blue-200 bg-gradient-to-r from-blue-50 to-indigo-50">
                <div class="flex gap-4">
                    <div class="flex-1 relative">
                        <svg class="absolute left-4 top-1/2 transform -translate-y-1/2 w-5 h-5 text-blue-500"
                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                        <input wire:model.live.debounce.300ms="searchProduct" type="text"
                            placeholder="Cari produk (nama, SKU, barcode)..."
                            class="w-full pl-12 pr-4 py-3.5 text-base border-2 border-blue-300 rounded-xl focus:ring-4 focus:ring-blue-400 focus:border-blue-500 transition-all shadow-sm bg-white" />
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
                        <svg class="w-5 h-5 text-orange-600" fill="currentColor" viewBox="0 0 20 20">
                            <path
                                d="M10 2a.75.75 0 01.75.75v1.5a.75.75 0 01-1.5 0v-1.5A.75.75 0 0110 2zM10 15a.75.75 0 01.75.75v1.5a.75.75 0 01-1.5 0v-1.5A.75.75 0 0110 15zM10 7a3 3 0 100 6 3 3 0 000-6zM15.657 5.404a.75.75 0 10-1.06-1.06l-1.061 1.06a.75.75 0 001.06 1.06l1.06-1.06zM6.464 14.596a.75.75 0 10-1.06-1.06l-1.06 1.06a.75.75 0 001.06 1.06l1.06-1.06zM18 10a.75.75 0 01-.75.75h-1.5a.75.75 0 010-1.5h1.5A.75.75 0 0118 10zM5 10a.75.75 0 01-.75.75h-1.5a.75.75 0 010-1.5h1.5A.75.75 0 015 10zM14.596 15.657a.75.75 0 001.06-1.06l-1.06-1.061a.75.75 0 10-1.06 1.06l1.06 1.06zM5.404 6.464a.75.75 0 001.06-1.06l-1.06-1.06a.75.75 0 10-1.061 1.06l1.06 1.06z" />
                        </svg>
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
                                        <span class="inline-flex items-center gap-1">
                                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd"
                                                    d="M5.5 3A2.5 2.5 0 003 5.5v2.879a2.5 2.5 0 00.732 1.767l6.5 6.5a2.5 2.5 0 003.536 0l2.878-2.878a2.5 2.5 0 000-3.536l-6.5-6.5A2.5 2.5 0 008.38 3H5.5zM6 7a1 1 0 100-2 1 1 0 000 2z"
                                                    clip-rule="evenodd" />
                                            </svg>
                                            {{ $discount['name'] }}
                                        </span>
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
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path
                                        d="M10.75 2.75a.75.75 0 00-1.5 0v5.5a.75.75 0 001.5 0v-5.5zm6 0a.75.75 0 00-1.5 0v5.5a.75.75 0 001.5 0v-5.5zm-12 0a.75.75 0 00-1.5 0v5.5a.75.75 0 001.5 0v-5.5zm8.5 6.75a.75.75 0 01.75.75v3.19l1.72-1.72a.75.75 0 111.06 1.06l-3 3a.75.75 0 01-1.06 0l-3-3a.75.75 0 111.06-1.06l1.72 1.72V10.25a.75.75 0 01.75-.75zm-8.5 0a.75.75 0 01.75.75v3.19l1.72-1.72a.75.75 0 111.06 1.06l-3 3a.75.75 0 01-1.06 0l-3-3a.75.75 0 111.06-1.06l1.72 1.72V10.25a.75.75 0 01.75-.75z" />
                                </svg>
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
                                    <svg class="w-3.5 h-3.5 text-pink-600" fill="currentColor" viewBox="0 0 20 20">
                                        <path
                                            d="M10.75 2.75a.75.75 0 00-1.5 0v5.5a.75.75 0 001.5 0v-5.5zm6 0a.75.75 0 00-1.5 0v5.5a.75.75 0 001.5 0v-5.5zm-12 0a.75.75 0 00-1.5 0v5.5a.75.75 0 001.5 0v-5.5z" />
                                    </svg>
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
            <div class="flex-1 overflow-y-auto p-4 bg-amber-50/50">
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 2xl:grid-cols-6 gap-3">
                    @forelse($products as $product)
                        @php
                            $hasDiscount =
                                isset($productDiscounts[$product->id]) && !empty($productDiscounts[$product->id]);
                            $productDiscountInfo = $hasDiscount ? $productDiscounts[$product->id][0] : null;
                        @endphp
                        <button wire:click="addToCart({{ $product->id }})"
                            class="group bg-white border-2 rounded-xl p-3 hover:shadow-lg transition-all duration-200 text-left transform hover:-translate-y-0.5
                                {{ $product->current_stock <= 0 ? 'opacity-50 cursor-not-allowed border-stone-200' : '' }}
                                {{ $hasDiscount && $product->current_stock > 0 ? 'border-orange-300 hover:border-orange-500 ring-2 ring-orange-100' : 'border-amber-200 hover:border-amber-500' }}"
                            {{ $product->current_stock <= 0 ? 'disabled' : '' }}>
                            <div
                                class="aspect-square rounded-lg mb-2 flex items-center justify-center transition-all relative
                                    {{ $hasDiscount ? 'bg-gradient-to-br from-orange-50 to-amber-50 group-hover:from-orange-100 group-hover:to-amber-100' : 'bg-gradient-to-br from-amber-50 to-orange-50 group-hover:from-amber-100 group-hover:to-orange-100' }}">
                                @if ($product->image_url)
                                    <img src="{{ $product->image_url }}" alt="{{ $product->name }}"
                                        class="w-full h-full object-cover rounded-lg">
                                @else
                                    <svg class="w-10 h-10 {{ $hasDiscount ? 'text-orange-400 group-hover:text-orange-600' : 'text-amber-400 group-hover:text-amber-600' }} transition-colors"
                                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                    </svg>
                                @endif

                                {{-- Discount Badge --}}
                                @if ($hasDiscount)
                                    <span
                                        class="absolute top-1 left-1 bg-gradient-to-r from-orange-500 to-red-500 text-white text-[10px] px-1.5 py-0.5 rounded-full font-bold shadow-md animate-pulse inline-flex items-center gap-0.5">
                                        <svg class="w-2.5 h-2.5" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M5.5 3A2.5 2.5 0 003 5.5v2.879a2.5 2.5 0 00.732 1.767l6.5 6.5a2.5 2.5 0 003.536 0l2.878-2.878a2.5 2.5 0 000-3.536l-6.5-6.5A2.5 2.5 0 008.38 3H5.5zM6 7a1 1 0 100-2 1 1 0 000 2z"
                                                clip-rule="evenodd" />
                                        </svg>
                                        {{ $productDiscountInfo['value'] ?? 'PROMO' }}
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
                                class="font-semibold text-stone-900 mb-1 line-clamp-2 transition-colors text-xs leading-tight
                                    {{ $hasDiscount ? 'group-hover:text-orange-600' : 'group-hover:text-amber-700' }}">
                                {{ $product->name }}
                            </h3>
                            <p class="text-sm font-bold {{ $hasDiscount ? 'text-orange-600' : 'text-amber-700' }}">
                                Rp {{ number_format($product->selling_price ?? 0, 0, ',', '.') }}
                            </p>
                            <p class="text-xs text-stone-500">SKU: {{ $product->sku }}</p>

                            {{-- Discount info below product --}}
                            @if ($hasDiscount)
                                <p class="text-[10px] text-orange-600 mt-1 font-medium truncate inline-flex items-center gap-0.5"
                                    title="{{ $productDiscountInfo['name'] ?? '' }}">
                                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                        <path
                                            d="M10 2a.75.75 0 01.75.75v1.5a.75.75 0 01-1.5 0v-1.5A.75.75 0 0110 2zM10 15a.75.75 0 01.75.75v1.5a.75.75 0 01-1.5 0v-1.5A.75.75 0 0110 15zM10 7a3 3 0 100 6 3 3 0 000-6zM15.657 5.404a.75.75 0 10-1.06-1.06l-1.061 1.06a.75.75 0 001.06 1.06l1.06-1.06zM6.464 14.596a.75.75 0 10-1.06-1.06l-1.06 1.06a.75.75 0 001.06 1.06l1.06-1.06zM18 10a.75.75 0 01-.75.75h-1.5a.75.75 0 010-1.5h1.5A.75.75 0 0118 10zM5 10a.75.75 0 01-.75.75h-1.5a.75.75 0 010-1.5h1.5A.75.75 0 015 10zM14.596 15.657a.75.75 0 001.06-1.06l-1.06-1.061a.75.75 0 10-1.06 1.06l1.06 1.06zM5.404 6.464a.75.75 0 001.06-1.06l-1.06-1.06a.75.75 0 10-1.061 1.06l1.06 1.06z" />
                                    </svg>
                                    {{ $productDiscountInfo['name'] ?? 'Diskon' }}
                                </p>
                            @endif
                        </button>
                    @empty
                        <div class="col-span-full text-center py-16 text-stone-500">
                            <svg class="w-20 h-20 mx-auto mb-4 text-amber-300" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4">
                                </path>
                            </svg>
                            <p class="text-lg font-semibold mb-1">Tidak ada produk ditemukan</p>
                            <p class="text-sm text-stone-400">Coba gunakan kata kunci pencarian yang berbeda</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- RIGHT: Cart & Checkout Section --}}
        @include('livewire.pos.partials.cart-panel')
    </div>

    {{-- Customer Selection Modal --}}
    @if ($showCustomerModal)
        <div class="fixed inset-0 bg-black/30 backdrop-blur-[2px] flex items-center justify-center p-4 z-50"
            wire:click="$set('showCustomerModal', false)">
            <div class="bg-white rounded-2xl shadow-2xl max-w-lg w-full max-h-[80vh] overflow-hidden" wire:click.stop>
                <div class="p-4 border-b border-amber-200 bg-gradient-to-r from-amber-50 to-orange-50">
                    <h3 class="text-lg font-bold text-stone-900">Pilih Customer</h3>
                    <input type="text" wire:model.live.debounce.300ms="searchCustomer"
                        placeholder="Cari nama/telepon/kode..."
                        class="w-full mt-3 px-4 py-2 border border-amber-300 rounded-lg focus:ring-2 focus:ring-amber-500" />

                    {{-- Birthday customers highlight in modal --}}
                    @if (!empty($birthdayCustomers))
                        <div
                            class="mt-3 p-2 bg-gradient-to-r from-pink-100 to-rose-100 rounded-lg border border-pink-200">
                            <p class="text-xs text-pink-700 font-bold mb-1 inline-flex items-center gap-1">
                                <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20">
                                    <path
                                        d="M10.75 2.75a.75.75 0 00-1.5 0v5.5a.75.75 0 001.5 0v-5.5zm6 0a.75.75 0 00-1.5 0v5.5a.75.75 0 001.5 0v-5.5zm-12 0a.75.75 0 00-1.5 0v5.5a.75.75 0 001.5 0v-5.5z" />
                                </svg>
                                Ulang Tahun Hari Ini - Dapat Diskon Spesial!
                            </p>
                            <div class="flex flex-wrap gap-1">
                                @foreach ($birthdayCustomers as $bCustomer)
                                    <button wire:click="selectCustomer({{ $bCustomer['id'] }})"
                                        class="text-xs px-2 py-1 bg-white rounded-full border border-pink-300 text-pink-700 hover:bg-pink-50 transition-all font-medium inline-flex items-center gap-1">
                                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M10.75 2.75a.75.75 0 00-1.5 0v5.5a.75.75 0 001.5 0v-5.5z" />
                                        </svg>
                                        {{ $bCustomer['name'] }}
                                    </button>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
                <div class="overflow-y-auto max-h-96 p-4 space-y-2">
                    <button wire:click="selectCustomer(null)"
                        class="w-full p-3 text-left bg-stone-50 hover:bg-stone-100 rounded-lg transition-all border border-stone-200">
                        <p class="font-medium text-stone-700">🚶 Walk-in Customer</p>
                        <p class="text-xs text-stone-500">Tanpa member</p>
                    </button>
                    @foreach ($customers as $customer)
                        <button wire:click="selectCustomer({{ $customer->id }})"
                            class="w-full p-3 text-left rounded-lg transition-all border
                                {{ $customer->is_birthday ? 'bg-gradient-to-r from-pink-50 to-rose-50 border-pink-300 hover:border-pink-400 ring-1 ring-pink-200' : 'bg-white border-amber-200 hover:bg-amber-50 hover:border-amber-400' }}">
                            <div class="flex justify-between items-start">
                                <div class="flex-1">
                                    <div class="flex items-center gap-2">
                                        <p class="font-medium text-stone-900">{{ $customer->name }}</p>
                                        {{-- Birthday Badge in list --}}
                                        @if ($customer->is_birthday)
                                            <span
                                                class="inline-flex items-center gap-0.5 px-1.5 py-0.5 text-[10px] font-bold rounded-full bg-gradient-to-r from-pink-500 to-rose-500 text-white animate-pulse">
                                                <svg class="w-2.5 h-2.5" fill="currentColor" viewBox="0 0 20 20">
                                                    <path
                                                        d="M10.75 2.75a.75.75 0 00-1.5 0v5.5a.75.75 0 001.5 0v-5.5z" />
                                                </svg>
                                                ULTAH
                                            </span>
                                        @endif
                                    </div>
                                    <p class="text-xs text-stone-500">{{ $customer->phone ?? '-' }} •
                                        {{ $customer->customer_code }}</p>

                                    {{-- Show discount info for birthday customer --}}
                                    @if ($customer->is_birthday)
                                        <p
                                            class="text-[10px] text-pink-600 mt-1 font-medium inline-flex items-center gap-1">
                                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                                <path
                                                    d="M10 2a.75.75 0 01.75.75v1.5a.75.75 0 01-1.5 0v-1.5A.75.75 0 0110 2zM10 15a.75.75 0 01.75.75v1.5a.75.75 0 01-1.5 0v-1.5A.75.75 0 0110 15zM10 7a3 3 0 100 6 3 3 0 000-6zM15.657 5.404a.75.75 0 10-1.06-1.06l-1.061 1.06a.75.75 0 001.06 1.06l1.06-1.06zM6.464 14.596a.75.75 0 10-1.06-1.06l-1.06 1.06a.75.75 0 001.06 1.06l1.06-1.06zM18 10a.75.75 0 01-.75.75h-1.5a.75.75 0 010-1.5h1.5A.75.75 0 0118 10zM5 10a.75.75 0 01-.75.75h-1.5a.75.75 0 010-1.5h1.5A.75.75 0 015 10zM14.596 15.657a.75.75 0 001.06-1.06l-1.06-1.061a.75.75 0 10-1.06 1.06l1.06 1.06zM5.404 6.464a.75.75 0 001.06-1.06l-1.06-1.06a.75.75 0 10-1.061 1.06l1.06 1.06z" />
                                            </svg>
                                            Dapat diskon ulang tahun spesial!
                                        </p>
                                    @endif
                                </div>
                                <div class="text-right">
                                    @if ($customer->tier)
                                        <span
                                            class="px-2 py-0.5 text-xs rounded-full bg-{{ $customer->tier->color ?? 'gray' }}-100 text-{{ $customer->tier->color ?? 'gray' }}-700">
                                            {{ $customer->tier->name }}
                                        </span>
                                    @endif
                                    <p class="text-xs text-amber-700 font-medium mt-1">
                                        {{ number_format($customer->total_points, 0) }} poin</p>
                                </div>
                            </div>
                        </button>
                    @endforeach
                </div>
                <div class="p-4 border-t border-amber-200">
                    <button wire:click="$set('showCustomerModal', false)"
                        class="w-full py-2 bg-stone-200 hover:bg-stone-300 text-stone-700 font-medium rounded-lg transition-all">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    @endif

    {{-- Payment Modal --}}
    @if ($showPaymentModal)
        <div class="fixed inset-0 bg-black/40 backdrop-blur-[3px] flex items-center justify-center p-4 z-50">
            <div class="bg-white rounded-2xl shadow-2xl max-w-2xl w-full max-h-[90vh] overflow-hidden">
                <div class="p-4 border-b border-amber-200 bg-gradient-to-r from-amber-600 to-orange-600">
                    <div class="flex justify-between items-center">
                        <h3 class="text-xl font-bold text-white inline-flex items-center gap-2">
                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z" />
                                <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z" />
                            </svg>
                            Konfirmasi Pembayaran
                        </h3>
                        <button wire:click="closePaymentModal" class="text-white/80 hover:text-white p-1">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </div>

                <div class="p-6 overflow-y-auto max-h-[60vh]">
                    {{-- Customer Info --}}
                    @if ($selectedCustomer)
                        <div
                            class="mb-4 p-3 rounded-xl {{ $isCustomerBirthday ? 'bg-gradient-to-r from-pink-50 to-rose-50 border border-pink-200' : 'bg-amber-50 border border-amber-200' }}">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <svg class="w-5 h-5 text-amber-700" fill="currentColor" viewBox="0 0 20 20">
                                        <path
                                            d="M10 8a3 3 0 100-6 3 3 0 000 6zM3.465 14.493a1.23 1.23 0 00.41 1.412A9.957 9.957 0 0010 18c2.31 0 4.438-.784 6.131-2.1.43-.333.604-.903.408-1.41a7.002 7.002 0 00-13.074.003z" />
                                    </svg>
                                    <div>
                                        <p class="font-semibold text-stone-900">{{ $selectedCustomer['name'] }}
                                        </p>
                                        <div class="flex items-center gap-2">
                                            <span
                                                class="px-2 py-0.5 text-xs rounded-full bg-{{ $selectedCustomer['tier_color'] }}-100 text-{{ $selectedCustomer['tier_color'] }}-700">
                                                {{ $selectedCustomer['tier_name'] }}
                                            </span>
                                            @if ($isCustomerBirthday)
                                                <span
                                                    class="px-2 py-0.5 text-xs rounded-full bg-pink-500 text-white font-bold inline-flex items-center gap-1">
                                                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                                        <path
                                                            d="M10.75 2.75a.75.75 0 00-1.5 0v5.5a.75.75 0 001.5 0v-5.5z" />
                                                    </svg>
                                                    Ultah!
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <p class="text-sm text-amber-700 font-medium">
                                    {{ number_format($selectedCustomer['total_points'], 0) }} poin</p>
                            </div>
                        </div>
                    @endif

                    {{-- Order Summary with Discount Details --}}
                    <div class="mb-4 p-4 bg-amber-50 rounded-xl border border-amber-200">
                        <h4 class="text-sm font-bold text-stone-700 mb-3 flex items-center gap-2">
                            <span class="w-5 h-5 bg-amber-600 rounded-full flex items-center justify-center">
                                <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2">
                                    </path>
                                </svg>
                            </span>
                            Ringkasan Pesanan
                        </h4>

                        <div class="space-y-2 text-sm">
                            {{-- Items Summary --}}
                            <div class="flex justify-between text-stone-600">
                                <span>{{ $this->totalItems }} item ({{ $this->totalQuantity }} qty)</span>
                                <span>Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
                            </div>

                            {{-- Discount Details Box --}}
                            @if ($discountAmount > 0 || $pointsValue > 0)
                                <div class="bg-green-50 rounded-lg p-3 mt-2 border border-green-200">
                                    <p class="text-xs font-bold text-green-700 mb-2 flex items-center gap-1">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z">
                                            </path>
                                        </svg>
                                        DISKON YANG DITERAPKAN
                                    </p>

                                    @if ($discountAmount > 0)
                                        <div class="flex justify-between items-center text-green-700 mb-1">
                                            <span class="flex items-center gap-1.5">
                                                @if (str_contains(strtolower($discountName ?? ''), 'birthday') || str_contains(strtolower($discountName ?? ''), 'ultah'))
                                                    <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20">
                                                        <path
                                                            d="M10.75 2.75a.75.75 0 00-1.5 0v5.5a.75.75 0 001.5 0v-5.5z" />
                                                    </svg>
                                                @elseif(str_contains(strtolower($discountName ?? ''), 'tier') || str_contains(strtolower($discountName ?? ''), 'member'))
                                                    <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd"
                                                            d="M9.664 1.319a.75.75 0 01.672 0 41.059 41.059 0 018.198 5.424.75.75 0 01-.254 1.285 31.372 31.372 0 00-7.86 3.83.75.75 0 01-.84 0 31.508 31.508 0 00-2.08-1.287V9.394c0-.244.116-.463.302-.592a35.504 35.504 0 013.305-2.033.75.75 0 00-.714-1.319 37 37 0 00-3.446 2.12A2.216 2.216 0 006 9.393v.38a31.293 31.293 0 00-4.28-1.746.75.75 0 01-.254-1.285 41.059 41.059 0 018.198-5.424zM6 11.459a29.848 29.848 0 00-2.455-1.158 41.029 41.029 0 00-.39 3.114.75.75 0 00.419.74c.528.256 1.046.53 1.554.82-.21.324-.455.63-.739.914a.75.75 0 101.06 1.06c.37-.369.69-.77.96-1.193a26.61 26.61 0 013.095 2.348.75.75 0 00.992 0 26.547 26.547 0 015.93-3.95.75.75 0 00.42-.739 41.053 41.053 0 00-.39-3.114 29.925 29.925 0 00-5.199 2.801 2.25 2.25 0 01-2.514 0c-.41-.275-.826-.541-1.25-.797a6.985 6.985 0 01-1.084 3.45 26.503 26.503 0 00-1.281-.78A5.487 5.487 0 006 12v-.54z"
                                                            clip-rule="evenodd" />
                                                    </svg>
                                                @else
                                                    <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd"
                                                            d="M5.5 3A2.5 2.5 0 003 5.5v2.879a2.5 2.5 0 00.732 1.767l6.5 6.5a2.5 2.5 0 003.536 0l2.878-2.878a2.5 2.5 0 000-3.536l-6.5-6.5A2.5 2.5 0 008.38 3H5.5zM6 7a1 1 0 100-2 1 1 0 000 2z"
                                                            clip-rule="evenodd" />
                                                    </svg>
                                                @endif
                                                {{ $discountName ?? 'Diskon' }}
                                            </span>
                                            <span class="font-semibold">- Rp
                                                {{ number_format($discountAmount, 0, ',', '.') }}</span>
                                        </div>
                                    @endif

                                    @if ($pointsValue > 0)
                                        <div class="flex justify-between items-center text-amber-700 mb-1">
                                            <span class="flex items-center gap-1.5">
                                                <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20">
                                                    <path
                                                        d="M9.653 16.915l-.005-.003-.019-.01a20.759 20.759 0 01-1.162-.682 22.045 22.045 0 01-2.582-1.9C4.045 12.733 2 10.352 2 7.5a4.5 4.5 0 018-2.828A4.5 4.5 0 0118 7.5c0 2.852-2.044 5.233-3.885 6.82a22.049 22.049 0 01-3.744 2.582l-.019.010-.005.003h-.002a.739.739 0 01-.69.001l-.002-.001z" />
                                                </svg>
                                                Poin Digunakan ({{ number_format($pointsToUse, 0) }} poin)
                                            </span> </span>
                                            <span class="font-semibold">- Rp
                                                {{ number_format($pointsValue, 0, ',', '.') }}</span>
                                        </div>
                                    @endif

                                    <div
                                        class="flex justify-between items-center pt-2 mt-2 border-t border-green-300 text-green-800 font-bold">
                                        <span>Total Penghematan</span>
                                        <span>Rp
                                            {{ number_format($discountAmount + $pointsValue, 0, ',', '.') }}</span>
                                    </div>
                                </div>
                            @endif

                            {{-- Tax --}}
                            <div class="flex justify-between text-stone-600 pt-2">
                                <span>PPN ({{ $taxPercentage }}%)</span>
                                <span>Rp {{ number_format($taxAmount, 0, ',', '.') }}</span>
                            </div>
                        </div>
                    </div>

                    {{-- Total Display --}}
                    <div
                        class="text-center mb-6 p-4 bg-gradient-to-r from-amber-500 to-orange-600 rounded-xl shadow-lg">
                        <p class="text-white/80 text-sm mb-1">Total yang Harus Dibayar</p>
                        <p class="text-4xl font-bold text-white">Rp {{ number_format($total, 0, ',', '.') }}</p>
                        @if ($discountAmount > 0 || $pointsValue > 0)
                            <p class="text-green-300 text-sm mt-1 flex items-center justify-center gap-1">
                                <span class="line-through text-white/60">Rp
                                    {{ number_format($subtotal + $taxAmount, 0, ',', '.') }}</span>
                                <span class="bg-green-500 text-white px-2 py-0.5 rounded-full text-xs font-bold">
                                    HEMAT
                                    {{ round((($discountAmount + $pointsValue) / ($subtotal > 0 ? $subtotal : 1)) * 100) }}%
                                </span>
                            </p>
                        @endif
                    </div>

                    {{-- Points to Earn --}}
                    @if ($selectedCustomer && $pointsEarned > 0)
                        <div class="mb-4 p-3 bg-amber-50 rounded-xl border border-amber-200 text-center">
                            <p class="text-sm text-amber-700 inline-flex items-center justify-center gap-1 w-full">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path
                                        d="M10 2a.75.75 0 01.75.75v1.5a.75.75 0 01-1.5 0v-1.5A.75.75 0 0110 2zM10 15a.75.75 0 01.75.75v1.5a.75.75 0 01-1.5 0v-1.5A.75.75 0 0110 15zM10 7a3 3 0 100 6 3 3 0 000-6zM15.657 5.404a.75.75 0 10-1.06-1.06l-1.061 1.06a.75.75 0 001.06 1.06l1.06-1.06zM6.464 14.596a.75.75 0 10-1.06-1.06l-1.06 1.06a.75.75 0 001.06 1.06l1.06-1.06zM18 10a.75.75 0 01-.75.75h-1.5a.75.75 0 010-1.5h1.5A.75.75 0 0118 10zM5 10a.75.75 0 01-.75.75h-1.5a.75.75 0 010-1.5h1.5A.75.75 0 015 10zM14.596 15.657a.75.75 0 001.06-1.06l-1.06-1.061a.75.75 0 10-1.06 1.06l1.06 1.06zM5.404 6.464a.75.75 0 001.06-1.06l-1.06-1.06a.75.75 0 10-1.061 1.06l1.06 1.06z" />
                                </svg>
                                Customer akan mendapat
                            </p>
                            <p class="text-xl font-bold text-amber-600">+{{ $pointsEarned }} Poin</p>
                        </div>
                    @endif

                    {{-- Payment Methods --}}
                    <div class="mb-6">
                        <p class="text-sm font-semibold text-stone-700 mb-3">Metode Pembayaran</p>
                        <div class="grid grid-cols-3 gap-2">
                            @foreach ($paymentMethods as $method)
                                <button wire:click="setPaymentMethod('{{ $method['code'] }}')"
                                    class="p-3 border-2 rounded-xl transition-all {{ $paymentMethod === $method['code'] ? 'border-amber-500 bg-amber-50 text-amber-700' : 'border-stone-200 hover:border-stone-300' }}">
                                    <p class="text-sm font-medium">{{ $method['name'] }}</p>
                                </button>
                            @endforeach
                        </div>
                    </div>

                    {{-- Cash Payment Section --}}
                    @if ($paymentMethod === 'cash')
                        <div class="mb-6">
                            <p class="text-sm font-semibold text-stone-700 mb-3">Jumlah Dibayar</p>
                            <input type="number" wire:model.live="paidAmount"
                                class="w-full px-4 py-3 text-2xl font-bold text-center border-2 border-amber-300 rounded-xl focus:ring-4 focus:ring-amber-500 focus:border-amber-500"
                                placeholder="0" />

                            {{-- Quick Cash Buttons --}}
                            <div class="grid grid-cols-4 gap-2 mt-3">
                                @foreach ([50000, 100000, 150000, 200000] as $amount)
                                    <button wire:click="quickCash({{ $amount }})"
                                        class="py-2 px-3 bg-stone-100 hover:bg-stone-200 rounded-lg font-medium text-stone-700 transition-all text-sm">
                                        {{ number_format($amount / 1000, 0) }}K
                                    </button>
                                @endforeach
                            </div>
                            <button wire:click="exactAmount"
                                class="w-full mt-2 py-2 bg-amber-100 hover:bg-amber-200 text-amber-700 font-medium rounded-lg transition-all">
                                Uang Pas (Rp {{ number_format($total, 0, ',', '.') }})
                            </button>

                            {{-- Change Display --}}
                            @if ($paidAmount >= $total && $changeAmount > 0)
                                <div class="mt-4 p-4 bg-green-50 rounded-xl text-center">
                                    <p class="text-stone-600">Kembalian</p>
                                    <p class="text-3xl font-bold text-green-600">Rp
                                        {{ number_format($changeAmount, 0, ',', '.') }}</p>
                                </div>
                            @endif
                        </div>
                    @else
                        {{-- Non-cash payment --}}
                        <div class="mb-6">
                            <p class="text-sm font-semibold text-stone-700 mb-3">Referensi/No. Transaksi (Opsional)
                            </p>
                            <input type="text" wire:model="paymentReference"
                                class="w-full px-4 py-2 border border-amber-300 rounded-lg focus:ring-2 focus:ring-amber-500"
                                placeholder="Nomor referensi transaksi" />
                        </div>
                    @endif
                </div>

                {{-- Action Buttons --}}
                <div class="p-4 border-t border-amber-200 bg-amber-50">
                    <button wire:click="processPayment" @if ($paymentMethod === 'cash' && $paidAmount < $total) disabled @endif
                        class="w-full py-4 bg-gradient-to-r from-green-500 to-emerald-600 hover:from-green-600 hover:to-emerald-700 disabled:from-stone-300 disabled:to-stone-400 disabled:cursor-not-allowed text-white font-bold text-lg rounded-xl shadow-lg transition-all">
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
        <div class="fixed inset-0 bg-black/40 backdrop-blur-[3px] flex items-center justify-center p-4 z-50">
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
                        <h2 class="text-2xl font-bold text-stone-900 mb-1">Transaksi Berhasil!</h2>
                        <p class="text-sm text-stone-500">{{ $receipt['date'] ?? now()->format('d/m/Y H:i') }}
                        </p>
                    </div>

                    {{-- Order Info --}}
                    <div class="bg-amber-50 rounded-xl p-3 mb-4 text-center border border-amber-200">
                        <p class="text-xs text-stone-600">No. Order</p>
                        <p class="text-xl font-bold text-amber-700">{{ $receipt['order_number'] ?? '-' }}</p>
                    </div>

                    {{-- Customer --}}
                    <div class="mb-4 pb-4 border-b border-dashed border-amber-300">
                        <p class="text-xs text-stone-500">Customer</p>
                        <p class="font-semibold">{{ $receipt['customer_name'] ?? 'Walk-in Customer' }}</p>
                    </div>

                    {{-- Items --}}
                    <div class="mb-4 pb-4 border-b border-dashed border-amber-300 max-h-40 overflow-y-auto">
                        @foreach ($receipt['items'] ?? [] as $item)
                            <div class="flex justify-between text-sm py-1">
                                <div>
                                    <span>{{ $item['name'] }}</span>
                                    <span class="text-stone-500 text-xs">x{{ $item['quantity'] }}</span>
                                </div>
                                <span>Rp {{ number_format($item['total'], 0, ',', '.') }}</span>
                            </div>
                        @endforeach
                    </div>

                    {{-- Totals --}}
                    <div class="space-y-1 text-sm mb-4">
                        <div class="flex justify-between">
                            <span class="text-stone-600">Subtotal</span>
                            <span>Rp {{ number_format($receipt['subtotal'] ?? 0, 0, ',', '.') }}</span>
                        </div>

                        {{-- Discount Details --}}
                        @if (($receipt['discount_amount'] ?? 0) > 0 || ($receipt['points_used'] ?? 0) > 0)
                            <div class="bg-green-50 rounded-lg p-2 my-2 border border-green-200">
                                <p
                                    class="text-[10px] font-bold text-green-700 uppercase mb-1 inline-flex items-center gap-1">
                                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M5.5 3A2.5 2.5 0 003 5.5v2.879a2.5 2.5 0 00.732 1.767l6.5 6.5a2.5 2.5 0 003.536 0l2.878-2.878a2.5 2.5 0 000-3.536l-6.5-6.5A2.5 2.5 0 008.38 3H5.5zM6 7a1 1 0 100-2 1 1 0 000 2z"
                                            clip-rule="evenodd" />
                                    </svg>
                                    Potongan
                                </p>
                                @if (($receipt['discount_amount'] ?? 0) > 0)
                                    <div class="flex justify-between text-green-600">
                                        <span class="flex items-center gap-1">
                                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd"
                                                    d="M5.5 3A2.5 2.5 0 003 5.5v2.879a2.5 2.5 0 00.732 1.767l6.5 6.5a2.5 2.5 0 003.536 0l2.878-2.878a2.5 2.5 0 000-3.536l-6.5-6.5A2.5 2.5 0 008.38 3H5.5zM6 7a1 1 0 100-2 1 1 0 000 2z"
                                                    clip-rule="evenodd" />
                                            </svg>
                                            {{ $receipt['discount_source'] ?? 'Diskon' }}
                                        </span>
                                        <span class="font-semibold">- Rp
                                            {{ number_format($receipt['discount_amount'], 0, ',', '.') }}</span>
                                    </div>
                                @endif
                                @if (($receipt['points_used'] ?? 0) > 0)
                                    <div class="flex justify-between text-amber-600">
                                        <span class="flex items-center gap-1">
                                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                                <path
                                                    d="M9.653 16.915l-.005-.003-.019-.01a20.759 20.759 0 01-1.162-.682 22.045 22.045 0 01-2.582-1.9C4.045 12.733 2 10.352 2 7.5a4.5 4.5 0 018-2.828A4.5 4.5 0 0118 7.5c0 2.852-2.044 5.233-3.885 6.82a22.049 22.049 0 01-3.744 2.582l-.019.010-.005.003h-.002a.739.739 0 01-.69.001l-.002-.001z" />
                                            </svg>
                                            Poin Digunakan
                                        </span>
                                        <span class="font-semibold">- Rp
                                            {{ number_format($receipt['points_used'], 0, ',', '.') }}</span>
                                    </div>
                                @endif
                                <div class="flex justify-end mt-1 pt-1 border-t border-green-300">
                                    <span class="text-xs text-green-700 font-bold">
                                        Total Hemat: Rp
                                        {{ number_format(($receipt['discount_amount'] ?? 0) + ($receipt['points_used'] ?? 0), 0, ',', '.') }}
                                    </span>
                                </div>
                            </div>
                        @endif

                        <div class="flex justify-between">
                            <span class="text-stone-600">PPN</span>
                            <span>Rp {{ number_format($receipt['tax_amount'] ?? 0, 0, ',', '.') }}</span>
                        </div>
                        <div
                            class="flex justify-between font-bold text-lg pt-2 border-t border-amber-200 bg-amber-50 -mx-2 px-2 py-2 rounded-lg">
                            <span>TOTAL</span>
                            <span class="text-amber-700">Rp
                                {{ number_format($receipt['total'] ?? 0, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between text-stone-600">
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
                        <div class="bg-amber-50 rounded-lg p-3 mb-4 text-center border border-amber-200">
                            <p class="text-sm text-amber-700 inline-flex items-center justify-center gap-1 w-full">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path
                                        d="M10 2a.75.75 0 01.75.75v1.5a.75.75 0 01-1.5 0v-1.5A.75.75 0 0110 2zM10 15a.75.75 0 01.75.75v1.5a.75.75 0 01-1.5 0v-1.5A.75.75 0 0110 15zM10 7a3 3 0 100 6 3 3 0 000-6zM15.657 5.404a.75.75 0 10-1.06-1.06l-1.061 1.06a.75.75 0 001.06 1.06l1.06-1.06zM6.464 14.596a.75.75 0 10-1.06-1.06l-1.06 1.06a.75.75 0 001.06 1.06l1.06-1.06zM18 10a.75.75 0 01-.75.75h-1.5a.75.75 0 010-1.5h1.5A.75.75 0 0118 10zM5 10a.75.75 0 01-.75.75h-1.5a.75.75 0 010-1.5h1.5A.75.75 0 015 10zM14.596 15.657a.75.75 0 001.06-1.06l-1.06-1.061a.75.75 0 10-1.06 1.06l1.06 1.06zM5.404 6.464a.75.75 0 001.06-1.06l-1.06-1.06a.75.75 0 10-1.061 1.06l1.06 1.06z" />
                                </svg>
                                Customer mendapat
                            </p>
                            <p class="text-xl font-bold text-amber-600">+{{ $receipt['points_earned'] }} Poin</p>
                        </div>
                    @endif

                    {{-- Cashier --}}
                    <div class="text-center text-xs text-stone-500 mb-4">
                        <p>Kasir: {{ $receipt['cashier'] ?? '-' }}</p>
                        <p class="mt-1">Terima kasih atas kunjungan Anda! 🙏</p>
                    </div>

                    {{-- Action Buttons --}}
                    <div class="space-y-2">
                        <button wire:click="closeReceipt"
                            class="w-full py-3 bg-gradient-to-r from-amber-600 to-orange-600 hover:from-amber-700 hover:to-orange-700 text-white font-bold rounded-xl shadow-lg transition-all">
                            ✓ Transaksi Baru
                        </button>
                        <button onclick="window.print()"
                            class="w-full py-2 bg-white border-2 border-amber-300 hover:border-amber-500 text-stone-700 hover:text-amber-700 font-medium rounded-xl transition-all inline-flex items-center justify-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                            </svg>
                            Cetak Struk
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
                    'bg-amber-600': notification.type === 'info',
                    'bg-yellow-500': notification.type === 'warning'
                }"
                class="px-4 py-3 rounded-lg shadow-lg text-white font-medium max-w-sm">
                <span x-text="notification.message"></span>
            </div>
        </template>
    </div>

    {{-- MODALS --}}
    @include('livewire.pos.partials.clear-cart-modal')
</div>
