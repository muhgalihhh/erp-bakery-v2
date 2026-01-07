<?php

namespace App\Filament\Resources\ShopSettingResource\Pages;

use App\Filament\Resources\ShopSettingResource;
use App\Models\ShopSetting;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageShopSettings extends ManageRecords
{
  protected static string $resource = ShopSettingResource::class;

  protected function getHeaderActions(): array
  {
    return [
      // No create action for single record
    ];
  }

  // Automatically load the single record
  public function mount(): void
  {
    parent::mount();

    // Ensure we always have a settings record
    $settings = ShopSetting::getSettings();

    // If no records in table, redirect to create the first one
    if (!ShopSetting::first()) {
      $this->redirect(static::getResource()::getUrl('index'));
    }
  }
}
