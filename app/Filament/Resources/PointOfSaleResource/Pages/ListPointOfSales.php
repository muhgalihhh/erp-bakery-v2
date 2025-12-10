<?php

namespace App\Filament\Resources\PointOfSaleResource\Pages;

use App\Filament\Resources\PointOfSaleResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListPointOfSales extends ListRecords
{
  protected static string $resource = PointOfSaleResource::class;

  protected function getHeaderActions(): array
  {
    return [
      CreateAction::make()
        ->label('New Transaction')
        ->icon('heroicon-o-plus-circle'),
    ];
  }
}
