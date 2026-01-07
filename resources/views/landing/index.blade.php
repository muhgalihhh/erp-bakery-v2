@extends('layouts.landing')

@section('content')
    <!-- Hero Section -->
    <section id="home" class="relative pt-16 bg-gradient-to-br from-blue-50 via-white to-indigo-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20 md:py-32">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                <div class="text-center lg:text-left">
                    <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold text-gray-900 mb-6">
                        {{ $settings->shop_name }}
                    </h1>
                    @if ($settings->tagline)
                        <p class="text-xl md:text-2xl text-gray-600 mb-4">{{ $settings->tagline }}</p>
                    @endif
                    <p class="text-lg text-gray-600 mb-8">{{ $settings->description }}</p>

                    <div class="flex flex-col sm:flex-row gap-4 justify-center lg:justify-start">
                        <a href="#products"
                            class="bg-blue-600 text-white px-8 py-3 rounded-lg font-semibold hover:bg-blue-700 transition-colors inline-flex items-center justify-center">
                            <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                            </svg>
                            Lihat Produk
                        </a>
                        @if ($settings->whatsapp_link)
                            <a href="{{ $settings->whatsapp_link }}" target="_blank"
                                class="bg-green-600 text-white px-8 py-3 rounded-lg font-semibold hover:bg-green-700 transition-colors inline-flex items-center justify-center">
                                <svg class="h-5 w-5 mr-2" fill="currentColor" viewBox="0 0 24 24">
                                    <path
                                        d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z" />
                                </svg>
                                Hubungi Kami
                            </a>
                        @endif
                    </div>

                    @if ($settings->announcement && $settings->is_open)
                        <div class="mt-8 bg-blue-50 border-l-4 border-blue-600 p-4 rounded">
                            <p class="text-blue-800 flex items-start">
                                <svg class="h-5 w-5 mr-2 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                                        clip-rule="evenodd" />
                                </svg>
                                {{ $settings->announcement }}
                            </p>
                        </div>
                    @endif
                </div>

                <div class="relative">
                    @if ($settings->hero_image)
                        <img src="{{ $settings->hero_image_url }}" alt="Hero Image"
                            class="rounded-2xl shadow-2xl w-full object-cover" style="max-height: 500px;">
                    @else
                        <div class="bg-gradient-to-br from-blue-100 to-indigo-100 rounded-2xl shadow-2xl w-full flex items-center justify-center"
                            style="height: 500px;">
                            <svg class="h-48 w-48 text-blue-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </section>

    <!-- Products Section -->
    <section id="products" class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">Produk Kami</h2>
                <p class="text-lg text-gray-600">Produk berkualitas tinggi yang dibuat dengan bahan pilihan</p>
            </div>

            @if ($products->count() > 0)
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                    @foreach ($products as $product)
                        <div
                            class="bg-white rounded-xl shadow-md overflow-hidden hover:shadow-xl transition-shadow duration-300">
                            <div class="relative h-48 bg-gray-100">
                                @if ($product->image_url)
                                    <img src="{{ Storage::url($product->image_url) }}" alt="{{ $product->name }}"
                                        class="w-full h-full object-cover">
                                @else
                                    <div class="w-full h-full flex items-center justify-center">
                                        <svg class="h-20 w-20 text-gray-300" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                    </div>
                                @endif

                                @if ($product->is_active && $product->current_stock > 0)
                                    <div
                                        class="absolute top-2 right-2 bg-green-500 text-white px-2 py-1 rounded-full text-xs font-semibold">
                                        Tersedia
                                    </div>
                                @else
                                    <div
                                        class="absolute top-2 right-2 bg-red-500 text-white px-2 py-1 rounded-full text-xs font-semibold">
                                        Habis
                                    </div>
                                @endif

                                @if ($product->current_stock > 0 && $product->current_stock <= $product->minimum_stock)
                                    <div
                                        class="absolute top-2 left-2 bg-orange-500 text-white px-2 py-1 rounded-full text-xs font-semibold">
                                        Stok Terbatas
                                    </div>
                                @endif
                            </div>

                            <div class="p-4">
                                <h3 class="font-semibold text-lg text-gray-900 mb-2">{{ $product->name }}</h3>
                                @if ($product->description)
                                    <p class="text-gray-600 text-sm mb-3 line-clamp-2">{{ $product->description }}</p>
                                @endif
                                <div class="flex items-center justify-between">
                                    <div>
                                        <span class="text-xl font-bold text-blue-600">Rp
                                            {{ number_format($product->selling_price, 0, ',', '.') }}</span>
                                        @if ($product->uom_stock)
                                            <span class="text-sm text-gray-500">/{{ $product->uom_stock }}</span>
                                        @endif
                                    </div>
                                    @if ($settings->whatsapp_link && $product->current_stock > 0)
                                        <a href="{{ $settings->whatsapp_link }}?text=Halo, saya tertarik dengan produk {{ $product->name }}"
                                            target="_blank"
                                            class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-blue-700 transition-colors">
                                            Pesan
                                        </a>
                                    @endif
                                </div>
                                @if ($product->current_stock > 0)
                                    <div class="mt-2 text-xs text-gray-500">
                                        Stok: {{ number_format($product->current_stock, 0) }} {{ $product->uom_stock }}
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-12">
                    <svg class="h-24 w-24 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                    </svg>
                    <p class="text-gray-500 text-lg">Produk akan segera hadir</p>
                </div>
            @endif
        </div>
    </section>

    <!-- About Section -->
    <section id="about" class="py-20 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                <div class="order-2 lg:order-1">
                    @if ($settings->about_image)
                        <img src="{{ $settings->about_image_url }}" alt="About Us"
                            class="rounded-2xl shadow-xl w-full object-cover" style="max-height: 500px;">
                    @else
                        <div class="bg-gradient-to-br from-blue-100 to-indigo-100 rounded-2xl shadow-xl w-full flex items-center justify-center"
                            style="height: 500px;">
                            <svg class="h-48 w-48 text-blue-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                        </div>
                    @endif
                </div>

                <div class="order-1 lg:order-2">
                    <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-6">Tentang Kami</h2>
                    <div class="prose prose-lg text-gray-600">
                        {!! nl2br(e($settings->about_us ?? $settings->description)) !!}
                    </div>

                    @if ($settings->business_hours)
                        <div class="mt-8 bg-white rounded-xl shadow-md p-6">
                            <h3 class="text-xl font-semibold text-gray-900 mb-4 flex items-center">
                                <svg class="h-6 w-6 mr-2 text-blue-600" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                Jam Operasional
                            </h3>
                            <div class="space-y-2">
                                @foreach (['monday' => 'Senin', 'tuesday' => 'Selasa', 'wednesday' => 'Rabu', 'thursday' => 'Kamis', 'friday' => 'Jumat', 'saturday' => 'Sabtu', 'sunday' => 'Minggu'] as $day => $dayName)
                                    @if (isset($settings->business_hours[$day]))
                                        <div class="flex justify-between text-sm">
                                            <span class="font-medium text-gray-700">{{ $dayName }}</span>
                                            <span class="text-gray-600">{{ $settings->business_hours[$day]['open'] }} -
                                                {{ $settings->business_hours[$day]['close'] }}</span>
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </section>

    <!-- Contact Section -->
    <section id="contact" class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">Hubungi Kami</h2>
                <p class="text-lg text-gray-600">Kami siap melayani Anda</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                @if ($settings->address)
                    <div class="bg-gray-50 rounded-xl p-6 text-center">
                        <div class="inline-flex items-center justify-center w-16 h-16 bg-blue-100 rounded-full mb-4">
                            <svg class="h-8 w-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">Alamat</h3>
                        <p class="text-gray-600">{{ $settings->address }}</p>
                        <p class="text-gray-600">{{ $settings->city }} {{ $settings->postal_code }}</p>
                    </div>
                @endif

                @if ($settings->phone || $settings->email)
                    <div class="bg-gray-50 rounded-xl p-6 text-center">
                        <div class="inline-flex items-center justify-center w-16 h-16 bg-blue-100 rounded-full mb-4">
                            <svg class="h-8 w-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">Kontak</h3>
                        @if ($settings->phone)
                            <p class="text-gray-600">{{ $settings->phone }}</p>
                        @endif
                        @if ($settings->email)
                            <p class="text-gray-600">{{ $settings->email }}</p>
                        @endif
                    </div>
                @endif

                @if ($settings->whatsapp_link)
                    <div class="bg-gray-50 rounded-xl p-6 text-center">
                        <div class="inline-flex items-center justify-center w-16 h-16 bg-green-100 rounded-full mb-4">
                            <svg class="h-8 w-8 text-green-600" fill="currentColor" viewBox="0 0 24 24">
                                <path
                                    d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">WhatsApp</h3>
                        <p class="text-gray-600 mb-3">Chat dengan kami</p>
                        <a href="{{ $settings->whatsapp_link }}" target="_blank"
                            class="inline-block bg-green-600 text-white px-6 py-2 rounded-lg font-medium hover:bg-green-700 transition-colors">
                            Hubungi Sekarang
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </section>
@endsection
