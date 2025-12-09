<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Vendor extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'vendor_code',
        'name',
        'contact_person',
        'phone',
        'email',
        'address',
        'tax_id',
        'payment_terms_days',
        'bank_name',
        'bank_account_number',
        'bank_account_name',
        'is_active',
        'notes',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'payment_terms_days' => 'integer',
    ];

    /**
     * Get all purchase orders for this vendor
     */
    public function purchaseOrders(): HasMany
    {
        return $this->hasMany(PurchaseOrder::class);
    }

    /**
     * Get all payments for this vendor
     */
    public function payments(): HasMany
    {
        return $this->hasMany(VendorPayment::class);
    }

    /**
     * Get active purchase orders
     */
    public function activePurchaseOrders(): HasMany
    {
        return $this->purchaseOrders()->whereIn('status', ['pending', 'approved']);
    }

    /**
     * Scope for active vendors only
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
