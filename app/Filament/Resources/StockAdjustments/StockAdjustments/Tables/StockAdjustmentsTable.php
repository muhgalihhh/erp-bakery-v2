<?php

namespace App\Filament\Resources\StockAdjustments\StockAdjustments\Tables;

use App\Models\StockAdjustment;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class StockAdjustmentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('adjustment_number')
                    ->label('No. Adjustment')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->copyMessage('Nomor disalin!')
                    ->weight(FontWeight::Bold)
                    ->color('primary'),

                TextColumn::make('adjustment_date')
                    ->label('Tanggal')
                    ->date('d M Y')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('product.name')
                    ->label('Produk')
                    ->searchable()
                    ->sortable()
                    ->description(fn($record) => $record->product?->sku ?? ''),

                TextColumn::make('type')
                    ->label('Tipe')
                    ->badge()
                    ->formatStateUsing(fn($state) => match ($state) {
                        StockAdjustment::TYPE_STOCK_OPNAME => 'Stock Opname',
                        StockAdjustment::TYPE_DAMAGED => 'Rusak',
                        StockAdjustment::TYPE_EXPIRED => 'Kadaluarsa',
                        StockAdjustment::TYPE_OTHER => 'Lainnya',
                        default => $state,
                    })
                    ->color(fn(string $state): string => match ($state) {
                        StockAdjustment::TYPE_STOCK_OPNAME => 'info',
                        StockAdjustment::TYPE_DAMAGED => 'danger',
                        StockAdjustment::TYPE_EXPIRED => 'warning',
                        StockAdjustment::TYPE_OTHER => 'gray',
                        default => 'gray',
                    }),

                TextColumn::make('system_quantity')
                    ->label('Stok Sistem')
                    ->formatStateUsing(fn($record) => number_format((float) $record->system_quantity, 2) . ' ' . $record->uom)
                    ->alignEnd(),

                TextColumn::make('actual_quantity')
                    ->label('Stok Aktual')
                    ->formatStateUsing(fn($record) => number_format((float) $record->actual_quantity, 2) . ' ' . $record->uom)
                    ->alignEnd()
                    ->weight(FontWeight::Bold),

                TextColumn::make('difference_quantity')
                    ->label('Selisih')
                    ->formatStateUsing(function ($record) {
                        $diff = (float) $record->difference_quantity;
                        $formatted = number_format(abs($diff), 2);
                        return ($diff >= 0 ? '+' : '-') . $formatted . ' ' . $record->uom;
                    })
                    ->color(fn($record) => $record->difference_quantity > 0 ? 'success' : ($record->difference_quantity < 0 ? 'danger' : 'gray'))
                    ->weight(FontWeight::Bold)
                    ->alignEnd(),

                TextColumn::make('reason')
                    ->label('Alasan')
                    ->formatStateUsing(fn($state) => match ($state) {
                        StockAdjustment::REASON_STOCK_COUNT => 'Stock Opname',
                        StockAdjustment::REASON_DAMAGED => 'Rusak',
                        StockAdjustment::REASON_EXPIRED => 'Kadaluarsa',
                        StockAdjustment::REASON_LOST => 'Hilang',
                        StockAdjustment::REASON_FOUND => 'Ketemu',
                        StockAdjustment::REASON_OTHER => 'Lainnya',
                        default => $state,
                    })
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn($state) => match ($state) {
                        'DRAFT' => 'Draft',
                        'APPROVED' => 'Disetujui',
                        'CANCELLED' => 'Dibatalkan',
                        default => ucfirst($state),
                    })
                    ->color(fn(string $state): string => match ($state) {
                        StockAdjustment::STATUS_DRAFT => 'warning',
                        StockAdjustment::STATUS_APPROVED => 'success',
                        StockAdjustment::STATUS_CANCELLED => 'danger',
                        default => 'gray',
                    })
                    ->sortable(),

                TextColumn::make('createdBy.name')
                    ->label('Dibuat Oleh')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('approvedBy.name')
                    ->label('Disetujui Oleh')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('adjustment_date', 'desc')
            ->filters([
                SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        StockAdjustment::STATUS_DRAFT => 'Draft',
                        StockAdjustment::STATUS_APPROVED => 'Disetujui',
                        StockAdjustment::STATUS_CANCELLED => 'Dibatalkan',
                    ])
                    ->multiple()
                    ->searchable(),

                SelectFilter::make('type')
                    ->label('Tipe')
                    ->options([
                        StockAdjustment::TYPE_STOCK_OPNAME => 'Stock Opname',
                        StockAdjustment::TYPE_DAMAGED => 'Rusak',
                        StockAdjustment::TYPE_EXPIRED => 'Kadaluarsa',
                        StockAdjustment::TYPE_OTHER => 'Lainnya',
                    ])
                    ->multiple()
                    ->searchable(),

                SelectFilter::make('product_id')
                    ->label('Produk')
                    ->relationship('product', 'name')
                    ->searchable()
                    ->preload(),

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

                EditAction::make()
                    ->label('Ubah')
                    ->icon('heroicon-o-pencil')
                    ->visible(fn(StockAdjustment $record) => $record->isDraft()),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->label('Hapus'),
                    ForceDeleteBulkAction::make()
                        ->label('Hapus Permanen'),
                    RestoreBulkAction::make()
                        ->label('Pulihkan'),
                ]),
            ])
            ->emptyStateHeading('Belum ada adjustment stok')
            ->emptyStateDescription('Buat adjustment pertama untuk penyesuaian stok.')
            ->emptyStateIcon('heroicon-o-clipboard-document-list');
    }
}
