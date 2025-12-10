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
    protected static ?string $model = SalesOrder::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedShoppingCart;

    protected static ?string $navigationLabel = 'Point of Sale';

    protected static ?string $modelLabel = 'POS Transaction';

    protected static string|UnitEnum|null $navigationGroup = 'Sales & CRM';

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn($query) => $query->where('order_type', 'pos'))
            ->columns([
                Tables\Columns\TextColumn::make('order_number')
                    ->label('No. Transaksi')
                    ->searchable()
                    ->sortable()
                    ->copyable(),
                Tables\Columns\TextColumn::make('customer.name')
                    ->label('Customer')
                    ->searchable()
                    ->default('Walk-in Customer'),
                Tables\Columns\TextColumn::make('total')
                    ->label('Total')
                    ->money('IDR')
                    ->sortable(),
                Tables\Columns\TextColumn::make('payment_method')
                    ->label('Metode Bayar')
                    ->formatStateUsing(fn(string $state): string => ucwords(str_replace('_', ' ', $state)))
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'cash' => 'success',
                        'debit_card', 'credit_card' => 'primary',
                        'e_wallet' => 'warning',
                        'bank_transfer', 'qris' => 'info',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('payment_status')
                    ->label('Status Bayar')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'paid' => 'success',
                        'partial' => 'warning',
                        'unpaid' => 'danger',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('served_by_user.name')
                    ->label('Kasir')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Tanggal')
                    ->dateTime('d M Y, H:i')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('today')
                    ->label('Hari Ini')
                    ->queries(
                        true: fn($query) => $query->whereDate('created_at', today()),
                        false: fn($query) => $query->whereDate('created_at', '!=', today()),
                        blank: fn($query) => $query,
                    )
                    ->default(true),
                Tables\Filters\SelectFilter::make('payment_method')
                    ->label('Metode Pembayaran')
                    ->options([
                        'cash' => 'Cash',
                        'debit_card' => 'Debit Card',
                        'credit_card' => 'Credit Card',
                        'bank_transfer' => 'Bank Transfer',
                        'e_wallet' => 'E-Wallet',
                        'qris' => 'QRIS',
                    ]),
            ])
            ->defaultSort('created_at', 'desc');
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
            'index' => Pages\ListPointOfSales::route('/'),
            'create' => Pages\CreatePointOfSale::route('/create'),
            'view' => Pages\ViewPointOfSale::route('/{record}'),
        ];
    }
}
