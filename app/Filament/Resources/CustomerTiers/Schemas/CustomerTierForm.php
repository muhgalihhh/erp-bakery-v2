<?php

namespace App\Filament\Resources\CustomerTiers\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class CustomerTierForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),
                TextInput::make('code')
                    ->required(),
                Textarea::make('description')
                    ->columnSpanFull(),
                TextInput::make('minimum_spend')
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('point_multiplier')
                    ->required()
                    ->numeric()
                    ->default(1),
                TextInput::make('discount_percentage')
                    ->required()
                    ->numeric()
                    ->default(0),
                Textarea::make('benefits')
                    ->columnSpanFull(),
                TextInput::make('priority')
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('color'),
                TextInput::make('icon'),
                Toggle::make('is_active')
                    ->required(),
            ]);
    }
}
