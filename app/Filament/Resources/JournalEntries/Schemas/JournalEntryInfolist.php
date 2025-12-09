<?php

namespace App\Filament\Resources\JournalEntries\Schemas;

use App\Models\JournalEntry;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;
use Filament\Support\Enums\FontWeight;

class JournalEntryInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(3)
            ->components([
                TextEntry::make('transaction_number')
                    ->label('Journal No.')
                    ->weight(FontWeight::Bold)
                    ->copyable(),

                TextEntry::make('posting_date')
                    ->label('Posting Date')
                    ->date('d M Y'),

                IconEntry::make('is_posted')
                    ->label('Status')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('warning'),

                TextEntry::make('description')
                    ->label('Description')
                    ->columnSpanFull(),

                TextEntry::make('referenceable_type')
                    ->label('Source Type')
                    ->formatStateUsing(fn($state) => $state ? class_basename($state) : '-')
                    ->badge()
                    ->color('info'),

                TextEntry::make('referenceable_id')
                    ->label('Source ID')
                    ->placeholder('-'),

                TextEntry::make('posted_at')
                    ->label('Posted At')
                    ->dateTime('d M Y H:i')
                    ->placeholder('Not posted yet'),

                RepeatableEntry::make('postings')
                    ->label('Journal Postings (Debit & Credit)')
                    ->schema([
                        TextEntry::make('account.code')
                            ->label('Account Code')
                            ->weight(FontWeight::Bold),

                        TextEntry::make('account.name')
                            ->label('Account Name'),

                        TextEntry::make('debit')
                            ->label('Debit')
                            ->money('IDR')
                            ->color('success')
                            ->weight(fn($state) => $state > 0 ? FontWeight::Bold : FontWeight::Medium),

                        TextEntry::make('credit')
                            ->label('Credit')
                            ->money('IDR')
                            ->color('danger')
                            ->weight(fn($state) => $state > 0 ? FontWeight::Bold : FontWeight::Medium),

                        TextEntry::make('line_description')
                            ->label('Description')
                            ->placeholder('-')
                            ->columnSpanFull(),
                    ])
                    ->columns(4)
                    ->contained(false)
                    ->columnSpanFull(),

                TextEntry::make('total_debit')
                    ->label('Total Debit')
                    ->money('IDR')
                    ->weight(FontWeight::Bold)
                    ->color('success')
                    ->getStateUsing(fn($record) => $record->postings()->sum('debit')),

                TextEntry::make('total_credit')
                    ->label('Total Credit')
                    ->money('IDR')
                    ->weight(FontWeight::Bold)
                    ->color('danger')
                    ->getStateUsing(fn($record) => $record->postings()->sum('credit')),

                TextEntry::make('creator.name')
                    ->label('Created By')
                    ->columnStart(1),

                TextEntry::make('created_at')
                    ->label('Created At')
                    ->dateTime('d M Y H:i'),

                TextEntry::make('poster.name')
                    ->label('Posted By')
                    ->placeholder('-'),

                TextEntry::make('poster.name')
                    ->label('Posted By')
                    ->placeholder('-'),

                TextEntry::make('posted_at')
                    ->label('Posted At')
                    ->dateTime('d M Y H:i')
                    ->placeholder('-'),

                TextEntry::make('updated_at')
                    ->label('Last Updated')
                    ->dateTime('d M Y H:i'),

                TextEntry::make('deleted_at')
                    ->label('Deleted At')
                    ->dateTime('d M Y H:i')
                    ->visible(fn(JournalEntry $record): bool => $record->trashed()),
            ]);
    }
}
