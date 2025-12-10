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
                Fieldset::make('Informasi Dasar')
                    ->schema([
                        TextInput::make('vendor_code')
                            ->label('Kode Supplier')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->placeholder('VND-001')
                            ->helperText('Kode unik untuk supplier ini'),

                        TextInput::make('name')
                            ->label('Nama Supplier')
                            ->required()
                            ->placeholder('PT Sumber Tepung Sejahtera')
                            ->columnSpanFull(),

                        TextInput::make('contact_person')
                            ->label('Kontak Person')
                            ->placeholder('Budi Santoso')
                            ->helperText('Nama orang yang dihubungi'),

                        TextInput::make('phone')
                            ->label('No. Telepon')
                            ->tel()
                            ->placeholder('08123456789'),

                        TextInput::make('email')
                            ->label('Email')
                            ->email()
                            ->placeholder('supplier@example.com'),

                        Textarea::make('address')
                            ->label('Alamat')
                            ->rows(3)
                            ->placeholder('Jl. Raya Industri No. 123, Jakarta')
                            ->columnSpanFull(),
                    ])
                    ->columns(3),

                Fieldset::make('Pajak & Pembayaran')
                    ->schema([
                        TextInput::make('tax_id')
                            ->label('NPWP')
                            ->placeholder('01.234.567.8-901.000')
                            ->helperText('Nomor Pokok Wajib Pajak (opsional)'),

                        TextInput::make('payment_terms_days')
                            ->label('Termin Pembayaran')
                            ->numeric()
                            ->default(30)
                            ->suffix('hari')
                            ->helperText('Contoh: 7 hari = NET 7, 30 hari = NET 30')
                            ->required(),
                    ])
                    ->columns(2),

                Fieldset::make('Informasi Bank')
                    ->schema([
                        TextInput::make('bank_name')
                            ->label('Nama Bank')
                            ->placeholder('Bank Mandiri'),

                        TextInput::make('bank_account_number')
                            ->label('No. Rekening')
                            ->placeholder('1234567890'),

                        TextInput::make('bank_account_name')
                            ->label('Atas Nama')
                            ->placeholder('PT Sumber Tepung Sejahtera')
                            ->helperText('Nama pemilik rekening'),
                    ])
                    ->columns(3),

                Fieldset::make('Status & Catatan')
                    ->schema([
                        Toggle::make('is_active')
                            ->label('Supplier Aktif')
                            ->default(true)
                            ->helperText('Hanya supplier aktif yang muncul di PO'),

                        Textarea::make('notes')
                            ->label('Catatan Internal')
                            ->rows(3)
                            ->placeholder('Catatan tentang supplier ini...')
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}

