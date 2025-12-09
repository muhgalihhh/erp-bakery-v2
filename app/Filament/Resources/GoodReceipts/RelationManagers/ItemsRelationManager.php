<?php

namespace App\Filament\Resources\GoodReceipts\RelationManagers;

use Filament\Tables;
use Filament\Tables\Table;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn;

class ItemsRelationManager extends RelationManager
{
  protected static string $relationship = 'items';

  protected static ?string $title = 'Items Received (Barang yang Diterima)';

  protected static ?string $recordTitleAttribute = 'product.name';

  public function table(Table $table): Table
  {
    return $table
      ->columns([
        TextColumn::make('product.name')
          ->label('Product Name')
          ->searchable()
          ->sortable()
          ->weight('bold')
          ->icon('heroicon-o-cube')
          ->color('primary'),

        TextColumn::make('product.sku')
          ->label('SKU')
          ->searchable()
          ->toggleable()
          ->copyable()
          ->color('gray'),

        TextColumn::make('ordered_quantity')
          ->label('Ordered Qty')
          ->alignEnd()
          ->formatStateUsing(fn($state, $record) => static::formatQuantity($state) . ' ' . ($record->product?->uom_purchase ?? 'unit'))
          ->color('info'),

        TextColumn::make('received_quantity')
          ->label('Received Qty')
          ->alignEnd()
          ->formatStateUsing(fn($state, $record) => static::formatQuantity($state) . ' ' . ($record->product?->uom_purchase ?? 'unit'))
          ->weight('semibold')
          ->color('success'),

        TextColumn::make('rejected_quantity')
          ->label('Rejected Qty')
          ->alignEnd()
          ->formatStateUsing(fn($state, $record) => static::formatQuantity($state) . ' ' . ($record->product?->uom_purchase ?? 'unit'))
          ->color('danger')
          ->default(0),

        TextColumn::make('accepted_quantity')
          ->label('Accepted Qty')
          ->alignEnd()
          ->state(fn($record) => $record->received_quantity - $record->rejected_quantity)
          ->formatStateUsing(fn($state, $record) => static::formatQuantity($state) . ' ' . ($record->product?->uom_purchase ?? 'unit'))
          ->weight('bold')
          ->color('success')
          ->icon('heroicon-o-check-circle'),

        TextColumn::make('notes')
          ->label('Notes')
          ->limit(50)
          ->placeholder('-')
          ->toggleable()
          ->wrap(),

        TextColumn::make('rejection_reason')
          ->label('Rejection Reason')
          ->limit(50)
          ->placeholder('-')
          ->toggleable(isToggledHiddenByDefault: true)
          ->wrap()
          ->color('danger'),
      ])
      ->defaultSort('id')
      ->filters([
        // Add filters if needed
      ])
      ->headerActions([
        // No add action - items come from PO
      ])
      ->actions([
        // No edit/delete actions - read only
      ])
      ->bulkActions([
        // No bulk actions
      ]);
  }

  /**
   * Format quantity with smart decimal places
   * - Shows no decimals for whole numbers (10 → "10")
   * - Shows up to 4 decimals for fractional, removing trailing zeros
   */
  private static function formatQuantity(float $quantity): string
  {
    return rtrim(rtrim(number_format($quantity, 4, '.', ','), '0'), '.');
  }
}

