<?php

namespace App\Filament\Resources\Customers\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class CustomerForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('customer_code'),
                TextInput::make('name')
                    ->required(),
                TextInput::make('phone')
                    ->tel()
                    ->required(),
                TextInput::make('email')
                    ->label('Email address')
                    ->email(),
                DatePicker::make('date_of_birth'),
                TextInput::make('gender'),
                Textarea::make('address')
                    ->columnSpanFull(),
                TextInput::make('city'),
                TextInput::make('province'),
                TextInput::make('postal_code'),
                TextInput::make('customer_tier_id')
                    ->numeric(),
                TextInput::make('total_points')
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('total_spent')
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('transaction_count')
                    ->required()
                    ->numeric()
                    ->default(0),
                DatePicker::make('last_purchase_date'),
                TextInput::make('instagram'),
                TextInput::make('facebook'),
                Textarea::make('preferences')
                    ->columnSpanFull(),
                Toggle::make('subscribe_newsletter')
                    ->required(),
                Toggle::make('subscribe_whatsapp')
                    ->required(),
                Toggle::make('is_active')
                    ->required(),
                Textarea::make('notes')
                    ->columnSpanFull(),
                TextInput::make('created_by')
                    ->numeric(),
                TextInput::make('updated_by')
                    ->numeric(),
            ]);
    }
}
