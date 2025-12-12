<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PointOfSaleResource\Pages;
use App\Models\SalesOrder;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables;
use Filament\Tables\Table;
use UnitEnum;

class PointOfSaleResource extends Resource
{
    // POS Filament resource disabled in favor of standalone POS module.
    // Keep class for potential future admin history, but hide from navigation and avoid route exposure.
    protected static ?string $model = SalesOrder::class;

    protected static string|BackedEnum|null $navigationIcon = null;

    protected static ?string $navigationLabel = null;

    protected static ?string $modelLabel = null;

    protected static string|UnitEnum|null $navigationGroup = null;

    protected static ?int $navigationSort = null;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([]);
    }

    public static function table(Table $table): Table
    {
        // Hide table by returning an empty configuration when resource is disabled.
        return $table->columns([])->filters([]);
    }
    // Resource is disabled; omit relations and pages.
}
