<?php

namespace App\Filament\Widgets;

use App\Models\Product;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class LowStockWidget extends BaseWidget
{
  protected static ?string $heading = 'Stok Rendah';

  protected int|string|array $columnSpan = 'full';

  public function table(Table $table): Table
  {
    return $table
      ->query(
        Product::query()
          ->where('is_active', true)
          ->whereColumn('current_stock', '<=', 'minimum_stock')
          ->orderByRaw('(minimum_stock - current_stock) DESC')
      )
      ->paginated([5, 10, 25])
      ->defaultPaginationPageOption(10)
      ->columns([
        Tables\Columns\TextColumn::make('sku')
          ->label('SKU')
          ->searchable(),
        Tables\Columns\TextColumn::make('name')
          ->label('Produk')
          ->searchable()
          ->limit(24),
        Tables\Columns\TextColumn::make('current_stock')
          ->label('Stok')
          ->numeric(2)
          ->suffix(fn($record) => ' ' . $record->uom_stock)
          ->color('danger')
          ->sortable(),
        Tables\Columns\TextColumn::make('minimum_stock')
          ->label('Min')
          ->numeric(2)
          ->suffix(fn($record) => ' ' . $record->uom_stock)
          ->sortable(),
        Tables\Columns\TextColumn::make('reorder_point')
          ->label('Reorder')
          ->numeric(2)
          ->suffix(fn($record) => ' ' . $record->uom_stock)
          ->sortable(),
      ])
      ->actions([
        // If a Product resource exists, enable quick view/edit. Left generic to avoid coupling.
      ])
      ->emptyStateHeading('Tidak ada yang rendah')
      ->emptyStateDescription('Semua stok di atas batas minimum.');
  }
}
