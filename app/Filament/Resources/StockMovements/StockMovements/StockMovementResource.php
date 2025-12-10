<?php

namespace App\Filament\Resources\StockMovements\StockMovements;

use App\Filament\Resources\StockMovements\StockMovements\Pages\ListStockMovements;
use App\Filament\Resources\StockMovements\StockMovements\Pages\ViewStockMovement;
use App\Filament\Resources\StockMovements\StockMovements\Schemas\StockMovementInfolist;
use App\Filament\Resources\StockMovements\StockMovements\Tables\StockMovementsTable;
use App\Models\StockMovement;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use UnitEnum;

class StockMovementResource extends Resource
{
    protected static ?string $model = StockMovement::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedArrowPath;

    protected static ?string $recordTitleAttribute = 'id';

    protected static string|UnitEnum|null $navigationGroup = '📦 Inventory';

    protected static ?string $navigationLabel = 'Riwayat Movement Stok';

    protected static ?int $navigationSort = 1;

    public static function getModelLabel(): string
    {
        return 'Stock Movement';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Riwayat Movement Stok';
    }

    public static function canCreate(): bool
    {
        return false; // View-only, tidak bisa create manual
    }

    public static function canEdit($record): bool
    {
        return false; // View-only, tidak bisa edit
    }

    public static function canDelete($record): bool
    {
        return false; // View-only, tidak bisa delete
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([]); // No form, view-only
    }

    public static function infolist(Schema $schema): Schema
    {
        return StockMovementInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return StockMovementsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListStockMovements::route('/'),
            'view' => ViewStockMovement::route('/{record}'),
        ];
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
