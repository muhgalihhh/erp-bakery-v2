<?php

namespace App\Providers;

use App\Models\User;
use App\Models\PurchaseOrder;
use App\Models\GoodReceipt;
use App\Models\VendorPayment;
use App\Models\StockAdjustment;
use App\Models\Customer;
use App\Models\SalesOrder;
use App\Observers\PurchaseOrderObserver;
use App\Observers\GoodReceiptObserver;
use App\Observers\VendorPaymentObserver;
use App\Observers\StockAdjustmentObserver;
use App\Observers\CustomerObserver;
use App\Observers\SalesOrderObserver;
use App\Policies\UserPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;
use App\Livewire\Pos\PointOfSale;

class AppServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        User::class => UserPolicy::class,
    ];

    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Register policies
        Gate::policy(User::class, UserPolicy::class);

        // Register Livewire components manually
        Livewire::component('pos.point-of-sale', PointOfSale::class);

        // Register observers for auto-generating unique codes
        PurchaseOrder::observe(PurchaseOrderObserver::class);
        GoodReceipt::observe(GoodReceiptObserver::class);
        VendorPayment::observe(VendorPaymentObserver::class);
        StockAdjustment::observe(StockAdjustmentObserver::class);

        // Sales & CRM observers
        Customer::observe(CustomerObserver::class);
        SalesOrder::observe(SalesOrderObserver::class);
    }
}
