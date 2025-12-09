<?php

namespace App\Filament\Resources\BomHeaders\Pages;

use App\Filament\Resources\BomHeaders\BomHeaderResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewBomHeader extends ViewRecord
{
    protected static string $resource = BomHeaderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
