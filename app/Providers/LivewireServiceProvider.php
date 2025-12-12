<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;

class LivewireServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Register Livewire components manually
        // This ensures components are available in both web and Filament contexts
        Livewire::component('pos.point-of-sale', \App\Livewire\Pos\PointOfSale::class);
    }
}
