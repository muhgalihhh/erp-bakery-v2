@props(['name', 'show' => false, 'maxWidth' => '2xl', 'closeable' => true])

@php
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
@endphp

<div x-data="{
    show: @entangle($name),
    close() {
        this.show = false;
        @if ($closeable) $wire.set('{{ $name }}', false); @endif
    }
}" x-on:keydown.escape.window="close()" x-show="show" x-cloak
    class="fixed inset-0 overflow-y-auto px-4 py-6 sm:px-0 z-50" style="display: none;">
    {{-- Backdrop --}}
    <div x-show="show" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200"
        x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
        class="fixed inset-0 transform transition-all" @if ($closeable) @click="close()" @endif>
        <div class="absolute inset-0 bg-gray-900/75 backdrop-blur-sm"></div>
    </div>

    {{-- Modal Content --}}
    <div x-show="show" x-transition:enter="ease-out duration-300"
        x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
        x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200"
        x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
        x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
        class="mb-6 bg-white rounded-2xl overflow-hidden shadow-2xl transform transition-all sm:w-full sm:mx-auto {{ $maxWidthClass }}">
        {{ $slot }}
    </div>
</div>
