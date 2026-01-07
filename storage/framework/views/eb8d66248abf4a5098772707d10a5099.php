<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>POS - Kasir | <?php echo e(config('app.name', 'Bakery ERP')); ?></title>
    <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/js/app.js']); ?>
    <?php echo \Livewire\Mechanisms\FrontendAssets\FrontendAssets::styles(); ?>

    <style>
        .pos-gradient {
            background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%);
        }
    </style>
</head>

<body class="bg-gray-100">
    
    <div class="pos-gradient text-white px-4 py-3 shadow-lg sticky top-0 z-50">
        <div class="flex justify-between items-center">
            
            <div class="flex items-center gap-4">
                <a href="<?php echo e(url('/admin/pos')); ?>"
                    class="inline-flex items-center gap-2 px-3 py-2 text-sm font-medium text-white/90 bg-white/10 rounded-lg hover:bg-white/20 transition-all duration-200 backdrop-blur-sm border border-white/20">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    <span class="hidden sm:inline">Kembali ke Admin</span>
                </a>
                <div class="h-8 w-px bg-white/20 hidden sm:block"></div>
                <div class="flex items-center gap-3">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                        xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z">
                        </path>
                    </svg>
                    <div>
                        <h1 class="text-xl font-bold leading-tight">Point of Sale</h1>
                        <p class="text-white/70 text-xs"><?php echo e(config('app.name', 'Bakery ERP')); ?></p>
                    </div>
                </div>
            </div>

            
            <div class="flex items-center gap-4">
                
                <div class="hidden md:block text-right">
                    <p class="text-xs text-white/70"><?php echo e(now()->translatedFormat('l, d F Y')); ?></p>
                    <p class="text-sm font-medium" id="current-time"><?php echo e(now()->format('H:i:s')); ?></p>
                </div>
                <div class="h-8 w-px bg-white/20 hidden md:block"></div>
                
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-full bg-white/20 flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                    </div>
                    <div class="text-right hidden sm:block">
                        <p class="text-xs text-white/70">Kasir</p>
                        <p class="font-semibold text-sm"><?php echo e(auth()->user()->name); ?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    
    <div class="min-h-[calc(100vh-60px)] bg-gray-50">
        <?php
$__split = function ($name, $params = []) {
    return [$name, $params];
};
[$__name, $__params] = $__split('pos.point-of-sale');

$key = null;

$key ??= \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::generateKey('lw-1534492601-0', null);

$__html = app('livewire')->mount($__name, $__params, $key);

echo $__html;

unset($__html);
unset($__name);
unset($__params);
unset($__split);
if (isset($__slots)) unset($__slots);
?>
    </div>

    <?php echo \Livewire\Mechanisms\FrontendAssets\FrontendAssets::scripts(); ?>


    
    <script>
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
        setInterval(updateClock, 1000);
        updateClock();
    </script>
</body>

</html>
<?php /**PATH C:\Materi Kuliah\Ifan\BakerySys v2\bakery-erp\resources\views/pos/index.blade.php ENDPATH**/ ?>