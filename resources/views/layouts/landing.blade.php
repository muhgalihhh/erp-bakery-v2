<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $settings->meta_title ?? $settings->shop_name }}</title>
    <meta name="description" content="{{ $settings->meta_description ?? $settings->description }}">
    <meta name="keywords" content="{{ $settings->meta_keywords ?? '' }}">

    <!-- Favicon -->
    @if ($settings->logo)
        <link rel="icon" type="image/x-icon" href="{{ $settings->logo_url }}">
    @endif

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        [x-cloak] {
            display: none !important;
        }
    </style>
</head>

<body class="antialiased bg-gray-50" x-data="{ mobileMenuOpen: false, scrolled: false }" @scroll.window="scrolled = window.scrollY > 20">

    <!-- Navigation -->
    <nav class="fixed w-full z-50 transition-all duration-300"
        :class="scrolled ? 'bg-white shadow-md' : 'bg-white/90 backdrop-blur-sm'">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <!-- Logo -->
                <div class="flex items-center">
                    <a href="/" class="flex items-center space-x-3">
                        @if ($settings->logo)
                            <img src="{{ $settings->logo_url }}" alt="{{ $settings->shop_name }}" class="h-10 w-auto">
                        @endif
                        <span class="text-xl font-bold text-gray-900">{{ $settings->shop_name }}</span>
                    </a>
                </div>

                <!-- Desktop Menu -->
                <div class="hidden md:flex items-center space-x-8">
                    <a href="#home"
                        class="text-gray-700 hover:text-blue-600 transition-colors font-medium">Beranda</a>
                    <a href="#products"
                        class="text-gray-700 hover:text-blue-600 transition-colors font-medium">Produk</a>
                    <a href="#about" class="text-gray-700 hover:text-blue-600 transition-colors font-medium">Tentang
                        Kami</a>
                    <a href="#contact"
                        class="text-gray-700 hover:text-blue-600 transition-colors font-medium">Kontak</a>
                    <a href="/admin"
                        class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                        Admin
                    </a>
                </div>

                <!-- Mobile Menu Button -->
                <div class="md:hidden">
                    <button @click="mobileMenuOpen = !mobileMenuOpen" class="text-gray-700 hover:text-blue-600">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path x-show="!mobileMenuOpen" stroke-linecap="round" stroke-linejoin="round"
                                stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                            <path x-show="mobileMenuOpen" stroke-linecap="round" stroke-linejoin="round"
                                stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- Mobile Menu -->
        <div x-show="mobileMenuOpen" x-cloak x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 -translate-y-2" x-transition:enter-end="opacity-100 translate-y-0"
            class="md:hidden bg-white border-t">
            <div class="px-4 pt-2 pb-3 space-y-1">
                <a href="#home"
                    class="block px-3 py-2 text-gray-700 hover:text-blue-600 hover:bg-gray-50 rounded-md">Beranda</a>
                <a href="#products"
                    class="block px-3 py-2 text-gray-700 hover:text-blue-600 hover:bg-gray-50 rounded-md">Produk</a>
                <a href="#about"
                    class="block px-3 py-2 text-gray-700 hover:text-blue-600 hover:bg-gray-50 rounded-md">Tentang
                    Kami</a>
                <a href="#contact"
                    class="block px-3 py-2 text-gray-700 hover:text-blue-600 hover:bg-gray-50 rounded-md">Kontak</a>
                <a href="/admin" class="block px-3 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">Admin</a>
            </div>
        </div>
    </nav>

    @yield('content')

    <!-- Footer -->
    <footer class="bg-gray-900 text-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <!-- About -->
                <div class="col-span-1 md:col-span-2">
                    <div class="flex items-center space-x-3 mb-4">
                        @if ($settings->logo_dark ?? $settings->logo)
                            <img src="{{ $settings->logo_dark_url ?? $settings->logo_url }}"
                                alt="{{ $settings->shop_name }}" class="h-10 w-auto">
                        @endif
                        <span class="text-xl font-bold">{{ $settings->shop_name }}</span>
                    </div>
                    <p class="text-gray-400 mb-4">{{ $settings->description }}</p>

                    <!-- Social Media -->
                    @if ($settings->facebook_url || $settings->instagram_url || $settings->twitter_url || $settings->youtube_url)
                        <div class="flex space-x-4">
                            @if ($settings->facebook_url)
                                <a href="{{ $settings->facebook_url }}" target="_blank"
                                    class="text-gray-400 hover:text-white transition-colors">
                                    <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24">
                                        <path
                                            d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z" />
                                    </svg>
                                </a>
                            @endif
                            @if ($settings->instagram_url)
                                <a href="{{ $settings->instagram_url }}" target="_blank"
                                    class="text-gray-400 hover:text-white transition-colors">
                                    <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24">
                                        <path
                                            d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z" />
                                    </svg>
                                </a>
                            @endif
                            @if ($settings->twitter_url)
                                <a href="{{ $settings->twitter_url }}" target="_blank"
                                    class="text-gray-400 hover:text-white transition-colors">
                                    <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24">
                                        <path
                                            d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z" />
                                    </svg>
                                </a>
                            @endif
                            @if ($settings->youtube_url)
                                <a href="{{ $settings->youtube_url }}" target="_blank"
                                    class="text-gray-400 hover:text-white transition-colors">
                                    <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24">
                                        <path
                                            d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z" />
                                    </svg>
                                </a>
                            @endif
                        </div>
                    @endif
                </div>

                <!-- Quick Links -->
                <div>
                    <h3 class="text-lg font-semibold mb-4">Menu</h3>
                    <ul class="space-y-2">
                        <li><a href="#home" class="text-gray-400 hover:text-white transition-colors">Beranda</a>
                        </li>
                        <li><a href="#products" class="text-gray-400 hover:text-white transition-colors">Produk</a>
                        </li>
                        <li><a href="#about" class="text-gray-400 hover:text-white transition-colors">Tentang
                                Kami</a></li>
                        <li><a href="#contact" class="text-gray-400 hover:text-white transition-colors">Kontak</a>
                        </li>
                    </ul>
                </div>

                <!-- Contact Info -->
                <div>
                    <h3 class="text-lg font-semibold mb-4">Kontak</h3>
                    <ul class="space-y-2 text-gray-400">
                        @if ($settings->address)
                            <li class="flex items-start space-x-2">
                                <svg class="h-5 w-5 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                <span>{{ $settings->address }}, {{ $settings->city }}</span>
                            </li>
                        @endif
                        @if ($settings->phone)
                            <li class="flex items-center space-x-2">
                                <svg class="h-5 w-5 flex-shrink-0" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                </svg>
                                <span>{{ $settings->phone }}</span>
                            </li>
                        @endif
                        @if ($settings->email)
                            <li class="flex items-center space-x-2">
                                <svg class="h-5 w-5 flex-shrink-0" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                </svg>
                                <span>{{ $settings->email }}</span>
                            </li>
                        @endif
                    </ul>
                </div>
            </div>

            <div class="border-t border-gray-800 mt-8 pt-8 text-center text-gray-400">
                <p>&copy; {{ date('Y') }} {{ $settings->shop_name }}. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</body>

</html>
