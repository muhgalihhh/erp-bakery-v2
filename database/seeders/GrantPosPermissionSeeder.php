<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use App\Models\User;

class GrantPosPermissionSeeder extends Seeder
{
  public function run(): void
  {
    // Create view_pos permission if it doesn't exist
    $permission = Permission::firstOrCreate([
      'name' => 'view_pos',
      'guard_name' => 'web'
    ]);

    // Grant permission to super_admin role
    $superAdminRole = Role::where('name', 'super_admin')->first();
    if ($superAdminRole) {
      $superAdminRole->givePermissionTo($permission);
      $this->command->info('✅ view_pos permission granted to super_admin role');
    }

    // Grant permission to cashier role if exists
    $cashierRole = Role::where('name', 'cashier')->first();
    if ($cashierRole) {
      $cashierRole->givePermissionTo($permission);
      $this->command->info('✅ view_pos permission granted to cashier role');
    }

    // Grant directly to admin user
    $admin = User::where('email', 'admin@bakery.com')->first();
    if ($admin) {
      $admin->givePermissionTo($permission);
      $this->command->info('✅ view_pos permission granted to admin@bakery.com');
    }
  }
}
