{{-- Cart Panel dengan Toggle --}}
<div
    class="relative {{ $showCartPanel ? 'w-96 xl:w-[30rem]' : 'w-16' }} flex flex-col bg-white shadow-2xl transition-all duration-300">
    {{-- Toggle Button --}}
    <button wire:click="toggleCartPanel"
        class="absolute -left-4 top-1/2 -translate-y-1/2 z-10 w-8 h-16 bg-blue-600 hover:bg-blue-700 text-white rounded-l-lg shadow-lg transition-all flex items-center justify-center"
        title="{{ $showCartPanel ? 'Sembunyikan Keranjang' : 'Tampilkan Keranjang' }}">
        <svg class="w-5 h-5 transition-transform {{ $showCartPanel ? 'rotate-180' : '' }}" fill="none"
            stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
        </svg>
    </button>

    @if ($showCartPanel)
        {{-- Customer Section --}}
        <div class="p-5 border-b-2 border-blue-200 bg-gradient-to-r from-blue-50 to-indigo-50">
            <div class="flex items-center justify-between mb-3">
                <label class="flex items-center gap-2 text-sm font-bold text-gray-700 uppercase tracking-wide">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                    Customer
                </label>
            </div>

            @if ($selectedCustomer)
                <div
                    class="bg-white rounded-xl p-4 border-2 {{ $isCustomerBirthday ? 'border-pink-400 ring-2 ring-pink-200' : 'border-blue-300' }} shadow-sm">
                    <div class="flex items-start justify-between">
                        <div class="flex-1">
                            <div class="flex items-center gap-2 mb-1">
                                <p class="font-semibold text-gray-900">{{ $selectedCustomer['name'] }}</p>
                                @if ($isCustomerBirthday)
                                    <span
                                        class="inline-flex items-center gap-1 px-2 py-0.5 text-xs font-bold rounded-full bg-gradient-to-r from-pink-500 to-rose-500 text-white animate-pulse shadow-md">
                                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                            <path
                                                d="M10 2a.75.75 0 01.75.75v1.5a.75.75 0 01-1.5 0v-1.5A.75.75 0 0110 2zM10 15a.75.75 0 01.75.75v1.5a.75.75 0 01-1.5 0v-1.5A.75.75 0 0110 15zM10 7a3 3 0 100 6 3 3 0 000-6zM15.657 5.404a.75.75 0 10-1.06-1.06l-1.061 1.06a.75.75 0 001.06 1.06l1.06-1.06zM6.464 14.596a.75.75 0 10-1.06-1.06l-1.06 1.06a.75.75 0 001.06 1.06l1.06-1.06zM18 10a.75.75 0 01-.75.75h-1.5a.75.75 0 010-1.5h1.5A.75.75 0 0118 10zM5 10a.75.75 0 01-.75.75h-1.5a.75.75 0 010-1.5h1.5A.75.75 0 015 10zM14.596 15.657a.75.75 0 001.06-1.06l-1.06-1.061a.75.75 0 10-1.06 1.06l1.06 1.06zM5.404 6.464a.75.75 0 001.06-1.06l-1.06-1.06a.75.75 0 10-1.061 1.06l1.06 1.06z" />
                                        </svg>
                                        ULTAH!
                                    </span>
                                @endif
                            </div>
                            <p class="text-xs text-gray-500 mb-2">{{ $selectedCustomer['phone'] ?? '-' }}</p>
                            <div class="flex flex-wrap items-center gap-2">
                                <span
                                    class="px-2 py-1 text-xs rounded-full bg-{{ $selectedCustomer['tier_color'] }}-100 text-{{ $selectedCustomer['tier_color'] }}-700 font-medium">
                                    {{ $selectedCustomer['tier_name'] }}
                                </span>
                                <span class="flex items-center gap-1 text-xs text-blue-700 font-medium">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path
                                            d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                    </svg>
                                    {{ number_format($selectedCustomer['total_points'], 0) }} poin
                                </span>
                            </div>

                            @if (!empty($customerDiscounts))
                                <div class="mt-2 flex flex-wrap gap-1">
                                    @foreach ($customerDiscounts as $discount)
                                        <span
                                            class="inline-flex items-center gap-1 px-2 py-0.5 text-[10px] font-medium rounded-full shadow-sm
                                            @if ($discount['type'] === 'birthday') bg-pink-100 text-pink-700 border border-pink-200
                                            @elseif($discount['type'] === 'tier') bg-blue-100 text-blue-700 border border-blue-200
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
                            class="text-red-500 hover:text-red-700 p-1.5 hover:bg-red-50 rounded-lg transition-all">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    @if ($isCustomerBirthday)
                        <div
                            class="mt-3 p-2 bg-gradient-to-r from-pink-50 to-rose-50 rounded-lg border border-pink-200">
                            <p
                                class="text-xs text-pink-700 text-center font-medium flex items-center justify-center gap-1">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                                        clip-rule="evenodd" />
                                </svg>
                                Selamat Ulang Tahun! Nikmati diskon spesial hari ini!
                            </p>
                        </div>
                    @endif
                </div>
            @else
                <button wire:click="$set('showCustomerModal', true)"
                    class="w-full px-4 py-3 border-2 border-dashed border-blue-300 rounded-xl text-gray-500 hover:border-blue-500 hover:text-blue-700 hover:bg-blue-50 transition-all flex items-center justify-center gap-2 font-medium">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                    </svg>
                    Pilih Customer (Opsional)
                </button>

                @if (!empty($birthdayCustomers))
                    <div class="mt-2 p-2 bg-pink-50 rounded-lg border border-pink-200">
                        <p class="text-xs text-pink-700 font-medium mb-1 flex items-center gap-1">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                <path
                                    d="M10 2a.75.75 0 01.75.75v1.5a.75.75 0 01-1.5 0v-1.5A.75.75 0 0110 2zM10 15a.75.75 0 01.75.75v1.5a.75.75 0 01-1.5 0v-1.5A.75.75 0 0110 15z" />
                            </svg>
                            Customer yang berulang tahun hari ini:
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
        <div class="flex-1 overflow-y-auto custom-scrollbar p-4 space-y-3 bg-gray-50">
            @forelse($cart as $index => $item)
                <div class="bg-white rounded-xl p-4 border border-gray-200 shadow-sm hover:shadow-md transition-all">
                    <div class="flex justify-between items-start mb-3">
                        <div class="flex-1 pr-2">
                            <h4 class="font-semibold text-gray-900 text-sm leading-tight mb-1">{{ $item['name'] }}</h4>
                            <p class="text-xs text-gray-500">SKU: {{ $item['sku'] ?? '-' }}</p>
                        </div>
                        <button wire:click="removeFromCart({{ $index }})"
                            class="text-red-500 hover:text-red-700 hover:bg-red-50 p-2 rounded-lg transition-all"
                            title="Hapus item">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                        </button>
                    </div>
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <button wire:click="updateQuantity({{ $index }}, {{ $item['quantity'] - 1 }})"
                                class="w-8 h-8 flex items-center justify-center bg-gray-100 hover:bg-gray-200 rounded-lg font-bold text-gray-700 transition-all">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M20 12H4" />
                                </svg>
                            </button>
                            <input type="number"
                                wire:change="updateQuantity({{ $index }}, $event.target.value)"
                                value="{{ $item['quantity'] }}"
                                class="w-14 text-center border border-gray-300 rounded-lg py-1.5 text-sm font-semibold text-gray-900 focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                min="1" />
                            <button wire:click="updateQuantity({{ $index }}, {{ $item['quantity'] + 1 }})"
                                class="w-8 h-8 flex items-center justify-center bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-bold transition-all">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 4v16m8-8H4" />
                                </svg>
                            </button>
                        </div>
                        <div class="text-right">
                            <p class="text-xs text-gray-500 mb-1">@ Rp
                                {{ number_format($item['price'], 0, ',', '.') }}</p>
                            <p class="text-base font-bold text-gray-900">Rp
                                {{ number_format($item['price'] * $item['quantity'], 0, ',', '.') }}</p>
                        </div>
                    </div>
                    @if ($item['current_stock'] <= 5)
                        <div class="mt-2 flex items-center gap-1 text-xs text-orange-600">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                                    clip-rule="evenodd" />
                            </svg>
                            Stok tersisa: {{ number_format($item['current_stock'], 0) }}
                        </div>
                    @endif
                </div>
            @empty
                <div class="text-center py-16 text-gray-400">
                    <svg class="w-20 h-20 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                    <p class="text-base font-semibold mb-1 text-gray-600">Keranjang Kosong</p>
                    <p class="text-sm text-gray-400">Pilih produk untuk memulai transaksi</p>
                </div>
            @endforelse
        </div>

        @include('livewire.pos.partials.cart-summary')
    @else
        {{-- Collapsed Cart View --}}
        <div class="flex-1 flex flex-col items-center justify-center p-2">
            <svg class="w-8 h-8 text-gray-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
            </svg>
            @if (count($cart) > 0)
                <span
                    class="w-6 h-6 bg-red-500 text-white text-xs font-bold rounded-full flex items-center justify-center">
                    {{ count($cart) }}
                </span>
            @endif
        </div>
    @endif
</div>
