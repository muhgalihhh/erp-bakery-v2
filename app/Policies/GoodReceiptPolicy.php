<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\GoodReceipt;
use Illuminate\Auth\Access\HandlesAuthorization;

class GoodReceiptPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:GoodReceipt');
    }

    public function view(AuthUser $authUser, GoodReceipt $goodReceipt): bool
    {
        return $authUser->can('View:GoodReceipt');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:GoodReceipt');
    }

    public function update(AuthUser $authUser, GoodReceipt $goodReceipt): bool
    {
        return $authUser->can('Update:GoodReceipt');
    }

    public function delete(AuthUser $authUser, GoodReceipt $goodReceipt): bool
    {
        return $authUser->can('Delete:GoodReceipt');
    }

    public function restore(AuthUser $authUser, GoodReceipt $goodReceipt): bool
    {
        return $authUser->can('Restore:GoodReceipt');
    }

    public function forceDelete(AuthUser $authUser, GoodReceipt $goodReceipt): bool
    {
        return $authUser->can('ForceDelete:GoodReceipt');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:GoodReceipt');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:GoodReceipt');
    }

    public function replicate(AuthUser $authUser, GoodReceipt $goodReceipt): bool
    {
        return $authUser->can('Replicate:GoodReceipt');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:GoodReceipt');
    }

}