<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>POS - Kasir</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    <style>
        .pos-gradient {
            background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%);
        }
    </style>
</head>

<body class="bg-gray-100">
    <div class="pos-gradient text-white p-4 shadow-md">
        <div class="container mx-auto flex justify-between items-center">
            <div class="flex items-center gap-3">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                    xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z">
                    </path>
                </svg>
                <div>
                    <h1 class="text-2xl font-bold">Point of Sale</h1>
                    <p class="text-white/80 text-sm">{{ config('app.name', 'Bakery ERP') }}</p>
                </div>
            </div>
            <div class="text-right">
                <p class="text-sm text-white/80">Kasir</p>
                <p class="font-bold text-lg">{{ auth()->user()->name }}</p>
            </div>
        </div>
    </div>

    <div class="min-h-screen bg-gray-50">
        @livewire('pos.point-of-sale')
    </div>

    @livewireScripts
</body>

</html>
