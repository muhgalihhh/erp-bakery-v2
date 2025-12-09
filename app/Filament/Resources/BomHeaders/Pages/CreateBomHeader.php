<?php

namespace App\Filament\Resources\BomHeaders\Pages;

use App\Filament\Resources\BomHeaders\BomHeaderResource;
use Filament\Resources\Pages\CreateRecord;

class CreateBomHeader extends CreateRecord
{
    protected static string $resource = BomHeaderResource::class;

    // Make form FULL WIDTH for better BOM creation experience
    protected static string $formMaxWidth = 'full';
}
