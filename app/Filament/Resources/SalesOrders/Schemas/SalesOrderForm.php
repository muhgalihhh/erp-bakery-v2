<?php

namespace App\Filament\Resources\SalesOrders\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TimePicker;
use Filament\Schemas\Schema;

class SalesOrderForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('order_number'),
                Select::make('customer_id')
                    ->relationship('customer', 'name'),
                DateTimePicker::make('order_date')
                    ->required(),
                TextInput::make('order_type')
                    ->required()
                    ->default('pos'),
                TextInput::make('order_channel')
                    ->required()
                    ->default('store'),
                DatePicker::make('delivery_date'),
                TimePicker::make('delivery_time'),
                Textarea::make('delivery_address')
                    ->columnSpanFull(),
                TextInput::make('delivery_phone')
                    ->tel(),
                TextInput::make('delivery_recipient'),
                TextInput::make('status')
                    ->required()
                    ->default('draft'),
                TextInput::make('subtotal')
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('discount_amount')
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('discount_source'),
                TextInput::make('tax_percentage')
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('tax_amount')
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('shipping_cost')
                    ->required()
                    ->numeric()
                    ->default(0)
                    ->prefix('$'),
                TextInput::make('points_used')
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('points_earned')
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('total')
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('payment_status')
                    ->required()
                    ->default('pending'),
                TextInput::make('payment_method'),
                TextInput::make('paid_amount')
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('change_amount')
                    ->required()
                    ->numeric()
                    ->default(0),
                Textarea::make('payment_details')
                    ->columnSpanFull(),
                TextInput::make('served_by')
                    ->numeric(),
                TextInput::make('prepared_by')
                    ->numeric(),
                Textarea::make('customer_notes')
                    ->columnSpanFull(),
                Textarea::make('internal_notes')
                    ->columnSpanFull(),
                TextInput::make('created_by')
                    ->numeric(),
                TextInput::make('updated_by')
                    ->numeric(),
                TextInput::make('cancelled_by')
                    ->numeric(),
                DateTimePicker::make('cancelled_at'),
                Textarea::make('cancellation_reason')
                    ->columnSpanFull(),
            ]);
    }
}
