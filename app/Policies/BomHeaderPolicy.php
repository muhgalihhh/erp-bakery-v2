<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\BomHeader;
use Illuminate\Auth\Access\HandlesAuthorization;

class BomHeaderPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:BomHeader');
    }

    public function view(AuthUser $authUser, BomHeader $bomHeader): bool
    {
        return $authUser->can('View:BomHeader');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:BomHeader');
    }

    public function update(AuthUser $authUser, BomHeader $bomHeader): bool
    {
        return $authUser->can('Update:BomHeader');
    }

    public function delete(AuthUser $authUser, BomHeader $bomHeader): bool
    {
        return $authUser->can('Delete:BomHeader');
    }

    public function restore(AuthUser $authUser, BomHeader $bomHeader): bool
    {
        return $authUser->can('Restore:BomHeader');
    }

    public function forceDelete(AuthUser $authUser, BomHeader $bomHeader): bool
    {
        return $authUser->can('ForceDelete:BomHeader');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:BomHeader');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:BomHeader');
    }

    public function replicate(AuthUser $authUser, BomHeader $bomHeader): bool
    {
        return $authUser->can('Replicate:BomHeader');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:BomHeader');
    }

}