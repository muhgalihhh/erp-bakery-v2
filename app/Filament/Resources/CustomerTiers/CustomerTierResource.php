<?php

namespace App\Filament\Resources\CustomerTiers;

use App\Filament\Resources\CustomerTiers\Pages\CreateCustomerTier;
use App\Filament\Resources\CustomerTiers\Pages\EditCustomerTier;
use App\Filament\Resources\CustomerTiers\Pages\ListCustomerTiers;
use App\Filament\Resources\CustomerTiers\Pages\ViewCustomerTier;
use App\Filament\Resources\CustomerTiers\Schemas\CustomerTierForm;
use App\Filament\Resources\CustomerTiers\Schemas\CustomerTierInfolist;
use App\Filament\Resources\CustomerTiers\Tables\CustomerTiersTable;
use App\Models\CustomerTier;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class CustomerTierResource extends Resource
{
    protected static ?string $model = CustomerTier::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedTrophy;

    protected static ?string $navigationLabel = 'Customer Tiers';

    protected static string|\UnitEnum|null $navigationGroup = '💰 Sales & CRM';

    protected static ?int $navigationSort = 2;

    protected static ?string $modelLabel = 'Customer Tier';

    protected static ?string $pluralModelLabel = 'Customer Tiers';

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return CustomerTierForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return CustomerTierInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CustomerTiersTable::configure($table);
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
            'index' => ListCustomerTiers::route('/'),
            'create' => CreateCustomerTier::route('/create'),
            'view' => ViewCustomerTier::route('/{record}'),
            'edit' => EditCustomerTier::route('/{record}/edit'),
        ];
    }
}
