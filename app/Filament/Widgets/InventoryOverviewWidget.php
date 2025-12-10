<?php

namespace App\Filament\Widgets;

use App\Models\Product;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class InventoryOverviewWidget extends BaseWidget
{
  protected ?string $heading = 'Ringkasan Persediaan';

  /**
   * Span full width on dashboard for clarity
   * @var string|int|array
   */
  protected string|int|array $columnSpan = 'full';

  protected function getStats(): array
  {
    $totals = Product::query()
      ->where('is_active', true)
      ->selectRaw('COALESCE(SUM(current_stock * standard_cost), 0) as total_value')
      ->selectRaw('COALESCE(SUM(CASE WHEN current_stock <= minimum_stock THEN 1 ELSE 0 END), 0) as low_count')
      ->selectRaw('COUNT(*) as sku_count')
      ->first();

    $totalValue = (float) ($totals->total_value ?? 0);
    $skuCount = (int) ($totals->sku_count ?? 0);
    $lowCount = (int) ($totals->low_count ?? 0);

    $currency = 'Rp';

    return [
      Stat::make('Nilai Persediaan', $currency . ' ' . number_format($totalValue, 0, ',', '.'))
        ->description('Total = stok × biaya standar')
        ->descriptionIcon('heroicon-m-banknotes')
        ->color('success'),

      Stat::make('SKU Aktif', number_format($skuCount))
        ->description('Produk aktif')
        ->descriptionIcon('heroicon-m-rectangle-stack')
        ->color('primary'),

      Stat::make('Stok Rendah', number_format($lowCount))
        ->description('Perlu perhatian')
        ->descriptionIcon('heroicon-m-exclamation-triangle')
        ->color('danger'),
    ];
  }

  public static function canView(): bool
  {
    return true;
  }
}
