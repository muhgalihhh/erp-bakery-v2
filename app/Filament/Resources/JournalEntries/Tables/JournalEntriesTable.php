<?php

namespace App\Filament\Resources\JournalEntries\Tables;

use App\Models\JournalEntry;
use App\Services\JournalService;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Notifications\Notification;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class JournalEntriesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('posting_date', 'desc')
            ->columns([
                TextColumn::make('transaction_number')
                    ->label('Journal No.')
                    ->searchable()
                    ->sortable()
                    ->weight(FontWeight::Bold)
                    ->copyable(),

                TextColumn::make('posting_date')
                    ->label('Posting Date')
                    ->date('d M Y')
                    ->sortable(),

                TextColumn::make('description')
                    ->label('Description')
                    ->searchable()
                    ->limit(50)
                    ->tooltip(fn($record) => $record->description),

                TextColumn::make('referenceable_type')
                    ->label('Source Type')
                    ->formatStateUsing(fn($state) => $state ? class_basename($state) : '-')
                    ->badge()
                    ->color('info'),

                TextColumn::make('total_amount')
                    ->label('Amount')
                    ->money('IDR')
                    ->getStateUsing(function ($record) {
                        return $record->postings()->sum('debit');
                    })
                    ->alignEnd(),

                IconColumn::make('is_posted')
                    ->label('Posted')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('warning'),

                TextColumn::make('posted_at')
                    ->label('Posted At')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('creator.name')
                    ->label('Created By')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('poster.name')
                    ->label('Posted By')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('is_posted')
                    ->label('Status')
                    ->options([
                        '1' => 'Posted',
                        '0' => 'Draft',
                    ]),
                TrashedFilter::make(),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make()
                    ->hidden(fn($record) => $record->is_posted),
                Action::make('post')
                    ->label('Post')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalDescription('Once posted, this journal entry cannot be edited. Are you sure?')
                    ->visible(fn($record) => !$record->is_posted)
                    ->action(function (JournalEntry $record) {
                        $journalService = new JournalService();
                        $journalService->postJournalEntry($record);

                        Notification::make()
                            ->success()
                            ->title('Journal Entry Posted')
                            ->body("Journal {$record->transaction_number} has been posted successfully.")
                            ->send();
                    }),
                Action::make('unpost')
                    ->label('Unpost')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalDescription('This will unpost the journal entry. Only super admins can do this.')
                    ->visible(fn($record) => $record->is_posted)
                    ->action(function (JournalEntry $record) {
                        $journalService = new JournalService();
                        $journalService->unpostJournalEntry($record);

                        Notification::make()
                            ->warning()
                            ->title('Journal Entry Unposted')
                            ->body("Journal {$record->transaction_number} has been unposted.")
                            ->send();
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
