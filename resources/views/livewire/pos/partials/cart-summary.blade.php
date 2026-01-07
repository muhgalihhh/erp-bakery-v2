{{-- Points Section --}}
@if ($selectedCustomer && $customerPoints > 0)
    <div class="px-5 py-4 border-t-2 border-blue-200 bg-gradient-to-r from-blue-50 to-indigo-50">
        <div class="flex items-center justify-between mb-3">
            <span class="flex items-center gap-2 text-sm font-medium text-blue-800">
                <svg class="w-5 h-5 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                    <path
                        d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                </svg>
                Poin Tersedia
            </span>
            <span class="text-sm font-bold text-blue-600">{{ number_format($customerPoints, 0) }} poin</span>
        </div>
        <div class="flex items-center gap-2">
            <input type="number" wire:model.lazy="pointsToUse" wire:change="setPointsToUse($event.target.value)"
                placeholder="Gunakan poin"
                class="flex-1 px-3 py-2.5 text-sm border border-blue-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                min="0" max="{{ $customerPoints }}" />
            <x-pos.button variant="primary" size="sm" wire:click="useAllPoints">
                Pakai Semua
            </x-pos.button>
        </div>
        @if ($pointsValue > 0)
            <p class="text-xs text-blue-700 mt-2 flex items-center gap-1">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd"
                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-11a1 1 0 10-2 0v2H7a1 1 0 100 2h2v2a1 1 0 102 0v-2h2a1 1 0 100-2h-2V7z"
                        clip-rule="evenodd" />
                </svg>
                Potongan: Rp {{ number_format($pointsValue, 0, ',', '.') }}
            </p>
        @endif
    </div>
@endif

{{-- Active Discounts Banner --}}
@if (!empty($cart) && ($discountAmount > 0 || !empty($customerDiscounts)))
    <div class="border-t-2 border-green-300 px-5 py-4 bg-gradient-to-r from-green-50 to-emerald-50">
        <div class="flex items-center gap-2 mb-3">
            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
            </svg>
            <span class="text-sm font-bold text-green-700 uppercase tracking-wide">Diskon Diterapkan</span>
        </div>
        <div class="flex flex-wrap gap-2">
            @if ($discountAmount > 0 && $discountName)
                <span
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium rounded-lg shadow-sm bg-green-100 text-green-700 border border-green-200">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                    </svg>
                    {{ $discountName }}
                    <span class="font-bold">- Rp {{ number_format($discountAmount, 0, ',', '.') }}</span>
                </span>
            @endif

            @if ($pointsValue > 0)
                <span
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium rounded-lg shadow-sm bg-blue-100 text-blue-700 border border-blue-200">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                        <path
                            d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                    </svg>
                    Poin {{ number_format($pointsToUse, 0) }}
                    <span class="font-bold">- Rp {{ number_format($pointsValue, 0, ',', '.') }}</span>
                </span>
            @endif
        </div>
    </div>
@endif

{{-- Summary & Checkout --}}
<div class="border-t-4 border-blue-500 px-5 py-5 space-y-4 bg-gradient-to-br from-blue-50 to-indigo-50">
    <div class="space-y-3 text-sm">
        <div class="flex justify-between items-center">
            <span class="text-gray-600">Subtotal</span>
            <span class="font-semibold text-gray-900">Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
        </div>

        {{-- Discount Breakdown --}}
        @if ($discountAmount > 0 || $tierDiscount > 0 || $pointsValue > 0)
            <div class="bg-green-50 rounded-xl p-3 space-y-2 border border-green-200">
                <p class="text-xs font-bold text-green-700 uppercase tracking-wide flex items-center gap-1">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-11a1 1 0 10-2 0v2H7a1 1 0 100 2h2v2a1 1 0 102 0v-2h2a1 1 0 100-2h-2V7z"
                            clip-rule="evenodd" />
                    </svg>
                    Potongan Harga
                </p>

                @if ($discountAmount > 0)
                    <div class="flex justify-between items-center text-green-600 text-xs">
                        <span class="flex items-center gap-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                            </svg>
                            {{ $discountName ?? 'Diskon' }}
                        </span>
                        <span class="font-semibold">- Rp {{ number_format($discountAmount, 0, ',', '.') }}</span>
                    </div>
                @endif

                @if ($pointsValue > 0)
                    <div class="flex justify-between items-center text-blue-600 text-xs">
                        <span class="flex items-center gap-1">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                <path
                                    d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                            </svg>
                            Poin ({{ number_format($pointsToUse, 0) }})
                        </span>
                        <span class="font-semibold">- Rp {{ number_format($pointsValue, 0, ',', '.') }}</span>
                    </div>
                @endif

                {{-- Total Savings --}}
                <div
                    class="flex justify-between items-center pt-2 border-t border-green-300 text-green-700 font-bold text-sm">
                    <span>Total Hemat</span>
                    <span>Rp {{ number_format($discountAmount + $pointsValue, 0, ',', '.') }}</span>
                </div>
            </div>
        @endif

        <div class="flex justify-between items-center">
            <span class="text-gray-600">PPN ({{ $taxPercentage }}%)</span>
            <span class="font-semibold text-gray-900">Rp {{ number_format($taxAmount, 0, ',', '.') }}</span>
        </div>
    </div>

    {{-- Total --}}
    <div class="border-t-2 border-blue-300 pt-4">
        <div class="flex justify-between items-center mb-2">
            <span class="text-lg font-bold text-gray-900">TOTAL BAYAR</span>
            <span class="text-2xl font-bold text-blue-700">Rp {{ number_format($total, 0, ',', '.') }}</span>
        </div>
        @if ($discountAmount > 0 || $pointsValue > 0)
            <div class="flex justify-end">
                <span
                    class="text-xs text-green-600 font-medium bg-green-100 px-3 py-1 rounded-full inline-flex items-center gap-1">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-8.707l-3-3a1 1 0 00-1.414 1.414L10.586 9H7a1 1 0 100 2h3.586l-1.293 1.293a1 1 0 101.414 1.414l3-3a1 1 0 000-1.414z"
                            clip-rule="evenodd" />
                    </svg>
                    Hemat Rp {{ number_format($discountAmount + $pointsValue, 0, ',', '.') }}
                </span>
            </div>
        @endif
    </div>

    {{-- Points Earned Info --}}
    @if ($selectedCustomer && $pointsEarned > 0)
        <div class="text-center py-3 bg-gradient-to-r from-yellow-100 to-amber-100 rounded-lg border border-yellow-300">
            <p class="text-sm text-yellow-800 flex items-center justify-center gap-2">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                    <path
                        d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                </svg>
                Customer akan dapat <strong>{{ $pointsEarned }} poin</strong>
            </p>
        </div>
    @endif

    {{-- Action Buttons --}}
    <div class="flex gap-2">
        @if (!empty($cart))
            <x-pos.button variant="danger" size="md" wire:click="confirmClearCart" class="flex-shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                </svg>
            </x-pos.button>
        @endif

        <x-pos.button variant="success" size="lg" wire:click="openPaymentModal" :disabled="!$this->canCheckout"
            class="flex-1 text-base py-4 shadow-lg hover:shadow-xl transform hover:scale-[1.02] transition-all">
            @if (empty($cart))
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
                Keranjang Kosong
            @else
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
                BAYAR (F9)
            @endif
        </x-pos.button>
    </div>
</div>
