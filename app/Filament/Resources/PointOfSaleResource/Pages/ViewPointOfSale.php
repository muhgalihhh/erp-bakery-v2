<?php

namespace App\Filament\Resources\PointOfSaleResource\Pages;

use App\Filament\Resources\PointOfSaleResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewPointOfSale extends ViewRecord
{
  protected static string $resource = PointOfSaleResource::class;

  protected function getHeaderActions(): array
  {
    return [
      EditAction::make(),
    ];
  }
}
