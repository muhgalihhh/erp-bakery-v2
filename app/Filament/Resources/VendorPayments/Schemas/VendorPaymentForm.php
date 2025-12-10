<?php

namespace App\Filament\Resources\VendorPayments\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Placeholder;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Schema;
use App\Models\Vendor;
use App\Models\PurchaseOrder;
use Illuminate\Support\Facades\Auth;

class VendorPaymentForm
{
    public static function make(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('Pembayaran Supplier')
                    ->tabs([
                        // Tab 1: Payment Information
                        Tabs\Tab::make('Info Pembayaran')
                            ->schema([
                                Fieldset::make('Detail Pembayaran')
                                    ->schema([
                                        Placeholder::make('payment_number')
                                            ->label('No. Pembayaran')
                                            ->content(fn($record) => $record?->payment_number ?? 'Otomatis setelah disimpan')
                                            ->helperText('📝 Dibuat otomatis: PAY-YYYYMM-XXXX')
                                            ->hidden(fn($context) => $context === 'create'),

                                        Select::make('vendor_id')
                                            ->label('Supplier')
                                            ->relationship('vendor', 'name')
                                            ->searchable()
                                            ->preload()
                                            ->required()
                                            ->helperText('Pilih supplier yang dibayar')
                                            ->reactive()
                                            ->afterStateUpdated(function ($state, callable $set) {
                                                if ($state) {
                                                    $vendor = Vendor::find($state);
                                                    $set('_vendor_email', $vendor?->email);
                                                    $set('_vendor_phone', $vendor?->phone);
                                                }
                                            }),

                                        Placeholder::make('_vendor_info')
                                            ->label('Kontak Supplier')
                                            ->content(
                                                fn($get) =>
                                                $get('_vendor_email') || $get('_vendor_phone')
                                                ? "📧 {$get('_vendor_email')} | 📱 {$get('_vendor_phone')}"
                                                : 'Pilih supplier untuk melihat kontak'
                                            )
                                            ->hidden(fn($context) => $context === 'create'),

                                        Select::make('purchase_order_id')
                                            ->label('Referensi PO (Opsional)')
                                            ->relationship(
                                                'purchaseOrder',
                                                'po_number',
                                                fn($query, $get) => $query
                                                    ->when(
                                                        $get('vendor_id'),
                                                        fn($q, $vendorId) =>
                                                        $q->where('vendor_id', $vendorId)
                                                    )
                                                    ->whereIn('status', ['approved', 'received', 'partially_received'])
                                            )
                                            ->searchable()
                                            ->preload()
                                            ->helperText('Link ke PO tertentu (opsional)')
                                            ->reactive()
                                            ->afterStateUpdated(function ($state, callable $set) {
                                                if ($state) {
                                                    $po = PurchaseOrder::find($state);
                                                    if ($po) {
                                                        $set('amount', $po->total);
                                                        $set('_po_total', 'Rp ' . number_format((float) $po->total, 0, ',', '.'));
                                                    }
                                                }
                                            }),

                                        Placeholder::make('_po_total')
                                            ->label('Total PO')
                                            ->content(fn($get) => $get('_po_total') ?? 'Tidak ada PO dipilih')
                                            ->hidden(fn($get) => !$get('purchase_order_id')),
                                    ])
                                    ->columns(2),

                                Fieldset::make('Jumlah & Metode Pembayaran')
                                    ->schema([
                                        DatePicker::make('payment_date')
                                            ->label('Tanggal Bayar')
                                            ->required()
                                            ->default(now())
                                            ->native(false)
                                            ->maxDate(now())
                                            ->helperText('Kapan pembayaran dilakukan?'),

                                        TextInput::make('amount')
                                            ->label('Jumlah Bayar')
                                            ->required()
                                            ->numeric()
                                            ->prefix('Rp')
                                            ->step(0.01)
                                            ->helperText('Jumlah yang dibayarkan ke supplier'),

                                        Select::make('payment_method')
                                            ->label('Metode Pembayaran')
                                            ->options([
                                                'cash' => 'Tunai',
                                                'bank_transfer' => 'Transfer Bank',
                                                'check' => 'Cek',
                                                'giro' => 'Giro',
                                                'other' => 'Lainnya',
                                            ])
                                            ->default('bank_transfer')
                                            ->required()
                                            ->reactive()
                                            ->helperText('Bagaimana pembayaran dilakukan?'),

                                        TextInput::make('reference_number')
                                            ->label(fn($get) => match ($get('payment_method')) {
                                                'check' => 'No. Cek',
                                                'giro' => 'No. Giro',
                                                'bank_transfer' => 'No. Referensi Transfer',
                                                default => 'No. Referensi',
                                            })
                                            ->maxLength(100)
                                            ->helperText(fn($get) => match ($get('payment_method')) {
                                                'check' => 'Masukkan nomor cek',
                                                'giro' => 'Masukkan nomor giro',
                                                'bank_transfer' => 'Masukkan ID transaksi/transfer',
                                                default => 'Nomor referensi (opsional)',
                                            }),

                                        TextInput::make('bank_account')
                                            ->label('Rekening Bank')
                                            ->maxLength(100)
                                            ->helperText('Rekening tujuan/sumber')
                                            ->visible(fn($get) => in_array($get('payment_method'), ['bank_transfer', 'check', 'giro'])),

                                        Select::make('paid_by')
                                            ->label('Dibayar Oleh')
                                            ->relationship('paidBy', 'name')
                                            ->default(Auth::id())
                                            ->required()
                                            ->helperText('User yang memproses pembayaran'),

                                        Select::make('status')
                                            ->label('Status')
                                            ->options([
                                                'draft' => 'Draft',
                                                'confirmed' => 'Dikonfirmasi',
                                                'cancelled' => 'Dibatalkan',
                                            ])
                                            ->default('draft')
                                            ->required()
                                            ->helperText('Status pembayaran'),
                                    ])
                                    ->columns(3),

                                Fieldset::make('Informasi Tambahan')
                                    ->schema([
                                        Textarea::make('notes')
                                            ->label('Catatan')
                                            ->rows(3)
                                            ->placeholder('Catatan tambahan tentang pembayaran ini...')
                                            ->columnSpanFull(),
                                    ]),
                            ]),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
