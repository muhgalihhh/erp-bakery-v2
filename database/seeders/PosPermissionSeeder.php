<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PosPermissionSeeder extends Seeder
{
  /**
   * Run the database seeds.
   *
   * Creates permissions for POS functionality:
   * - page_Pos: Basic access to POS page
   * - view_pos: View POS (alias)
   * - create_pos_transaction: Create new sales
   * - apply_pos_discount: Apply manual discounts
   * - void_pos_transaction: Void/cancel sales
   * - view_all_pos_transactions: View all cashier transactions
   * - view_pos_reports: Access POS reports
   */
  public function run(): void
  {
    $guard = 'web';

    // Define POS permissions
    $permissions = [
      // Filament Shield page permission
      'page_Pos',

      // Granular POS permissions
      'view_pos',
      'create_pos_transaction',
      'apply_pos_discount',
      'void_pos_transaction',
      'view_all_pos_transactions',
      'view_pos_reports',
    ];

    // Create permissions
    foreach ($permissions as $permission) {
      Permission::firstOrCreate([
        'name' => $permission,
        'guard_name' => $guard,
      ]);
    }

    $this->command->info('POS permissions created successfully!');

    // Assign all POS permissions to super_admin role
    $superAdmin = Role::where('name', 'super_admin')->first();
    if ($superAdmin) {
      $superAdmin->givePermissionTo($permissions);
      $this->command->info('All POS permissions assigned to super_admin role.');
    }

    // Create Kasir role with basic POS permissions
    $kasirRole = Role::firstOrCreate([
      'name' => 'kasir',
      'guard_name' => $guard,
    ]);

    $kasirPermissions = [
      'page_Pos',
      'view_pos',
      'create_pos_transaction',
    ];

    $kasirRole->syncPermissions($kasirPermissions);
    $this->command->info('Kasir role created with basic POS permissions.');

    // Create Supervisor Kasir role with additional permissions
    $supervisorRole = Role::firstOrCreate([
      'name' => 'supervisor_kasir',
      'guard_name' => $guard,
    ]);

    $supervisorPermissions = [
      'page_Pos',
      'view_pos',
      'create_pos_transaction',
      'apply_pos_discount',
      'void_pos_transaction',
      'view_all_pos_transactions',
      'view_pos_reports',
    ];

    $supervisorRole->syncPermissions($supervisorPermissions);
    $this->command->info('Supervisor Kasir role created with full POS permissions.');
  }
}
