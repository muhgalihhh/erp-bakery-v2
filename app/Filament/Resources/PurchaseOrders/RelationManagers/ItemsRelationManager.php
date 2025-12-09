<?php

namespace App\Filament\Resources\PurchaseOrders\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Support\Enums\FontWeight;

class ItemsRelationManager extends RelationManager
{
  protected static string $relationship = 'items';

  protected static ?string $recordTitleAttribute = 'id';

  protected static ?string $title = 'Order Items';

  public function table(Table $table): Table
  {
    return $table
      ->recordTitleAttribute('id')
      ->columns([
        Tables\Columns\TextColumn::make('product.name')
          ->label('Product Name')
          ->weight(FontWeight::Bold)
          ->searchable()
          ->sortable(),

        Tables\Columns\TextColumn::make('product.sku')
          ->label('SKU')
          ->searchable()
          ->toggleable(isToggledHiddenByDefault: true),

        Tables\Columns\TextColumn::make('quantity')
          ->label('Quantity')
          ->alignRight()
          ->formatStateUsing(fn($record) => static::formatQuantity($record->quantity) . ' ' . ($record->product->uom_purchase ?? 'pcs'))
          ->weight(FontWeight::Medium),

        Tables\Columns\TextColumn::make('unit_price')
          ->label('Unit Price')
          ->money('IDR')
          ->alignRight(),

        Tables\Columns\TextColumn::make('subtotal')
          ->label('Subtotal')
          ->money('IDR')
          ->alignRight(),

        Tables\Columns\TextColumn::make('discount_amount')
          ->label('Discount')
          ->money('IDR')
          ->alignRight()
          ->color('warning')
          ->default(0),

        Tables\Columns\TextColumn::make('tax_percentage')
          ->label('Tax %')
          ->formatStateUsing(fn($state) => $state ? "{$state}%" : '0%')
          ->alignCenter()
          ->toggleable(isToggledHiddenByDefault: true),

        Tables\Columns\TextColumn::make('total')
          ->label('Total')
          ->money('IDR')
          ->alignRight()
          ->weight(FontWeight::Bold)
          ->color('success'),
      ])
      ->filters([
        //
      ])
      ->headerActions([
        // Read-only in view mode
      ])
      ->actions([
        // Read-only in view mode
      ])
      ->bulkActions([
        // Read-only in view mode
      ])
      ->defaultSort('id', 'asc')
      ->paginated(false); // Show all items without pagination
  }

  /**
   * Format quantity with smart decimal places
   * - Shows no decimals for whole numbers (10 → "10")
   * - Shows up to 2 decimals for fractional (10.5 → "10.5", 10.25 → "10.25")
   */
  private static function formatQuantity(float $quantity): string
  {
    // Remove trailing zeros after decimal point
    return rtrim(rtrim(number_format($quantity, 4, '.', ','), '0'), '.');
  }
}

