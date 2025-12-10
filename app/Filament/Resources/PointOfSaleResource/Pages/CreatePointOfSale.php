<?php

namespace App\Filament\Resources\PointOfSaleResource\Pages;

use App\Filament\Resources\PointOfSaleResource;
use Filament\Resources\Pages\CreateRecord;

class CreatePointOfSale extends CreateRecord
{
  protected static string $resource = PointOfSaleResource::class;

  protected function getRedirectUrl(): string
  {
    return $this->getResource()::getUrl('index');
  }
}
