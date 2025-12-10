<?php

namespace App\Filament\Resources\Products\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ProductsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('sku')
                    ->label('SKU')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->weight('bold'),

                ImageColumn::make('image_url')
                    ->label('Gambar')
                    ->circular()
                    ->defaultImageUrl(url('/images/no-image.png')),

                TextColumn::make('name')
                    ->label('Nama Produk')
                    ->searchable()
                    ->sortable()
                    ->description(fn($record) => $record->description)
                    ->limit(50),

                TextColumn::make('type')
                    ->label('Tipe')
                    ->badge()
                    ->colors([
                        'success' => 'finished',
                        'warning' => 'raw',
                        'info' => 'wip',
                        'gray' => 'service',
                        'danger' => 'consumable',
                    ])
                    ->formatStateUsing(fn($state) => match ($state) {
                        'raw' => 'Bahan Baku',
                        'wip' => 'WIP',
                        'finished' => 'Barang Jadi',
                        'service' => 'Jasa',
                        'consumable' => 'Habis Pakai',
                        default => $state,
                    }),

                IconColumn::make('is_sellable')
                    ->label('Dijual?')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('gray'),

                IconColumn::make('is_purchasable')
                    ->label('Dibeli?')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('gray'),

                TextColumn::make('selling_price')
                    ->label('Harga Jual')
                    ->money('IDR')
                    ->sortable(),

                TextColumn::make('purchase_price')
                    ->label('Harga Beli')
                    ->money('IDR')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('current_stock')
                    ->label('Stok')
                    ->formatStateUsing(fn($state, $record) => static::formatQuantity($state) . ' ' . $record->uom_stock)
                    ->sortable()
                    ->alignEnd()
                    ->color(fn($record) => $record->isLowStock() ? 'danger' : 'success'),

                TextColumn::make('minimum_stock')
                    ->label('Stok Min')
                    ->formatStateUsing(fn($state, $record) => static::formatQuantity($state) . ' ' . $record->uom_stock)
                    ->alignEnd()
                    ->toggleable(isToggledHiddenByDefault: true),

                IconColumn::make('is_active')
                    ->label('Aktif?')
                    ->boolean()
                    ->sortable(),

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

                TextColumn::make('deleted_at')
                    ->label('Dihapus')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('type')
                    ->label('Tipe Produk')
                    ->options([
                        'raw' => 'Bahan Baku',
                        'wip' => 'Work In Progress',
                        'finished' => 'Barang Jadi',
                        'service' => 'Jasa',
                        'consumable' => 'Habis Pakai',
                    ])
                    ->multiple(),

                SelectFilter::make('is_sellable')
                    ->label('Bisa Dijual?')
                    ->options([
                        1 => 'Ya (Dijual)',
                        0 => 'Tidak',
                    ]),

                SelectFilter::make('is_purchasable')
                    ->label('Bisa Dibeli?')
                    ->options([
                        1 => 'Ya (Dibeli)',
                        0 => 'Tidak',
                    ]),

                SelectFilter::make('is_active')
                    ->label('Status')
                    ->options([
                        1 => 'Aktif',
                        0 => 'Tidak Aktif',
                    ]),

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
                    ->label('Ubah'),
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
            ->defaultSort('name', 'asc')
            ->striped();
    }

    /**
     * Format quantity with smart decimal places
     * - Shows no decimals for whole numbers (10 → "10")
     * - Shows up to 2 decimals for fractional, removing trailing zeros
     */
    private static function formatQuantity(float $quantity): string
    {
        return rtrim(rtrim(number_format($quantity, 2, '.', ','), '0'), '.');
    }
}

