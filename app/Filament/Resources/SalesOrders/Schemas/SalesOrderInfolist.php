<?php

namespace App\Filament\Resources\SalesOrders\Schemas;

use App\Models\SalesOrder;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class SalesOrderInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('order_number')
                    ->placeholder('-'),
                TextEntry::make('customer.name')
                    ->label('Customer')
                    ->placeholder('-'),
                TextEntry::make('order_date')
                    ->dateTime(),
                TextEntry::make('order_type'),
                TextEntry::make('order_channel'),
                TextEntry::make('delivery_date')
                    ->date()
                    ->placeholder('-'),
                TextEntry::make('delivery_time')
                    ->time()
                    ->placeholder('-'),
                TextEntry::make('delivery_address')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('delivery_phone')
                    ->placeholder('-'),
                TextEntry::make('delivery_recipient')
                    ->placeholder('-'),
                TextEntry::make('status'),
                TextEntry::make('subtotal')
                    ->numeric(),
                TextEntry::make('discount_amount')
                    ->numeric(),
                TextEntry::make('discount_source')
                    ->placeholder('-'),
                TextEntry::make('tax_percentage')
                    ->numeric(),
                TextEntry::make('tax_amount')
                    ->numeric(),
                TextEntry::make('shipping_cost')
                    ->money(),
                TextEntry::make('points_used')
                    ->numeric(),
                TextEntry::make('points_earned')
                    ->numeric(),
                TextEntry::make('total')
                    ->numeric(),
                TextEntry::make('payment_status'),
                TextEntry::make('payment_method')
                    ->placeholder('-'),
                TextEntry::make('paid_amount')
                    ->numeric(),
                TextEntry::make('change_amount')
                    ->numeric(),
                TextEntry::make('payment_details')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('served_by')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('prepared_by')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('customer_notes')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('internal_notes')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('created_by')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('updated_by')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('cancelled_by')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('cancelled_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('cancellation_reason')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('deleted_at')
                    ->dateTime()
                    ->visible(fn (SalesOrder $record): bool => $record->trashed()),
            ]);
    }
}
