<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class ShopSetting extends Model
{
  protected $fillable = [
    'shop_name',
    'tagline',
    'description',
    'about_us',
    'email',
    'phone',
    'whatsapp',
    'address',
    'city',
    'postal_code',
    'facebook_url',
    'instagram_url',
    'twitter_url',
    'youtube_url',
    'logo',
    'logo_dark',
    'hero_image',
    'about_image',
    'business_hours',
    'meta_title',
    'meta_description',
    'meta_keywords',
    'is_open',
    'announcement',
  ];

  protected $casts = [
    'business_hours' => 'array',
    'is_open' => 'boolean',
  ];

  /**
   * Get the single shop settings record
   */
  public static function getSettings()
  {
    $settings = static::first();

    if (!$settings) {
      $settings = static::create([
        'shop_name' => 'Bakery Shop',
        'tagline' => 'Freshly Baked with Love',
      ]);
    }

    return $settings;
  }

  /**
   * Get logo URL
   */
  public function getLogoUrlAttribute()
  {
    return $this->logo ? Storage::url($this->logo) : null;
  }

  /**
   * Get dark logo URL
   */
  public function getLogoDarkUrlAttribute()
  {
    return $this->logo_dark ? Storage::url($this->logo_dark) : null;
  }

  /**
   * Get hero image URL
   */
  public function getHeroImageUrlAttribute()
  {
    return $this->hero_image ? Storage::url($this->hero_image) : null;
  }

  /**
   * Get about image URL
   */
  public function getAboutImageUrlAttribute()
  {
    return $this->about_image ? Storage::url($this->about_image) : null;
  }

  /**
   * Get formatted phone number for WhatsApp link
   */
  public function getWhatsappLinkAttribute()
  {
    if (!$this->whatsapp) {
      return null;
    }

    $number = preg_replace('/[^0-9]/', '', $this->whatsapp);
    return "https://wa.me/{$number}";
  }

  /**
   * Get business hours for specific day
   */
  public function getBusinessHours($day = null)
  {
    if (!$this->business_hours) {
      return null;
    }

    $day = $day ?? strtolower(now()->format('l'));

    return $this->business_hours[$day] ?? null;
  }

  /**
   * Check if shop is currently open
   */
  public function isCurrentlyOpen()
  {
    if (!$this->is_open) {
      return false;
    }

    $currentDay = strtolower(now()->format('l'));
    $hours = $this->getBusinessHours($currentDay);

    if (!$hours) {
      return false;
    }

    $currentTime = now()->format('H:i');
    return $currentTime >= $hours['open'] && $currentTime <= $hours['close'];
  }
}
