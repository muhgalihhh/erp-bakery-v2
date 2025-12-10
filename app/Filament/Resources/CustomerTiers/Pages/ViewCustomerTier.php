<?php

namespace App\Filament\Resources\CustomerTiers\Pages;

use App\Filament\Resources\CustomerTiers\CustomerTierResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewCustomerTier extends ViewRecord
{
    protected static string $resource = CustomerTierResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
