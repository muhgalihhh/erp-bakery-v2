<?php

namespace App\Filament\Pages;

use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Illuminate\Contracts\Support\Htmlable;

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
