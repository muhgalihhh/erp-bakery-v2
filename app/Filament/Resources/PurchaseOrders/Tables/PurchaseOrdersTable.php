<?php

namespace App\Filament\Resources\PurchaseOrders\Tables;

use App\Models\PurchaseOrder;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class PurchaseOrdersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('po_number')
                    ->label('No. PO')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->copyable()
                    ->icon('heroicon-o-document-text')
                    ->color('primary'),

                TextColumn::make('vendor.name')
                    ->label('Supplier')
                    ->searchable()
                    ->sortable()
                    ->icon('heroicon-o-building-office-2'),

                TextColumn::make('order_date')
                    ->label('Tgl Order')
                    ->date('d M Y')
                    ->sortable()
                    ->icon('heroicon-o-calendar'),

                TextColumn::make('expected_delivery_date')
                    ->label('Estimasi Kirim')
                    ->date('d M Y')
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->colors([
                        'secondary' => 'draft',
                        'warning' => 'pending',
                        'success' => 'approved',
                        'info' => 'partially_received',
                        'primary' => 'received',
                        'danger' => 'cancelled',
                    ])
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'draft' => 'DRAFT',
                        'pending' => 'PENDING',
                        'approved' => 'DISETUJUI',
                        'partially_received' => 'SEBAGIAN DITERIMA',
                        'received' => 'DITERIMA',
                        'cancelled' => 'DIBATALKAN',
                        default => strtoupper($state),
                    })
                    ->sortable(),

                TextColumn::make('total')
                    ->label('Total')
                    ->money('IDR')
                    ->sortable()
                    ->weight('bold')
                    ->alignEnd()
                    ->color('success'),

                TextColumn::make('approvedBy.name')
                    ->label('Disetujui Oleh')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->icon('heroicon-o-user-circle'),

                TextColumn::make('approved_at')
                    ->label('Tgl Disetujui')
                    ->dateTime('d M Y H:i')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->label('Diperbarui')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'draft' => 'Draft',
                        'pending' => 'Pending',
                        'approved' => 'Disetujui',
                        'partially_received' => 'Sebagian Diterima',
                        'received' => 'Diterima',
                        'cancelled' => 'Dibatalkan',
                    ])
                    ->multiple(),

                SelectFilter::make('vendor')
                    ->label('Supplier')
                    ->relationship('vendor', 'name')
                    ->searchable()
                    ->preload(),

                TrashedFilter::make()
                    ->label('Status Penghapusan')
                    ->placeholder('Semua')
                    ->trueLabel('Hanya yang Dihapus')
                    ->falseLabel('Tanpa yang Dihapus')
                    ->native(false),
            ])
            ->actions([
                ViewAction::make()
                    ->label('Lihat'),
                EditAction::make()
                    ->label('Ubah')
                    ->visible(
                        fn(PurchaseOrder $record): bool =>
                        $record->status === PurchaseOrder::STATUS_DRAFT
                    ),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->label('Hapus'),
                    ForceDeleteBulkAction::make()
                        ->label('Hapus Permanen'),
                    RestoreBulkAction::make()
                        ->label('Pulihkan'),
                ]),
            ])
            ->defaultSort('order_date', 'desc');
    }
}

