<?php

namespace App\Filament\Resources\CustomerTiers\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class CustomerTierInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('name'),
                TextEntry::make('code'),
                TextEntry::make('description')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('minimum_spend')
                    ->numeric(),
                TextEntry::make('point_multiplier')
                    ->numeric(),
                TextEntry::make('discount_percentage')
                    ->numeric(),
                TextEntry::make('benefits')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('priority')
                    ->numeric(),
                TextEntry::make('color')
                    ->placeholder('-'),
                TextEntry::make('icon')
                    ->placeholder('-'),
                IconEntry::make('is_active')
                    ->boolean(),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
            ]);
    }
}
