<?php

namespace App\Filament\Resources\BomHeaders\Schemas;

use App\Models\BomHeader;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class BomHeaderInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('product.name')
                    ->label('Product'),
                TextEntry::make('bom_code'),
                TextEntry::make('version'),
                TextEntry::make('description')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('quantity_produced')
                    ->formatStateUsing(fn($state) => number_format((float) $state, 0)),
                IconEntry::make('is_active')
                    ->boolean(),
                IconEntry::make('is_default')
                    ->boolean(),
                TextEntry::make('production_time_minutes')
                    ->formatStateUsing(fn($state) => $state ? number_format((float) $state, 0) . ' min' : '-')
                    ->placeholder('-'),
                TextEntry::make('instructions')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('created_by')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('updated_by')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('deleted_at')
                    ->dateTime()
                    ->visible(fn(BomHeader $record): bool => $record->trashed()),
            ]);
    }
}
