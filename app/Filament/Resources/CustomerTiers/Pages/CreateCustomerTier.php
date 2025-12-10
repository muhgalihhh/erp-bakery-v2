<?php

namespace App\Filament\Resources\CustomerTiers\Pages;

use App\Filament\Resources\CustomerTiers\CustomerTierResource;
use Filament\Resources\Pages\CreateRecord;

class CreateCustomerTier extends CreateRecord
{
    protected static string $resource = CustomerTierResource::class;
}
