<?php

namespace App\Filament\Resources\BomHeaders\Pages;

use App\Filament\Resources\BomHeaders\BomHeaderResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListBomHeaders extends ListRecords
{
    protected static string $resource = BomHeaderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
