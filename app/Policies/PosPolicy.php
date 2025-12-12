<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

/**
 * POS Policy - Manages access to Point of Sale functionality
 *
 * Permissions managed by Filament Shield:
 * - view_pos: Can access POS page
 * - create_pos_transaction: Can create new sales transactions
 * - apply_discount: Can apply manual discounts
 * - void_transaction: Can void/cancel transactions
 * - view_all_transactions: Can view all cashier transactions
 */
class PosPolicy
{
  use HandlesAuthorization;

  /**
   * Determine if user can view/access POS
   */
  public function view(User $user): bool
  {
    return $user->can('view_pos');
  }

  /**
   * Determine if user can create transactions
   */
  public function create(User $user): bool
  {
    return $user->can('create_pos_transaction');
  }

  /**
   * Determine if user can apply manual discounts
   */
  public function applyDiscount(User $user): bool
  {
    return $user->can('apply_pos_discount');
  }

  /**
   * Determine if user can void transactions
   */
  public function voidTransaction(User $user): bool
  {
    return $user->can('void_pos_transaction');
  }

  /**
   * Determine if user can view all transactions (not just their own)
   */
  public function viewAll(User $user): bool
  {
    return $user->can('view_all_pos_transactions');
  }

  /**
   * Determine if user can access POS reports
   */
  public function viewReports(User $user): bool
  {
    return $user->can('view_pos_reports');
  }
}
