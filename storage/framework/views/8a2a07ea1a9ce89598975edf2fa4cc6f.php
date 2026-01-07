<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['name', 'show' => false, 'maxWidth' => '2xl', 'closeable' => true]));

foreach ($attributes->all() as $__key => $__value) {
    if (in_array($__key, $__propNames)) {
        $$__key = $$__key ?? $__value;
    } else {
        $__newAttributes[$__key] = $__value;
    }
}

$attributes = new \Illuminate\View\ComponentAttributeBag($__newAttributes);

unset($__propNames);
unset($__newAttributes);

foreach (array_filter((['name', 'show' => false, 'maxWidth' => '2xl', 'closeable' => true]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php
    $maxWidthClass = [
        'sm' => 'sm:max-w-sm',
        'md' => 'sm:max-w-md',
        'lg' => 'sm:max-w-lg',
        'xl' => 'sm:max-w-xl',
        '2xl' => 'sm:max-w-2xl',
        '3xl' => 'sm:max-w-3xl',
        '4xl' => 'sm:max-w-4xl',
        '5xl' => 'sm:max-w-5xl',
        '6xl' => 'sm:max-w-6xl',
        '7xl' => 'sm:max-w-7xl',
    ][$maxWidth];
?>

<div x-data="{
    show: <?php if ((object) ($name) instanceof \Livewire\WireDirective) : ?>window.Livewire.find('<?php echo e($__livewire->getId()); ?>').entangle('<?php echo e($name->value()); ?>')<?php echo e($name->hasModifier('live') ? '.live' : ''); ?><?php else : ?>window.Livewire.find('<?php echo e($__livewire->getId()); ?>').entangle('<?php echo e($name); ?>')<?php endif; ?>,
    close() {
        this.show = false;
        <?php if($closeable): ?> $wire.set('<?php echo e($name); ?>', false); <?php endif; ?>
    }
}" x-on:keydown.escape.window="close()" x-show="show" x-cloak
    class="fixed inset-0 overflow-y-auto px-4 py-6 sm:px-0 z-50" style="display: none;">
    
    <div x-show="show" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200"
        x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
        class="fixed inset-0 transform transition-all" <?php if($closeable): ?> @click="close()" <?php endif; ?>>
        <div class="absolute inset-0 bg-gray-900/75 backdrop-blur-sm"></div>
    </div>

    
    <div x-show="show" x-transition:enter="ease-out duration-300"
        x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
        x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200"
        x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
        x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
        class="mb-6 bg-white rounded-2xl overflow-hidden shadow-2xl transform transition-all sm:w-full sm:mx-auto <?php echo e($maxWidthClass); ?>">
        <?php echo e($slot); ?>

    </div>
</div>
<?php /**PATH C:\Materi Kuliah\Ifan\BakerySys v2\bakery-erp\resources\views/components/pos/modal.blade.php ENDPATH**/ ?>