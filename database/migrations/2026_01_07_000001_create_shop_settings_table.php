<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
  /**
   * Run the migrations.
   */
  public function up(): void
  {
    Schema::create('shop_settings', function (Blueprint $table) {
      $table->id();
      $table->string('shop_name')->default('Bakery Shop');
      $table->string('tagline')->nullable();
      $table->text('description')->nullable();
      $table->text('about_us')->nullable();

      // Contact Information
      $table->string('email')->nullable();
      $table->string('phone')->nullable();
      $table->string('whatsapp')->nullable();

      // Address
      $table->text('address')->nullable();
      $table->string('city')->nullable();
      $table->string('postal_code')->nullable();

      // Social Media
      $table->string('facebook_url')->nullable();
      $table->string('instagram_url')->nullable();
      $table->string('twitter_url')->nullable();
      $table->string('youtube_url')->nullable();

      // Images
      $table->string('logo')->nullable();
      $table->string('logo_dark')->nullable();
      $table->string('hero_image')->nullable();
      $table->string('about_image')->nullable();

      // Business Hours
      $table->json('business_hours')->nullable();

      // SEO
      $table->string('meta_title')->nullable();
      $table->text('meta_description')->nullable();
      $table->text('meta_keywords')->nullable();

      // Additional Settings
      $table->boolean('is_open')->default(true);
      $table->text('announcement')->nullable();

      $table->timestamps();
    });

    // Insert default record
    DB::table('shop_settings')->insert([
      'shop_name' => 'Bakery Shop',
      'tagline' => 'Freshly Baked with Love',
      'description' => 'Kami menyediakan berbagai macam roti dan kue berkualitas tinggi yang dibuat dengan bahan-bahan pilihan.',
      'about_us' => 'Toko roti kami telah berdiri sejak tahun 2020 dan terus berkomitmen untuk memberikan produk terbaik kepada pelanggan.',
      'email' => 'info@bakeryshop.com',
      'phone' => '021-1234567',
      'whatsapp' => '628123456789',
      'address' => 'Jl. Contoh No. 123',
      'city' => 'Jakarta',
      'postal_code' => '12345',
      'business_hours' => json_encode([
            'monday' => ['open' => '08:00', 'close' => '20:00'],
            'tuesday' => ['open' => '08:00', 'close' => '20:00'],
            'wednesday' => ['open' => '08:00', 'close' => '20:00'],
            'thursday' => ['open' => '08:00', 'close' => '20:00'],
            'friday' => ['open' => '08:00', 'close' => '20:00'],
            'saturday' => ['open' => '08:00', 'close' => '21:00'],
            'sunday' => ['open' => '09:00', 'close' => '18:00'],
          ]),
      'is_open' => true,
      'created_at' => now(),
      'updated_at' => now(),
    ]);
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('shop_settings');
  }
};
