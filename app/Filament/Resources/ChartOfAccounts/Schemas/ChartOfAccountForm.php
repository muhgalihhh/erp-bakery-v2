<?php

namespace App\Filament\Resources\ChartOfAccounts\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Schema;

class ChartOfAccountForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Fieldset::make('Account Information')
                    ->schema([
                        TextInput::make('code')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(50)
                            ->placeholder('1-1310')
                            ->helperText('Format: 1-1310 (hierarkis)'),

                        TextInput::make('name')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Persediaan Tepung & Gula'),

                        Select::make('type')
                            ->required()
                            ->options([
                                'asset' => 'Asset (Aset)',
                                'liability' => 'Liability (Kewajiban)',
                                'equity' => 'Equity (Modal)',
                                'revenue' => 'Revenue (Pendapatan)',
                                'expense' => 'Expense (Biaya)',
                            ])
                            ->native(false)
                            ->searchable(),

                        Select::make('subtype')
                            ->options([
                                'current_asset' => 'Current Asset (Aset Lancar)',
                                'fixed_asset' => 'Fixed Asset (Aset Tetap)',
                                'current_liability' => 'Current Liability (Kewajiban Jangka Pendek)',
                                'long_term_liability' => 'Long-term Liability (Kewajiban Jangka Panjang)',
                                'equity' => 'Equity (Modal)',
                                'cogs' => 'COGS (Harga Pokok Penjualan)',
                                'operating_expense' => 'Operating Expense (Biaya Operasional)',
                                'other_expense' => 'Other Expense (Biaya Lain-lain)',
                                'sales_revenue' => 'Sales Revenue (Pendapatan Penjualan)',
                                'other_revenue' => 'Other Revenue (Pendapatan Lain-lain)',
                            ])
                            ->native(false)
                            ->searchable(),

                        Select::make('parent_id')
                            ->relationship('parent', 'name')
                            ->searchable()
                            ->preload()
                            ->label('Parent Account')
                            ->helperText('Kosongkan jika ini adalah akun utama'),

                        TextInput::make('currency')
                            ->required()
                            ->default('IDR')
                            ->maxLength(3)
                            ->placeholder('IDR, USD, EUR'),
                    ])
                    ->columns(2),

                Fieldset::make('Additional Information')
                    ->schema([
                        Textarea::make('description')
                            ->rows(3)
                            ->placeholder('Deskripsi tambahan untuk akun ini'),

                        Toggle::make('is_active')
                            ->default(true)
                            ->label('Active')
                            ->helperText('Akun tidak aktif tidak bisa digunakan untuk transaksi baru'),
                    ]),
            ]);
    }
}

