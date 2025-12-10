<?php

namespace App\Filament\Resources\DiscountRules;

use App\Filament\Resources\DiscountRules\Pages\CreateDiscountRule;
use App\Filament\Resources\DiscountRules\Pages\EditDiscountRule;
use App\Filament\Resources\DiscountRules\Pages\ListDiscountRules;
use App\Filament\Resources\DiscountRules\Pages\ViewDiscountRule;
use App\Filament\Resources\DiscountRules\Schemas\DiscountRuleForm;
use App\Filament\Resources\DiscountRules\Schemas\DiscountRuleInfolist;
use App\Filament\Resources\DiscountRules\Tables\DiscountRulesTable;
use App\Models\DiscountRule;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class DiscountRuleResource extends Resource
{
    protected static ?string $model = DiscountRule::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedTicket;

    protected static ?string $navigationLabel = 'Discount Rules';

    protected static string|\UnitEnum|null $navigationGroup = '💰 Sales & CRM';

    protected static ?int $navigationSort = 3;

    protected static ?string $modelLabel = 'Discount Rule';

    protected static ?string $pluralModelLabel = 'Discount Rules';

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema(DiscountRuleForm::schema())
            ->columns(3);
    }

    public static function infolist(Schema $schema): Schema
    {
        return DiscountRuleInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return DiscountRulesTable::configure($table);
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
            'index' => ListDiscountRules::route('/'),
            'create' => CreateDiscountRule::route('/create'),
            'view' => ViewDiscountRule::route('/{record}'),
            'edit' => EditDiscountRule::route('/{record}/edit'),
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
