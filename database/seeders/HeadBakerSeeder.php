<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class HeadBakerSeeder extends Seeder
{
  /**
   * Run the database seeds.
   */
  public function run(): void
  {
    // Create Head Baker Role
    $headBaker = Role::firstOrCreate(
      ['name' => 'head_baker'],
      ['guard_name' => 'web']
    );

    // Define permissions for Head Baker
    $permissions = [
      // Product permissions (can manage all products for production)
      'view_any_product',
      'view_product',
      'create_product',
      'update_product',
      // Note: No delete_product - only admin can delete

      // BOM Header permissions (full access to recipes)
      'view_any_bom_header',
      'view_bom_header',
      'create_bom_header',
      'update_bom_header',
      'delete_bom_header',

      // Chart of Accounts - view only (to understand costs)
      'view_any_chart_of_account',
      'view_chart_of_account',
    ];

    // Sync permissions to the role
    $existingPermissions = Permission::whereIn('name', $permissions)->get();
    $headBaker->syncPermissions($existingPermissions);

    // Create a test Head Baker user
    $user = User::firstOrCreate(
      ['email' => 'baker@bakery.com'],
      [
        'name' => 'Budi Santoso (Head Baker)',
        'password' => Hash::make('password'),
        'email_verified_at' => now(),
      ]
    );

    // Assign role to user
    $user->assignRole('head_baker');

    $this->command->info('✅ Head Baker role created with ' . $existingPermissions->count() . ' permissions');
    $this->command->info('✅ Test user created: baker@bakery.com / password');
  }
}
