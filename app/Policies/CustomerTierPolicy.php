<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\CustomerTier;
use Illuminate\Auth\Access\HandlesAuthorization;

class CustomerTierPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:CustomerTier');
    }

    public function view(AuthUser $authUser, CustomerTier $customerTier): bool
    {
        return $authUser->can('View:CustomerTier');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:CustomerTier');
    }

    public function update(AuthUser $authUser, CustomerTier $customerTier): bool
    {
        return $authUser->can('Update:CustomerTier');
    }

    public function delete(AuthUser $authUser, CustomerTier $customerTier): bool
    {
        return $authUser->can('Delete:CustomerTier');
    }

    public function restore(AuthUser $authUser, CustomerTier $customerTier): bool
    {
        return $authUser->can('Restore:CustomerTier');
    }

    public function forceDelete(AuthUser $authUser, CustomerTier $customerTier): bool
    {
        return $authUser->can('ForceDelete:CustomerTier');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:CustomerTier');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:CustomerTier');
    }

    public function replicate(AuthUser $authUser, CustomerTier $customerTier): bool
    {
        return $authUser->can('Replicate:CustomerTier');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:CustomerTier');
    }

}