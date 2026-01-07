<?php

namespace App\Filament\Widgets;

use App\Models\Product;
use Carbon\Carbon;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;

class PosTopProductsWidget extends BaseWidget
{
  protected static ?string $heading = 'Produk Terlaris Hari Ini';

  protected int|string|array $columnSpan = 1;

  public function table(Table $table): Table
  {
    $today = Carbon::today();

    return $table
      ->query(
        Product::query()
          ->select('products.*')
          ->selectRaw('COALESCE(SUM(sales_order_items.quantity), 0) as total_sold')
          ->leftJoin('sales_order_items', 'products.id', '=', 'sales_order_items.product_id')
          ->leftJoin('sales_orders', function ($join) use ($today) {
            $join->on('sales_orders.id', '=', 'sales_order_items.sales_order_id')
              ->whereDate('sales_orders.order_date', $today)
              ->where('sales_orders.status', 'completed');
          })
          ->where('products.is_active', true)
          ->groupBy('products.id')
          ->orderByDesc('total_sold')
          ->limit(5)
      )
      ->columns([
        Tables\Columns\TextColumn::make('name')
          ->label('Produk')
          ->weight('medium')
          ->searchable()
          ->limit(30),

        Tables\Columns\TextColumn::make('sku')
          ->label('SKU')
          ->size('sm')
          ->color('gray'),

        Tables\Columns\TextColumn::make('total_sold')
          ->label('Terjual')
          ->badge()
          ->color('success')
          ->suffix(' pcs')
          ->alignCenter(),

        Tables\Columns\TextColumn::make('selling_price')
          ->label('Harga')
          ->money('IDR')
          ->size('sm')
          ->alignEnd(),
      ])
      ->paginated(false)
      ->striped();
  }
}
