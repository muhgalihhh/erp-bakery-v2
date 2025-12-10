<?php

namespace App\Filament\Resources\StockMovements\StockMovements\Tables;

use App\Models\StockMovement;
use Filament\Actions\ViewAction;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class StockMovementsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('created_at')
                    ->label('Tanggal & Waktu')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->weight(FontWeight::Bold),

                TextColumn::make('type')
                    ->label('Tipe')
                    ->badge()
                    ->formatStateUsing(fn($state) => match ($state) {
                        StockMovement::TYPE_IN => 'MASUK',
                        StockMovement::TYPE_OUT => 'KELUAR',
                        StockMovement::TYPE_ADJUSTMENT => 'ADJUSTMENT',
                        default => $state,
                    })
                    ->color(fn(string $state): string => match ($state) {
                        StockMovement::TYPE_IN => 'success',
                        StockMovement::TYPE_OUT => 'danger',
                        StockMovement::TYPE_ADJUSTMENT => 'warning',
                        default => 'gray',
                    })
                    ->sortable(),

                TextColumn::make('product.name')
                    ->label('Produk')
                    ->searchable()
                    ->sortable()
                    ->description(fn($record) => $record->product?->sku ?? ''),

                TextColumn::make('quantity')
                    ->label('Kuantitas')
                    ->formatStateUsing(function ($record) {
                        $qty = (float) $record->quantity;
                        $formatted = number_format(abs($qty), 2);
                        return ($qty >= 0 ? '+' : '-') . $formatted . ' ' . $record->uom;
                    })
                    ->color(fn($record) => $record->quantity >= 0 ? 'success' : 'danger')
                    ->weight(FontWeight::Bold)
                    ->alignEnd(),

                TextColumn::make('balance_after')
                    ->label('Saldo Setelah')
                    ->formatStateUsing(fn($record) => number_format((float)$record->balance_after, 2) . ' ' . $record->uom)
                    ->alignEnd()
                    ->toggleable(),

                TextColumn::make('reference_number')
                    ->label('Referensi')
                    ->searchable()
                    ->description(fn($record) => match ($record->reference_type) {
                        'App\\Models\\GoodReceipt' => '📦 Terima Barang',
                        'App\\Models\\ManufacturingOrder' => '🏭 Produksi',
                        'App\\Models\\StockAdjustment' => '📋 Adjustment',
                        default => $record->reference_type ?? '-',
                    }),

                TextColumn::make('notes')
                    ->label('Catatan')
                    ->limit(50)
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('batch_number')
                    ->label('Batch/Lot')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('expired_date')
                    ->label('Tanggal Expired')
                    ->date('d M Y')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('createdBy.name')
                    ->label('User')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('type')
                    ->label('Tipe Movement')
                    ->options([
                        StockMovement::TYPE_IN => 'MASUK (IN)',
                        StockMovement::TYPE_OUT => 'KELUAR (OUT)',
                        StockMovement::TYPE_ADJUSTMENT => 'ADJUSTMENT',
                    ])
                    ->multiple()
                    ->searchable(),

                SelectFilter::make('product_id')
                    ->label('Produk')
                    ->relationship('product', 'name')
                    ->searchable()
                    ->preload(),

                SelectFilter::make('reference_type')
                    ->label('Sumber Transaksi')
                    ->options([
                        'App\\Models\\GoodReceipt' => 'Good Receipt (Terima Barang)',
                        'App\\Models\\ManufacturingOrder' => 'Manufacturing Order (Produksi)',
                        'App\\Models\\StockAdjustment' => 'Stock Adjustment',
                    ])
                    ->searchable(),

                TrashedFilter::make()
                    ->label('Status Penghapusan')
                    ->placeholder('Tanpa yang Dihapus')
                    ->trueLabel('Hanya yang Dihapus')
                    ->falseLabel('Tanpa yang Dihapus')
                    ->native(false),
            ])
            ->recordActions([
                ViewAction::make()
                    ->label('Lihat')
                    ->icon('heroicon-o-eye'),
            ])
            ->emptyStateHeading('Belum ada movement stok')
            ->emptyStateDescription('Movement stok akan muncul otomatis saat ada transaksi GR, Produksi, atau Adjustment.')
            ->emptyStateIcon('heroicon-o-arrow-path');
    }
}

