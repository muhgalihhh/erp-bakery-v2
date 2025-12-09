<?php

namespace App\Filament\Resources\BomHeaders;

use App\Filament\Resources\BomHeaders\Pages\CreateBomHeader;
use App\Filament\Resources\BomHeaders\Pages\EditBomHeader;
use App\Filament\Resources\BomHeaders\Pages\ListBomHeaders;
use App\Filament\Resources\BomHeaders\Pages\ViewBomHeader;
use App\Filament\Resources\BomHeaders\Schemas\BomHeaderForm;
use App\Filament\Resources\BomHeaders\Schemas\BomHeaderInfolist;
use App\Filament\Resources\BomHeaders\Tables\BomHeadersTable;
use App\Models\BomHeader;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use UnitEnum;

class BomHeaderResource extends Resource
{
    protected static ?string $model = BomHeader::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClipboardDocumentList;

    protected static ?string $recordTitleAttribute = 'Production Recipe';

    // Custom Labels
    protected static ?string $navigationLabel = 'Bill of Material (BOM)';

    protected static ?string $modelLabel = 'BOM';

    protected static ?string $pluralModelLabel = 'Bill of Material';

    protected static string|UnitEnum|null $navigationGroup = 'Produksi';

    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return BomHeaderForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return BomHeaderInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return BomHeadersTable::configure($table);
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
            'index' => ListBomHeaders::route('/'),
            'create' => CreateBomHeader::route('/create'),
            'view' => ViewBomHeader::route('/{record}'),
            'edit' => EditBomHeader::route('/{record}/edit'),
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
