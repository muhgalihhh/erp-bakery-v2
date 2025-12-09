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
                    ->label('MO Number')
                    ->searchable()
                    ->sortable()
                    ->weight(FontWeight::Bold)
                    ->copyable(),

                TextColumn::make('production_date')
                    ->label('Production Date')
                    ->date('d M Y')
                    ->sortable(),

                TextColumn::make('product.name')
                    ->label('Product')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('quantity_to_produce')
                    ->label('Target Qty')
                    ->formatStateUsing(fn($state) => number_format((float) $state, 0))
                    ->alignEnd(),

                TextColumn::make('quantity_produced')
                    ->label('Produced')
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
                    ->formatStateUsing(fn($state) => ucwords(str_replace('_', ' ', $state))),

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
                    ->label('Started')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('actual_finish_time')
                    ->label('Finished')
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
                    ->options([
                        'draft' => 'Draft',
                        'confirmed' => 'Confirmed',
                        'in_progress' => 'In Progress',
                        'completed' => 'Completed',
                        'cancelled' => 'Cancelled',
                    ]),
                SelectFilter::make('product_id')
                    ->relationship('product', 'name')
                    ->searchable()
                    ->preload(),
                TrashedFilter::make(),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make()
                    ->hidden(fn($record) => in_array($record->status, ['completed', 'cancelled'])),

                // Confirm Action
                Action::make('confirm')
                    ->label('Confirm')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalDescription('This will prepare materials based on BOM. Continue?')
                    ->visible(fn($record) => $record->status === 'draft')
                    ->action(function (ManufacturingOrder $record) {
                        try {
                            $record->confirm();

                            Notification::make()
                                ->success()
                                ->title('MO Confirmed')
                                ->body("MO {$record->mo_number} confirmed. Materials prepared.")
                                ->send();
                        } catch (\Exception $e) {
                            Notification::make()
                                ->danger()
                                ->title('Failed to Confirm')
                                ->body($e->getMessage())
                                ->send();
                        }
                    }),

                // Start Production Action
                Action::make('start')
                    ->label('Start')
                    ->icon('heroicon-o-play')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->modalDescription('Start production now?')
                    ->visible(fn($record) => $record->status === 'confirmed')
                    ->action(function (ManufacturingOrder $record) {
                        try {
                            $record->start();

                            Notification::make()
                                ->success()
                                ->title('Production Started')
                                ->body("MO {$record->mo_number} is now in progress.")
                                ->send();
                        } catch (\Exception $e) {
                            Notification::make()
                                ->danger()
                                ->title('Failed to Start')
                                ->body($e->getMessage())
                                ->send();
                        }
                    }),

                // Complete Production Action
                Action::make('complete')
                    ->label('Complete')
                    ->icon('heroicon-o-check-badge')
                    ->color('success')
                    ->form([
                        \Filament\Forms\Components\TextInput::make('quantity_produced')
                            ->label('Actual Quantity Produced')
                            ->required()
                            ->numeric()
                            ->minValue(0)
                            ->suffix('units')
                            ->default(fn($record) => $record->quantity_to_produce)
                            ->helperText('How many units were successfully produced?'),

                        \Filament\Forms\Components\TextInput::make('quantity_scrapped')
                            ->label('Quantity Scrapped/Rejected')
                            ->numeric()
                            ->minValue(0)
                            ->default(0)
                            ->suffix('units')
                            ->helperText('Defective or rejected units'),

                        \Filament\Forms\Components\TextInput::make('labor_cost')
                            ->label('Labor Cost')
                            ->required()
                            ->numeric()
                            ->minValue(0)
                            ->default(0)
                            ->prefix('Rp')
                            ->helperText('Total labor cost for this production'),

                        \Filament\Forms\Components\TextInput::make('overhead_cost')
                            ->label('Overhead Cost')
                            ->required()
                            ->numeric()
                            ->minValue(0)
                            ->default(0)
                            ->prefix('Rp')
                            ->helperText('Gas, electricity, machine depreciation, etc.'),

                        \Filament\Forms\Components\Textarea::make('completion_notes')
                            ->label('Completion Notes')
                            ->rows(3)
                            ->placeholder('Any notes about this production run...')
                            ->columnSpanFull(),
                    ])
                    ->modalWidth('2xl')
                    ->modalHeading('Complete Production')
                    ->modalDescription('Enter production results and costs')
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
                                ->title('Production Completed!')
                                ->body("MO {$record->mo_number} completed. HPP: Rp " . number_format($record->cost_per_unit, 2) . "/unit")
                                ->send();
                        } catch (\Exception $e) {
                            Notification::make()
                                ->danger()
                                ->title('Failed to Complete')
                                ->body($e->getMessage())
                                ->persistent()
                                ->send();
                        }
                    }),

                // Cancel Action
                Action::make('cancel')
                    ->label('Cancel')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalDescription('Cancel this manufacturing order?')
                    ->visible(fn($record) => !in_array($record->status, ['completed', 'cancelled']))
                    ->action(function (ManufacturingOrder $record) {
                        try {
                            $record->cancel();

                            Notification::make()
                                ->warning()
                                ->title('MO Cancelled')
                                ->body("MO {$record->mo_number} has been cancelled.")
                                ->send();
                        } catch (\Exception $e) {
                            Notification::make()
                                ->danger()
                                ->title('Failed to Cancel')
                                ->body($e->getMessage())
                                ->send();
                        }
                    }),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ]);
    }
}

