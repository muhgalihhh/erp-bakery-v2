<?php

namespace App\Filament\Resources\VendorPayments;

use App\Filament\Resources\VendorPayments\Pages\CreateVendorPayment;
use App\Filament\Resources\VendorPayments\Pages\EditVendorPayment;
use App\Filament\Resources\VendorPayments\Pages\ListVendorPayments;
use App\Filament\Resources\VendorPayments\Pages\ViewVendorPayment;
use App\Filament\Resources\VendorPayments\Schemas\VendorPaymentForm;
use App\Filament\Resources\VendorPayments\Schemas\VendorPaymentInfolist;
use App\Filament\Resources\VendorPayments\Tables\VendorPaymentsTable;
use App\Models\VendorPayment;
use BackedEnum;
use UnitEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class VendorPaymentResource extends Resource
{
    protected static ?string $model = VendorPayment::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBanknotes;

    protected static ?string $navigationLabel = 'Pembayaran Supplier';

    protected static ?string $modelLabel = 'Pembayaran Supplier';

    protected static ?string $pluralModelLabel = 'Pembayaran Supplier';

    protected static string|UnitEnum|null $navigationGroup = 'Pembelian';

    protected static ?int $navigationSort = 4;

    protected static ?string $recordTitleAttribute = 'payment_number';

    public static function form(Schema $schema): Schema
    {
        return VendorPaymentForm::make($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return VendorPaymentInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return VendorPaymentsTable::make($table);
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
            'index' => ListVendorPayments::route('/'),
            'create' => CreateVendorPayment::route('/create'),
            'view' => ViewVendorPayment::route('/{record}'),
            'edit' => EditVendorPayment::route('/{record}/edit'),
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
