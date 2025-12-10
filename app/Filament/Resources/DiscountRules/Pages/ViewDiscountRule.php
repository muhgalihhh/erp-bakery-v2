<?php

namespace App\Filament\Resources\DiscountRules\Pages;

use App\Filament\Resources\DiscountRules\DiscountRuleResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewDiscountRule extends ViewRecord
{
    protected static string $resource = DiscountRuleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
