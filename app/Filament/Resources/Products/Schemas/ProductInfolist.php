<?php

namespace App\Filament\Resources\Products\Schemas;

use App\Models\Product;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class ProductInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('sku')
                    ->label('SKU'),
                TextEntry::make('name'),
                TextEntry::make('description')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('type'),
                IconEntry::make('is_sellable')
                    ->boolean(),
                IconEntry::make('is_purchasable')
                    ->boolean(),
                TextEntry::make('uom_purchase'),
                TextEntry::make('uom_stock'),
                TextEntry::make('uom_usage'),
                TextEntry::make('conversion_purchase_to_stock')
                    ->numeric(),
                TextEntry::make('conversion_stock_to_usage')
                    ->numeric(),
                TextEntry::make('purchase_price')
                    ->money(),
                TextEntry::make('selling_price')
                    ->money(),
                TextEntry::make('standard_cost')
                    ->money(),
                TextEntry::make('current_stock')
                    ->numeric(),
                TextEntry::make('minimum_stock')
                    ->numeric(),
                TextEntry::make('maximum_stock')
                    ->numeric(),
                TextEntry::make('incomeAccount.name')
                    ->label('Income account')
                    ->placeholder('-'),
                TextEntry::make('expenseAccount.name')
                    ->label('Expense account')
                    ->placeholder('-'),
                TextEntry::make('inventoryAccount.name')
                    ->label('Inventory account')
                    ->placeholder('-'),
                IconEntry::make('is_active')
                    ->boolean(),
                TextEntry::make('barcode')
                    ->placeholder('-'),
                ImageEntry::make('image_url')
                    ->placeholder('-'),
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
                    ->visible(fn (Product $record): bool => $record->trashed()),
            ]);
    }
}
