<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class InventoryControllerSeeder extends Seeder
{
  /**
   * Run the database seeds.
   */
  public function run(): void
  {
    // Create Inventory Controller Role
    $inventoryController = Role::firstOrCreate(
      ['name' => 'inventory_controller'],
      ['guard_name' => 'web']
    );

    $this->command->info('✅ Inventory Controller role created');
    $this->command->info('   Permissions will be assigned manually by admin via UI');

    // Create a test Inventory Controller user
    $user = User::firstOrCreate(
      ['email' => 'inventory@bakery.com'],
      [
        'name' => 'Siti Rahayu (Inventory Controller)',
        'password' => Hash::make('password'),
        'email_verified_at' => now(),
      ]
    );

    // Assign role to user
    $user->assignRole('inventory_controller');

    $this->command->info('✅ Test user created: inventory@bakery.com / password');
  }
}
