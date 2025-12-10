<?php

namespace App\Filament\Resources\StockAdjustments\StockAdjustments;

use App\Filament\Resources\StockAdjustments\StockAdjustments\Pages\CreateStockAdjustment;
use App\Filament\Resources\StockAdjustments\StockAdjustments\Pages\EditStockAdjustment;
use App\Filament\Resources\StockAdjustments\StockAdjustments\Pages\ListStockAdjustments;
use App\Filament\Resources\StockAdjustments\StockAdjustments\Pages\ViewStockAdjustment;
use App\Filament\Resources\StockAdjustments\StockAdjustments\Schemas\StockAdjustmentForm;
use App\Filament\Resources\StockAdjustments\StockAdjustments\Schemas\StockAdjustmentInfolist;
use App\Filament\Resources\StockAdjustments\StockAdjustments\Tables\StockAdjustmentsTable;
use App\Models\StockAdjustment;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class StockAdjustmentResource extends Resource
{
    protected static ?string $model = StockAdjustment::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'adjustment_number';

    protected static string|\UnitEnum|null $navigationGroup = '📦 Inventory';

    protected static ?string $navigationLabel = 'Penyesuaian Stok';

    protected static ?int $navigationSort = 2;

    public static function getModelLabel(): string
    {
        return 'Penyesuaian Stok';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Penyesuaian Stok';
    }

    public static function form(Schema $schema): Schema
    {
        return StockAdjustmentForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return StockAdjustmentInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return StockAdjustmentsTable::configure($table);
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
            'index' => ListStockAdjustments::route('/'),
            'create' => CreateStockAdjustment::route('/create'),
            'view' => ViewStockAdjustment::route('/{record}'),
            'edit' => EditStockAdjustment::route('/{record}/edit'),
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
