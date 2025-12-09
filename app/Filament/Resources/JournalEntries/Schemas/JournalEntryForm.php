<?php

namespace App\Filament\Resources\JournalEntries\Schemas;

use App\Models\ChartOfAccount;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class JournalEntryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([
                DatePicker::make('posting_date')
                    ->label('Posting Date')
                    ->required()
                    ->default(now())
                    ->native(false),

                TextInput::make('transaction_number')
                    ->label('Journal No.')
                    ->disabled()
                    ->dehydrated(false)
                    ->placeholder('Auto-generated'),

                Textarea::make('description')
                    ->label('Description')
                    ->required()
                    ->rows(3)
                    ->columnSpanFull(),

                Repeater::make('postings')
                    ->relationship()
                    ->label('Journal Postings (Debit & Credit)')
                    ->schema([
                        Select::make('account_id')
                            ->label('Account')
                            ->required()
                            ->searchable()
                            ->preload()
                            ->options(ChartOfAccount::orderBy('code')->pluck('name', 'id'))
                            ->getOptionLabelFromRecordUsing(fn($record) => "{$record->code} - {$record->name}"),

                        TextInput::make('debit')
                            ->label('Debit')
                            ->numeric()
                            ->default(0)
                            ->minValue(0)
                            ->prefix('Rp')
                            ->live(),

                        TextInput::make('credit')
                            ->label('Credit')
                            ->numeric()
                            ->default(0)
                            ->minValue(0)
                            ->prefix('Rp')
                            ->live(),

                        Textarea::make('line_description')
                            ->label('Line Description')
                            ->rows(2)
                            ->columnSpanFull(),
                    ])
                    ->columns(3)
                    ->defaultItems(2)
                    ->minItems(2)
                    ->addActionLabel('Add Line')
                    ->reorderable(false)
                    ->columnSpanFull(),

                Placeholder::make('balance_info')
                    ->label('Balance Check')
                    ->content(function ($get) {
                        $postings = $get('postings') ?? [];
                        $totalDebit = collect($postings)->sum('debit');
                        $totalCredit = collect($postings)->sum('credit');
                        $diff = abs($totalDebit - $totalCredit);

                        $status = $diff < 0.01 ? '✅ BALANCED' : '❌ UNBALANCED';

                        return "Total Debit: Rp " . number_format($totalDebit, 2) .
                            " | Total Credit: Rp " . number_format($totalCredit, 2) .
                            " | Difference: Rp " . number_format($diff, 2) .
                            " | Status: {$status}";
                    })
                    ->columnSpanFull(),
            ]);
    }
}
