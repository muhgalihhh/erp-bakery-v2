<?php

namespace App\Filament\Resources\GoodReceipts\Tables;

use App\Models\GoodReceipt;
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

class GoodReceiptsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('receipt_number')
                    ->label('No. Penerimaan')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->icon('heroicon-o-document-text')
                    ->weight(FontWeight::Bold)
                    ->color('primary'),

                TextColumn::make('purchaseOrder.po_number')
                    ->label('No. PO')
                    ->searchable()
                    ->sortable()
                    ->url(fn($record) => route('filament.admin.resources.purchase-orders.view', $record->purchaseOrder))
                    ->color('info'),

                TextColumn::make('purchaseOrder.vendor.name')
                    ->label('Supplier')
                    ->searchable()
                    ->sortable()
                    ->weight(FontWeight::Medium),

                TextColumn::make('receipt_date')
                    ->label('Tanggal Terima')
                    ->date('d M Y')
                    ->sortable()
                    ->icon('heroicon-o-calendar'),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'draft' => 'warning',
                        'confirmed' => 'success',
                        'cancelled' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'draft' => 'DRAFT',
                        'confirmed' => 'DIKONFIRMASI',
                        'cancelled' => 'DIBATALKAN',
                        default => strtoupper($state),
                    })
                    ->sortable(),

                TextColumn::make('receivedBy.name')
                    ->label('Diterima Oleh')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('delivery_note_number')
                    ->label('No. Surat Jalan')
                    ->searchable()
                    ->placeholder('-')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d M Y, H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->label('Diperbarui')
                    ->dateTime('d M Y, H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'draft' => 'Draft',
                        'confirmed' => 'Dikonfirmasi',
                        'cancelled' => 'Dibatalkan',
                    ])
                    ->multiple(),

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
                        fn(GoodReceipt $record): bool =>
                        $record->status === GoodReceipt::STATUS_DRAFT
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
            ->defaultSort('receipt_date', 'desc');
    }
}
