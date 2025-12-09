<?php

namespace App\Filament\Resources\Vendors\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Schema;

class VendorForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Fieldset::make('Basic Information')
                    ->schema([
                        TextInput::make('vendor_code')
                            ->label('Vendor Code')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->placeholder('VND-001')
                            ->helperText('Unique vendor code'),

                        TextInput::make('name')
                            ->label('Vendor Name')
                            ->required()
                            ->placeholder('PT Sumber Roti Sejahtera')
                            ->columnSpanFull(),

                        TextInput::make('contact_person')
                            ->label('Contact Person')
                            ->placeholder('Budi Santoso'),

                        TextInput::make('phone')
                            ->label('Phone Number')
                            ->tel()
                            ->placeholder('08123456789'),

                        TextInput::make('email')
                            ->label('Email')
                            ->email()
                            ->placeholder('vendor@example.com'),

                        Textarea::make('address')
                            ->label('Address')
                            ->rows(3)
                            ->placeholder('Jl. Raya Industri No. 123, Jakarta')
                            ->columnSpanFull(),
                    ])
                    ->columns(3),

                Fieldset::make('Tax & Payment')
                    ->schema([
                        TextInput::make('tax_id')
                            ->label('Tax ID / NPWP')
                            ->placeholder('01.234.567.8-901.000'),

                        TextInput::make('payment_terms_days')
                            ->label('Payment Terms')
                            ->numeric()
                            ->default(30)
                            ->suffix('days')
                            ->helperText('NET payment terms')
                            ->required(),
                    ])
                    ->columns(2),

                Fieldset::make('Bank Information')
                    ->schema([
                        TextInput::make('bank_name')
                            ->label('Bank Name')
                            ->placeholder('Bank Mandiri'),

                        TextInput::make('bank_account_number')
                            ->label('Account Number')
                            ->placeholder('1234567890'),

                        TextInput::make('bank_account_name')
                            ->label('Account Holder')
                            ->placeholder('PT Sumber Roti Sejahtera'),
                    ])
                    ->columns(3),

                Fieldset::make('Status & Notes')
                    ->schema([
                        Toggle::make('is_active')
                            ->label('Active Vendor')
                            ->default(true)
                            ->helperText('Only active vendors in PO'),

                        Textarea::make('notes')
                            ->label('Internal Notes')
                            ->rows(3)
                            ->placeholder('Notes about vendor...')
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}

