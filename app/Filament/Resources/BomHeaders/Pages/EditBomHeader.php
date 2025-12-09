<?php

namespace App\Filament\Resources\BomHeaders\Pages;

use App\Filament\Resources\BomHeaders\BomHeaderResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditBomHeader extends EditRecord
{
    protected static string $resource = BomHeaderResource::class;

    // Make form FULL WIDTH for better BOM editing experience
    protected static string $formMaxWidth = 'full';

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
            ForceDeleteAction::make(),
            RestoreAction::make(),
        ];
    }
}
