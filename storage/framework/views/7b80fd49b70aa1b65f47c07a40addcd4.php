
<?php if (isset($component)) { $__componentOriginale85fd4c76a37af3451433de183dae210 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginale85fd4c76a37af3451433de183dae210 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.pos.modal','data' => ['name' => 'showClearCartModal','maxWidth' => 'md']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('pos.modal'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'showClearCartModal','maxWidth' => 'md']); ?>
    <?php if (isset($component)) { $__componentOriginalf6d021953c3d5194ffe515f60f331b06 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalf6d021953c3d5194ffe515f60f331b06 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.pos.modal-header','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('pos.modal-header'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
        <div class="flex items-center gap-2 text-red-600">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
            </svg>
            Konfirmasi Hapus Keranjang
        </div>
     <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalf6d021953c3d5194ffe515f60f331b06)): ?>
<?php $attributes = $__attributesOriginalf6d021953c3d5194ffe515f60f331b06; ?>
<?php unset($__attributesOriginalf6d021953c3d5194ffe515f60f331b06); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalf6d021953c3d5194ffe515f60f331b06)): ?>
<?php $component = $__componentOriginalf6d021953c3d5194ffe515f60f331b06; ?>
<?php unset($__componentOriginalf6d021953c3d5194ffe515f60f331b06); ?>
<?php endif; ?>

    <?php if (isset($component)) { $__componentOriginal688aa919556cc8d3fd57bcfebd2779c1 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal688aa919556cc8d3fd57bcfebd2779c1 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.pos.modal-body','data' => ['class' => 'text-center py-6']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('pos.modal-body'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'text-center py-6']); ?>
        <div class="mb-4">
            <svg class="w-16 h-16 mx-auto text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
            </svg>
        </div>
        <h3 class="text-lg font-bold text-gray-900 mb-2">Yakin ingin mengosongkan keranjang?</h3>
        <p class="text-gray-600 text-sm">Semua item di keranjang akan dihapus. Tindakan ini tidak dapat dibatalkan.</p>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!empty($cart)): ?>
            <div class="mt-4 p-3 bg-gray-50 rounded-lg text-left">
                <p class="text-xs font-medium text-gray-700 mb-2">Item yang akan dihapus:</p>
                <ul class="text-xs text-gray-600 space-y-1 max-h-32 overflow-y-auto custom-scrollbar">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $cart; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <li class="flex justify-between">
                            <span><?php echo e($item['name']); ?></span>
                            <span class="font-medium"><?php echo e($item['quantity']); ?>x</span>
                        </li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </ul>
            </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
     <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal688aa919556cc8d3fd57bcfebd2779c1)): ?>
<?php $attributes = $__attributesOriginal688aa919556cc8d3fd57bcfebd2779c1; ?>
<?php unset($__attributesOriginal688aa919556cc8d3fd57bcfebd2779c1); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal688aa919556cc8d3fd57bcfebd2779c1)): ?>
<?php $component = $__componentOriginal688aa919556cc8d3fd57bcfebd2779c1; ?>
<?php unset($__componentOriginal688aa919556cc8d3fd57bcfebd2779c1); ?>
<?php endif; ?>

    <?php if (isset($component)) { $__componentOriginal9d1ae8b1114f77686c9d201c1eb62ef1 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9d1ae8b1114f77686c9d201c1eb62ef1 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.pos.modal-footer','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('pos.modal-footer'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
        <?php if (isset($component)) { $__componentOriginal12393518255b06fa15daa7b187a00e6c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal12393518255b06fa15daa7b187a00e6c = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.pos.button','data' => ['variant' => 'secondary','@click' => 'close()']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('pos.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['variant' => 'secondary','@click' => 'close()']); ?>
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
            Batal
         <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal12393518255b06fa15daa7b187a00e6c)): ?>
<?php $attributes = $__attributesOriginal12393518255b06fa15daa7b187a00e6c; ?>
<?php unset($__attributesOriginal12393518255b06fa15daa7b187a00e6c); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal12393518255b06fa15daa7b187a00e6c)): ?>
<?php $component = $__componentOriginal12393518255b06fa15daa7b187a00e6c; ?>
<?php unset($__componentOriginal12393518255b06fa15daa7b187a00e6c); ?>
<?php endif; ?>
        <?php if (isset($component)) { $__componentOriginal12393518255b06fa15daa7b187a00e6c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal12393518255b06fa15daa7b187a00e6c = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.pos.button','data' => ['variant' => 'danger','wire:click' => 'clearCart']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('pos.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['variant' => 'danger','wire:click' => 'clearCart']); ?>
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
            </svg>
            Ya, Kosongkan
         <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal12393518255b06fa15daa7b187a00e6c)): ?>
<?php $attributes = $__attributesOriginal12393518255b06fa15daa7b187a00e6c; ?>
<?php unset($__attributesOriginal12393518255b06fa15daa7b187a00e6c); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal12393518255b06fa15daa7b187a00e6c)): ?>
<?php $component = $__componentOriginal12393518255b06fa15daa7b187a00e6c; ?>
<?php unset($__componentOriginal12393518255b06fa15daa7b187a00e6c); ?>
<?php endif; ?>
     <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal9d1ae8b1114f77686c9d201c1eb62ef1)): ?>
<?php $attributes = $__attributesOriginal9d1ae8b1114f77686c9d201c1eb62ef1; ?>
<?php unset($__attributesOriginal9d1ae8b1114f77686c9d201c1eb62ef1); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal9d1ae8b1114f77686c9d201c1eb62ef1)): ?>
<?php $component = $__componentOriginal9d1ae8b1114f77686c9d201c1eb62ef1; ?>
<?php unset($__componentOriginal9d1ae8b1114f77686c9d201c1eb62ef1); ?>
<?php endif; ?>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginale85fd4c76a37af3451433de183dae210)): ?>
<?php $attributes = $__attributesOriginale85fd4c76a37af3451433de183dae210; ?>
<?php unset($__attributesOriginale85fd4c76a37af3451433de183dae210); ?>
<?php endif; ?>
<?php if (isset($__componentOriginale85fd4c76a37af3451433de183dae210)): ?>
<?php $component = $__componentOriginale85fd4c76a37af3451433de183dae210; ?>
<?php unset($__componentOriginale85fd4c76a37af3451433de183dae210); ?>
<?php endif; ?>
<?php /**PATH C:\Materi Kuliah\Ifan\BakerySys v2\bakery-erp\resources\views/livewire/pos/partials/clear-cart-modal.blade.php ENDPATH**/ ?>