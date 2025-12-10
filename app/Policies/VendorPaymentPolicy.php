<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\VendorPayment;
use Illuminate\Auth\Access\HandlesAuthorization;

class VendorPaymentPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:VendorPayment');
    }

    public function view(AuthUser $authUser, VendorPayment $vendorPayment): bool
    {
        return $authUser->can('View:VendorPayment');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:VendorPayment');
    }

    public function update(AuthUser $authUser, VendorPayment $vendorPayment): bool
    {
        return $authUser->can('Update:VendorPayment');
    }

    public function delete(AuthUser $authUser, VendorPayment $vendorPayment): bool
    {
        return $authUser->can('Delete:VendorPayment');
    }

    public function restore(AuthUser $authUser, VendorPayment $vendorPayment): bool
    {
        return $authUser->can('Restore:VendorPayment');
    }

    public function forceDelete(AuthUser $authUser, VendorPayment $vendorPayment): bool
    {
        return $authUser->can('ForceDelete:VendorPayment');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:VendorPayment');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:VendorPayment');
    }

    public function replicate(AuthUser $authUser, VendorPayment $vendorPayment): bool
    {
        return $authUser->can('Replicate:VendorPayment');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:VendorPayment');
    }

}