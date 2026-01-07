<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Point of Sale') - {{ config('app.name', 'Bakery ERP') }}</title>

    {{-- Vite Assets --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- Alpine.js --}}
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    {{-- Livewire Styles --}}
    @livewireStyles

    {{-- Custom POS Styles --}}
    <style>
        /* Custom styles untuk POS */
        .pos-gradient {
            background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%);
        }

        .pos-gradient-success {
            background: linear-gradient(135deg, #065f46 0%, #10b981 100%);
        }

        .pos-card {
            @apply bg-white rounded-xl shadow-sm border border-gray-200 transition-all duration-200;
        }

        .pos-card:hover {
            @apply shadow-md border-blue-200;
        }

        .pos-button {
            @apply px-4 py-2 rounded-lg font-medium transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2;
        }

        .pos-button-primary {
            @apply bg-blue-600 text-white hover:bg-blue-700 focus:ring-blue-500;
        }

        .pos-button-success {
            @apply bg-green-600 text-white hover:bg-green-700 focus:ring-green-500;
        }

        .pos-button-danger {
            @apply bg-red-600 text-white hover:bg-red-700 focus:ring-red-500;
        }

        .pos-button-secondary {
            @apply bg-gray-200 text-gray-700 hover:bg-gray-300 focus:ring-gray-500;
        }

        .pos-input {
            @apply w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent;
        }

        /* Scrollbar custom */
        .custom-scrollbar::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }

        .custom-scrollbar::-webkit-scrollbar-track {
            background: #f1f5f9;
            border-radius: 10px;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 10px;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }

        /* Alpine cloak */
        [x-cloak] {
            display: none !important;
        }

        /* Animation */
        @keyframes slideInRight {
            from {
                transform: translateX(100%);
                opacity: 0;
            }

            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
        }

        .slide-in-right {
            animation: slideInRight 0.3s ease-out;
        }

        .fade-in {
            animation: fadeIn 0.3s ease-out;
        }

        /* Print styles */
        @media print {
            body * {
                visibility: hidden;
            }

            .print-area,
            .print-area * {
                visibility: visible;
            }

            .print-area {
                position: absolute;
                left: 0;
                top: 0;
            }
        }
    </style>

    @stack('styles')
</head>

<body class="bg-gray-50 overflow-hidden">
    @yield('content')

    {{-- Livewire Scripts --}}
    @livewireScripts

    {{-- Custom Scripts --}}
    <script>
        // Real-time clock
        function updateClock() {
            const now = new Date();
            const timeString = now.toLocaleTimeString('id-ID', {
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit'
            });
            const clockElement = document.getElementById('current-time');
            if (clockElement) {
                clockElement.textContent = timeString;
            }
        }

        // Update clock every second
        setInterval(updateClock, 1000);
        updateClock();

        // Keyboard shortcuts
        document.addEventListener('keydown', function(e) {
            // F9 - Quick checkout
            if (e.key === 'F9') {
                e.preventDefault();
                @if (isset($livewire))
                    @this.set('showPaymentModal', true);
                @endif
            }

            // F8 - Clear cart
            if (e.key === 'F8') {
                e.preventDefault();
                @if (isset($livewire))
                    @this.call('confirmClearCart');
                @endif
            }
        });

        // Notification system
        window.addEventListener('notify', event => {
            const detail = event.detail[0] || event.detail;
            showNotification(detail.type, detail.message);
        });

        function showNotification(type, message) {
            // Create notification element
            const notification = document.createElement('div');
            notification.className =
                `fixed top-4 right-4 z-50 max-w-sm p-4 rounded-lg shadow-lg fade-in ${getNotificationColor(type)}`;
            notification.innerHTML = `
                <div class="flex items-start gap-3">
                    <div class="flex-shrink-0">
                        ${getNotificationIcon(type)}
                    </div>
                    <div class="flex-1">
                        <p class="font-medium">${message}</p>
                    </div>
                    <button onclick="this.parentElement.parentElement.remove()" class="flex-shrink-0 text-gray-400 hover:text-gray-600">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                        </svg>
                    </button>
                </div>
            `;

            document.body.appendChild(notification);

            // Auto remove after 3 seconds
            setTimeout(() => {
                notification.style.opacity = '0';
                setTimeout(() => notification.remove(), 300);
            }, 3000);
        }

        function getNotificationColor(type) {
            const colors = {
                'success': 'bg-green-50 text-green-800 border border-green-200',
                'error': 'bg-red-50 text-red-800 border border-red-200',
                'warning': 'bg-yellow-50 text-yellow-800 border border-yellow-200',
                'info': 'bg-blue-50 text-blue-800 border border-blue-200'
            };
            return colors[type] || colors.info;
        }

        function getNotificationIcon(type) {
            const icons = {
                'success': '<svg class="w-5 h-5 text-green-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>',
                'error': '<svg class="w-5 h-5 text-red-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path></svg>',
                'warning': '<svg class="w-5 h-5 text-yellow-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg>',
                'info': '<svg class="w-5 h-5 text-blue-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path></svg>'
            };
            return icons[type] || icons.info;
        }
    </script>

    @stack('scripts')
</body>

</html>
