<?php

namespace App\Filament\Widgets;

use App\Models\SalesOrder;
use App\Models\Product;
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class PosStatsWidget extends BaseWidget
{
    protected function getPollingInterval(): ?string
    {
        return '30s';
    }

    protected function getStats(): array
    {
        $today = Carbon::today();
        $startOfWeek = Carbon::now()->startOfWeek();
        $startOfMonth = Carbon::now()->startOfMonth();

        // Sales statistics
        $todaySales = SalesOrder::whereDate('order_date', $today)
            ->where('status', 'completed')
            ->sum('total');

        $todayTransactions = SalesOrder::whereDate('order_date', $today)
            ->where('status', 'completed')
            ->count();

        $weekSales = SalesOrder::whereBetween('order_date', [$startOfWeek, Carbon::now()])
            ->where('status', 'completed')
            ->sum('total');

        $monthSales = SalesOrder::whereBetween('order_date', [$startOfMonth, Carbon::now()])
            ->where('status', 'completed')
            ->sum('total');

        // Stock alerts
        $lowStockProducts = Product::whereColumn('current_stock', '<=', 'minimum_stock')
            ->where('current_stock', '>', 0)
            ->count();

        $outOfStockProducts = Product::where('current_stock', '<=', 0)->count();
        $stockAlerts = $lowStockProducts + $outOfStockProducts;

        return [
            Stat::make('Penjualan Hari Ini', 'Rp ' . number_format($todaySales, 0, ',', '.'))
                ->description($todayTransactions . ' transaksi')
                ->descriptionIcon('heroicon-m-shopping-cart')
                ->color('success')
                ->chart([7, 3, 4, 5, 6, 3, 5, 8]),

            Stat::make('Penjualan Minggu Ini', 'Rp ' . number_format($weekSales, 0, ',', '.'))
                ->description('Akumulasi minggu berjalan')
                ->descriptionIcon('heroicon-m-calendar')
                ->color('info'),

            Stat::make('Penjualan Bulan Ini', 'Rp ' . number_format($monthSales, 0, ',', '.'))
                ->description('Akumulasi bulan berjalan')
                ->descriptionIcon('heroicon-m-calendar-days')
                ->color('primary'),

            Stat::make('Peringatan Stok', $stockAlerts . ' produk')
                ->description($lowStockProducts . ' rendah, ' . $outOfStockProducts . ' habis')
                ->descriptionIcon('heroicon-m-exclamation-triangle')
                ->color($stockAlerts > 0 ? 'danger' : 'success'),
        ];
    }
}
