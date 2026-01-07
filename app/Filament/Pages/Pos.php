<?php

namespace App\Filament\Pages;

use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use App\Filament\Widgets\PosStatsWidget;
use App\Filament\Widgets\PosRecentTransactionsWidget;
use App\Filament\Widgets\PosTopProductsWidget;

class Pos extends Page
{
    use HasPageShield;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedShoppingCart;

    protected static ?string $navigationLabel = 'Point of Sale';

    protected static ?string $title = 'Point of Sale';

    protected static ?string $slug = 'pos';

    protected static ?int $navigationSort = 1;

    protected string $view = 'filament.pages.pos';

    public static function getNavigationGroup(): ?string
    {
        return 'Sales';
    }

    public function mount(): void
    {
        abort_unless(static::canAccess(), 403);
    }

    public static function canAccess(): bool
    {
        $user = auth()->user();
        if (!$user) {
            return false;
        }

        return $user->can('page_Pos') || $user->can('view_pos');
    }

    protected function getHeaderWidgets(): array
    {
        return [
            PosStatsWidget::class,
        ];
    }

    protected function getFooterWidgets(): array
    {
        return [
            PosRecentTransactionsWidget::class,
            PosTopProductsWidget::class,
        ];
    }

    public function getHeaderWidgetsColumns(): int|array
    {
        return 4;
    }

    public function getFooterWidgetsColumns(): int|array
    {
        return [
            'default' => 1,
            'lg' => 2,
        ];
    }

    /**
     * Get breadcrumbs
     */
    public function getBreadcrumbs(): array
    {
        return [
            '#' => 'Point of Sale',
        ];
    }
}
