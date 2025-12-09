<?php

namespace App\Filament\Resources\GoodReceipts;

use App\Filament\Resources\GoodReceipts\Pages\CreateGoodReceipt;
use App\Filament\Resources\GoodReceipts\Pages\EditGoodReceipt;
use App\Filament\Resources\GoodReceipts\Pages\ListGoodReceipts;
use App\Filament\Resources\GoodReceipts\Pages\ViewGoodReceipt;
use App\Filament\Resources\GoodReceipts\RelationManagers;
use App\Filament\Resources\GoodReceipts\Schemas\GoodReceiptForm;
use App\Filament\Resources\GoodReceipts\Schemas\GoodReceiptInfolist;
use App\Filament\Resources\GoodReceipts\Tables\GoodReceiptsTable;
use App\Models\GoodReceipt;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use UnitEnum;

class GoodReceiptResource extends Resource
{
    protected static ?string $model = GoodReceipt::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClipboardDocumentCheck;

    protected static ?string $recordTitleAttribute = 'receipt_number';

    protected static string|UnitEnum|null $navigationGroup = 'Pembelian';

    protected static ?int $navigationSort = 3;

    protected static ?string $navigationLabel = 'Terima Barang';

    protected static ?string $modelLabel = 'Penerimaan Barang';

    protected static ?string $pluralModelLabel = 'Penerimaan Barang';

    public static function form(Schema $schema): Schema
    {
        return GoodReceiptForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return GoodReceiptInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return GoodReceiptsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\ItemsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListGoodReceipts::route('/'),
            'create' => CreateGoodReceipt::route('/create'),
            'view' => ViewGoodReceipt::route('/{record}'),
            'edit' => EditGoodReceipt::route('/{record}/edit'),
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
