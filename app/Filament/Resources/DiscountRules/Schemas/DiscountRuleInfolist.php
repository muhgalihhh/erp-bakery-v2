<?php

namespace App\Filament\Resources\DiscountRules\Schemas;

use App\Models\DiscountRule;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\KeyValueEntry;
use Filament\Schemas\Schema;

class DiscountRuleInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('name'),
                TextEntry::make('code')
                    ->placeholder('-'),
                TextEntry::make('description')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('coupon_code')
                    ->placeholder('-'),
                IconEntry::make('is_public')
                    ->boolean(),
                TextEntry::make('start_date')
                    ->dateTime(),
                TextEntry::make('end_date')
                    ->dateTime(),
                TextEntry::make('priority')
                    ->numeric(),
                IconEntry::make('can_combine')
                    ->boolean(),
                TextEntry::make('conditions')
                    ->placeholder('-')
                    ->formatStateUsing(fn($state) => is_array($state) ? json_encode($state, JSON_PRETTY_PRINT) : ($state ?? '-'))
                    ->columnSpanFull(),
                TextEntry::make('actions')
                    ->formatStateUsing(fn($state) => is_array($state) ? json_encode($state, JSON_PRETTY_PRINT) : ($state ?? '-'))
                    ->columnSpanFull(),
                TextEntry::make('usage_limit')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('usage_limit_per_customer')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('usage_count')
                    ->numeric(),
                IconEntry::make('is_active')
                    ->boolean(),
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
                    ->visible(fn(DiscountRule $record): bool => $record->trashed()),
            ]);
    }
}
