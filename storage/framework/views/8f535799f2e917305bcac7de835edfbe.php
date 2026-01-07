<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'variant' => 'primary', // primary, secondary, success, danger, warning
    'size' => 'md', // sm, md, lg
    'type' => 'button',
]));

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

foreach (array_filter(([
    'variant' => 'primary', // primary, secondary, success, danger, warning
    'size' => 'md', // sm, md, lg
    'type' => 'button',
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php
    $baseClasses =
        'inline-flex items-center justify-center gap-2 font-medium rounded-lg transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed';

    $variantClasses = [
        'primary' => 'bg-blue-600 hover:bg-blue-700 text-white focus:ring-blue-500',
        'secondary' => 'bg-gray-200 hover:bg-gray-300 text-gray-700 focus:ring-gray-500',
        'success' => 'bg-green-600 hover:bg-green-700 text-white focus:ring-green-500',
        'danger' => 'bg-red-600 hover:bg-red-700 text-white focus:ring-red-500',
        'warning' => 'bg-yellow-500 hover:bg-yellow-600 text-white focus:ring-yellow-500',
    ][$variant];

    $sizeClasses = [
        'sm' => 'px-3 py-1.5 text-sm',
        'md' => 'px-4 py-2 text-base',
        'lg' => 'px-6 py-3 text-lg',
    ][$size];
?>

<button type="<?php echo e($type); ?>"
    <?php echo e($attributes->merge(['class' => $baseClasses . ' ' . $variantClasses . ' ' . $sizeClasses])); ?>>
    <?php echo e($slot); ?>

</button>
<?php /**PATH C:\Materi Kuliah\Ifan\BakerySys v2\bakery-erp\resources\views/components/pos/button.blade.php ENDPATH**/ ?>