<?php

namespace App\Filament\Resources\ManufacturingOrders\Tables;

use App\Models\ManufacturingOrder;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Notifications\Notification;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class ManufacturingOrdersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('production_date', 'desc')
            ->columns([
                TextColumn::make('mo_number')
                    ->label('No. Order Produksi')
                    ->searchable()
                    ->sortable()
                    ->weight(FontWeight::Bold)
                    ->copyable(),

                TextColumn::make('production_date')
                    ->label('Tanggal Produksi')
                    ->date('d M Y')
                    ->sortable(),

                TextColumn::make('product.name')
                    ->label('Produk')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('quantity_to_produce')
                    ->label('Target')
                    ->formatStateUsing(fn($state) => number_format((float) $state, 0))
                    ->alignEnd(),

                TextColumn::make('quantity_produced')
                    ->label('Diproduksi')
                    ->formatStateUsing(fn($state) => number_format((float) $state, 0))
                    ->alignEnd()
                    ->color(fn($record) => $record->quantity_produced >= $record->quantity_to_produce ? 'success' : 'warning'),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn($state) => match ($state) {
                        'draft' => 'gray',
                        'confirmed' => 'info',
                        'in_progress' => 'warning',
                        'completed' => 'success',
                        'cancelled' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn($state) => match ($state) {
                        'draft' => 'Draft',
                        'confirmed' => 'Dikonfirmasi',
                        'in_progress' => 'Sedang Proses',
                        'completed' => 'Selesai',
                        'cancelled' => 'Dibatalkan',
                        default => ucwords(str_replace('_', ' ', $state))
                    }),

                TextColumn::make('total_cost')
                    ->label('Total HPP')
                    ->money('IDR')
                    ->sortable()
                    ->alignEnd()
                    ->toggleable(),

                TextColumn::make('cost_per_unit')
                    ->label('HPP/Unit')
                    ->money('IDR')
                    ->sortable()
                    ->alignEnd()
                    ->toggleable(),

                TextColumn::make('supervisor.name')
                    ->label('Supervisor')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('actual_start_time')
                    ->label('Mulai')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('actual_finish_time')
                    ->label('Selesai')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'draft' => 'Draft',
                        'confirmed' => 'Dikonfirmasi',
                        'in_progress' => 'Sedang Proses',
                        'completed' => 'Selesai',
                        'cancelled' => 'Dibatalkan',
                    ]),
                SelectFilter::make('product_id')
                    ->label('Produk')
                    ->relationship('product', 'name')
                    ->searchable()
                    ->preload(),
                TrashedFilter::make(),
            ])
            ->recordActions([
                ViewAction::make()
                    ->label('Lihat'),
                EditAction::make()
                    ->label('Ubah')
                    ->hidden(fn($record) => in_array($record->status, ['completed', 'cancelled'])),

                // Confirm Action
                Action::make('confirm')
                    ->label('Konfirmasi')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Konfirmasi Order Produksi')
                    ->modalDescription('Ini akan menyiapkan bahan baku berdasarkan resep. Lanjutkan?')
                    ->visible(fn($record) => $record->status === 'draft')
                    ->action(function (ManufacturingOrder $record) {
                        try {
                            $record->confirm();

                            Notification::make()
                                ->success()
                                ->title('Order Produksi Dikonfirmasi')
                                ->body("Order {$record->mo_number} telah dikonfirmasi. Bahan baku siap diproses.")
                                ->send();
                        } catch (\Exception $e) {
                            Notification::make()
                                ->danger()
                                ->title('Gagal Konfirmasi')
                                ->body($e->getMessage())
                                ->send();
                        }
                    }),

                // Start Production Action
                Action::make('start')
                    ->label('Mulai Produksi')
                    ->icon('heroicon-o-play')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->modalHeading('Mulai Produksi')
                    ->modalDescription('Mulai proses produksi sekarang?')
                    ->visible(fn($record) => $record->status === 'confirmed')
                    ->action(function (ManufacturingOrder $record) {
                        try {
                            $record->start();

                            Notification::make()
                                ->success()
                                ->title('Produksi Dimulai')
                                ->body("Order {$record->mo_number} sedang dalam proses produksi.")
                                ->send();
                        } catch (\Exception $e) {
                            Notification::make()
                                ->danger()
                                ->title('Gagal Memulai')
                                ->body($e->getMessage())
                                ->send();
                        }
                    }),

                // Complete Production Action
                Action::make('complete')
                    ->label('Selesaikan')
                    ->icon('heroicon-o-check-badge')
                    ->color('success')
                    ->form([
                        \Filament\Forms\Components\TextInput::make('quantity_produced')
                            ->label('Jumlah Aktual Diproduksi')
                            ->required()
                            ->numeric()
                            ->minValue(0)
                            ->suffix('unit')
                            ->default(fn($record) => $record->quantity_to_produce)
                            ->helperText('Berapa unit yang berhasil diproduksi?'),

                        \Filament\Forms\Components\TextInput::make('quantity_scrapped')
                            ->label('Jumlah Reject/Gagal')
                            ->numeric()
                            ->minValue(0)
                            ->default(0)
                            ->suffix('unit')
                            ->helperText('Unit yang cacat atau ditolak'),

                        \Filament\Forms\Components\TextInput::make('labor_cost')
                            ->label('Biaya Tenaga Kerja')
                            ->required()
                            ->numeric()
                            ->minValue(0)
                            ->default(0)
                            ->prefix('Rp')
                            ->helperText('Total biaya tenaga kerja untuk produksi ini'),

                        \Filament\Forms\Components\TextInput::make('overhead_cost')
                            ->label('Biaya Overhead')
                            ->required()
                            ->numeric()
                            ->minValue(0)
                            ->default(0)
                            ->prefix('Rp')
                            ->helperText('Gas, listrik, depresiasi mesin, dll.'),

                        \Filament\Forms\Components\Textarea::make('completion_notes')
                            ->label('Catatan Penyelesaian')
                            ->rows(3)
                            ->placeholder('Catatan tentang produksi ini...')
                            ->columnSpanFull(),
                    ])
                    ->modalWidth('2xl')
                    ->modalHeading('Selesaikan Produksi')
                    ->modalDescription('Masukkan hasil produksi dan biaya')
                    ->visible(fn($record) => $record->status === 'in_progress')
                    ->action(function (ManufacturingOrder $record, array $data) {
                        try {
                            // Update the record with form data
                            $record->quantity_produced = $data['quantity_produced'];
                            $record->quantity_scrapped = $data['quantity_scrapped'] ?? 0;
                            $record->labor_cost = $data['labor_cost'];
                            $record->overhead_cost = $data['overhead_cost'];
                            $record->completion_notes = $data['completion_notes'] ?? null;
                            $record->save();

                            // Now complete the production
                            $record->complete();

                            Notification::make()
                                ->success()
                                ->title('Produksi Selesai!')
                                ->body("Order {$record->mo_number} selesai. HPP: Rp " . number_format((float) $record->cost_per_unit, 0) . "/unit")
                                ->send();
                        } catch (\Exception $e) {
                            Notification::make()
                                ->danger()
                                ->title('Gagal Menyelesaikan')
                                ->body($e->getMessage())
                                ->persistent()
                                ->send();
                        }
                    }),

                // Cancel Action
                Action::make('cancel')
                    ->label('Batalkan')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading('Batalkan Order Produksi')
                    ->modalDescription('Batalkan order produksi ini?')
                    ->visible(fn($record) => !in_array($record->status, ['completed', 'cancelled']))
                    ->action(function (ManufacturingOrder $record) {
                        try {
                            $record->cancel();

                            Notification::make()
                                ->warning()
                                ->title('Order Produksi Dibatalkan')
                                ->body("Order {$record->mo_number} telah dibatalkan.")
                                ->send();
                        } catch (\Exception $e) {
                            Notification::make()
                                ->danger()
                                ->title('Gagal Membatalkan')
                                ->body($e->getMessage())
                                ->send();
                        }
                    }),
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
            ]);
    }
}

