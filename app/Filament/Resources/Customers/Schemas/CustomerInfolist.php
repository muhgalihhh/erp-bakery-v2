<?php

namespace App\Filament\Resources\Customers\Schemas;

use App\Models\Customer;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class CustomerInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('customer_code')
                    ->placeholder('-'),
                TextEntry::make('name'),
                TextEntry::make('phone'),
                TextEntry::make('email')
                    ->label('Email address')
                    ->placeholder('-'),
                TextEntry::make('date_of_birth')
                    ->date()
                    ->placeholder('-'),
                TextEntry::make('gender')
                    ->placeholder('-'),
                TextEntry::make('address')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('city')
                    ->placeholder('-'),
                TextEntry::make('province')
                    ->placeholder('-'),
                TextEntry::make('postal_code')
                    ->placeholder('-'),
                TextEntry::make('customer_tier_id')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('total_points')
                    ->numeric(),
                TextEntry::make('total_spent')
                    ->numeric(),
                TextEntry::make('transaction_count')
                    ->numeric(),
                TextEntry::make('last_purchase_date')
                    ->date()
                    ->placeholder('-'),
                TextEntry::make('instagram')
                    ->placeholder('-'),
                TextEntry::make('facebook')
                    ->placeholder('-'),
                TextEntry::make('preferences')
                    ->placeholder('-')
                    ->columnSpanFull(),
                IconEntry::make('subscribe_newsletter')
                    ->boolean(),
                IconEntry::make('subscribe_whatsapp')
                    ->boolean(),
                IconEntry::make('is_active')
                    ->boolean(),
                TextEntry::make('notes')
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
                    ->visible(fn (Customer $record): bool => $record->trashed()),
            ]);
    }
}
