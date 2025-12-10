<?php

namespace App\Filament\Resources\PurchaseOrders\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Placeholder;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Schema;
use App\Models\Vendor;
use App\Models\Product;

class PurchaseOrderForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('Purchase Order')
                    ->tabs([
                        // Tab 1: PO Information
                        Tabs\Tab::make('Informasi PO')
                            ->schema([
                                Fieldset::make('Supplier & Detail PO')
                                    ->schema([
                                        Placeholder::make('po_number')
                                            ->label('No. PO')
                                            ->content(fn($record) => $record?->po_number ?? 'Otomatis setelah disimpan')
                                            ->helperText('📝 Dibuat otomatis: PO-YYYYMM-XXXX')
                                            ->hidden(fn($context) => $context === 'create')
                                            ->columnSpan(2),

                                        Select::make('vendor_id')
                                            ->label('Supplier')
                                            ->relationship(
                                                'vendor',
                                                'name',
                                                fn($query) =>
                                                $query->where('is_active', true)
                                            )
                                            ->searchable()
                                            ->preload()
                                            ->required()
                                            ->helperText('Pilih supplier aktif')
                                            ->columnSpan(fn($context) => $context === 'create' ? 2 : 1),

                                        DatePicker::make('order_date')
                                            ->label('Tanggal Order')
                                            ->required()
                                            ->default(now())
                                            ->native(false),

                                        DatePicker::make('expected_delivery_date')
                                            ->label('Estimasi Pengiriman')
                                            ->native(false)
                                            ->helperText('Kapan diharapkan dikirim?'),

                                        Select::make('status')
                                            ->label('Status')
                                            ->options([
                                                'draft' => 'Draft',
                                                'pending' => 'Menunggu Persetujuan',
                                                'approved' => 'Disetujui',
                                                'received' => 'Diterima',
                                                'cancelled' => 'Dibatalkan',
                                            ])
                                            ->default('draft')
                                            ->required()
                                            ->helperText('Status PO saat ini'),
                                    ])
                                    ->columns(3),

                                Fieldset::make('Ringkasan Keuangan')
                                    ->schema([
                                        TextInput::make('shipping_cost')
                                            ->label('Biaya Kirim')
                                            ->numeric()
                                            ->default(0)
                                            ->prefix('Rp')
                                            ->helperText('Biaya tambahan pengiriman'),

                                        Placeholder::make('subtotal_display')
                                            ->label('Subtotal')
                                            ->content(fn($get) => 'Rp ' . number_format((float) ($get('subtotal') ?? 0), 0)),

                                        Placeholder::make('discount_display')
                                            ->label('Total Diskon')
                                            ->content(fn($get) => 'Rp ' . number_format((float) ($get('discount_amount') ?? 0), 0)),

                                        Placeholder::make('tax_display')
                                            ->label('Total Pajak')
                                            ->content(fn($get) => 'Rp ' . number_format((float) ($get('tax_amount') ?? 0), 0)),

                                        Placeholder::make('total_display')
                                            ->label('Total Keseluruhan')
                                            ->content(fn($get) => 'Rp ' . number_format((float) ($get('total') ?? 0), 0)),
                                    ])
                                    ->columns(5)
                                    ->hidden(fn($get) => !$get('id')), // Only show on edit

                                Fieldset::make('Catatan & Syarat')
                                    ->schema([
                                        Textarea::make('notes')
                                            ->label('Catatan Internal')
                                            ->rows(2)
                                            ->placeholder('Catatan internal untuk PO ini...')
                                            ->columnSpanFull(),

                                        Textarea::make('terms_and_conditions')
                                            ->label('Syarat & Ketentuan')
                                            ->rows(3)
                                            ->placeholder('Termin pembayaran, syarat pengiriman, dll.')
                                            ->columnSpanFull(),
                                    ]),
                            ]),

                        // Tab 2: Items (Line Items dengan Repeater)
                        Tabs\Tab::make('Item Pembelian')
                            ->schema([
                                Repeater::make('items')
                                    ->relationship('items')
                                    ->schema([
                                        // Row 1: Product Selection - Full Width
                                        Select::make('product_id')
                                            ->label('Produk / Bahan')
                                            ->relationship(
                                                'product',
                                                'name',
                                                fn($query) =>
                                                $query->whereIn('type', ['raw', 'wip', 'consumable'])
                                                    ->where('is_active', true)
                                            )
                                            ->searchable()
                                            ->preload()
                                            ->required()
                                            ->helperText('Pilih bahan baku atau konsumsi')
                                            ->columnSpanFull()
                                            ->reactive()
                                            ->afterStateUpdated(function ($state, callable $set) {
                                                if ($state) {
                                                    $product = Product::find($state);
                                                    if ($product) {
                                                        $set('unit_price', $product->purchase_price ?? 0);
                                                    }
                                                }
                                            }),

                                        // Row 2: Quantities & Prices - 4 columns
                                        TextInput::make('quantity')
                                            ->label('Jumlah')
                                            ->required()
                                            ->numeric()
                                            ->default(1)
                                            ->step(0.01)
                                            ->reactive()
                                            ->afterStateUpdated(
                                                fn($state, callable $set, $get) =>
                                                self::calculateItemTotal($set, $get)
                                            )
                                            ->suffix(fn($get) => Product::find($get('product_id'))?->uom_purchase ?? 'unit')
                                            ->helperText(fn($get) => static::getUomConversionInfo($get))
                                            ->columnSpan(1),

                                        TextInput::make('unit_price')
                                            ->label('Harga Satuan')
                                            ->required()
                                            ->numeric()
                                            ->prefix('Rp')
                                            ->default(0)
                                            ->reactive()
                                            ->afterStateUpdated(
                                                fn($state, callable $set, $get) =>
                                                self::calculateItemTotal($set, $get)
                                            )
                                            ->columnSpan(1),

                                        TextInput::make('discount_percentage')
                                            ->label('Diskon %')
                                            ->numeric()
                                            ->suffix('%')
                                            ->default(0)
                                            ->minValue(0)
                                            ->maxValue(100)
                                            ->reactive()
                                            ->afterStateUpdated(
                                                fn($state, callable $set, $get) =>
                                                self::calculateItemTotal($set, $get)
                                            )
                                            ->columnSpan(1),

                                        TextInput::make('tax_percentage')
                                            ->label('Pajak %')
                                            ->numeric()
                                            ->suffix('%')
                                            ->default(0)
                                            ->reactive()
                                            ->afterStateUpdated(
                                                fn($state, callable $set, $get) =>
                                                self::calculateItemTotal($set, $get)
                                            )
                                            ->columnSpan(1),

                                        // Row 3: Total Display - Full Width
                                        Placeholder::make('total_display')
                                            ->label('Total Item')
                                            ->content(fn($get) => 'Rp ' . number_format((float) ($get('total') ?? 0), 0))
                                            ->extraAttributes(['class' => 'text-success-600 font-bold text-lg'])
                                            ->columnSpanFull(),

                                        // Row 4: Notes - Full Width
                                        Textarea::make('notes')
                                            ->label('Catatan Item')
                                            ->rows(2)
                                            ->placeholder('Catatan khusus untuk item ini...')
                                            ->columnSpanFull(),
                                    ])
                                    ->columns(4)
                                    ->defaultItems(0)
                                    ->addActionLabel('➕ Tambah Item')
                                    ->reorderable()
                                    ->reorderableWithButtons()
                                    ->collapsible()
                                    ->cloneable()
                                    ->deleteAction(
                                        fn($action) => $action
                                            ->requiresConfirmation()
                                            ->modalHeading('Hapus Item?')
                                            ->modalDescription('Apakah Anda yakin?')
                                    )
                                    ->itemLabel(
                                        fn(array $state): ?string =>
                                        $state['product_id']
                                        ? '📦 ' . Product::find($state['product_id'])?->name .
                                        ' • Jml: ' . ($state['quantity'] ?? 0) . ' × Rp ' . number_format((float) ($state['unit_price'] ?? 0), 0) .
                                        ' = Rp ' . number_format((float) ($state['total'] ?? 0), 0)
                                        : '🆕 Item Baru'
                                    )
                                    ->columnSpanFull()
                                    ->live(),
                            ]),
                    ])
                    ->columnSpanFull(),
            ]);
    }

    /**
     * Calculate item total based on quantity, price, discount, and tax
     */
    private static function calculateItemTotal(callable $set, callable $get): void
    {
        $quantity = (float) ($get('quantity') ?? 0);
        $unitPrice = (float) ($get('unit_price') ?? 0);
        $discountPercentage = (float) ($get('discount_percentage') ?? 0);
        $taxPercentage = (float) ($get('tax_percentage') ?? 0);

        // Calculate subtotal
        $subtotal = $quantity * $unitPrice;
        $set('subtotal', $subtotal);

        // Calculate discount
        $discountAmount = $subtotal * ($discountPercentage / 100);
        $set('discount_amount', $discountAmount);

        // After discount
        $afterDiscount = $subtotal - $discountAmount;

        // Calculate tax
        $taxAmount = $afterDiscount * ($taxPercentage / 100);
        $set('tax_amount', $taxAmount);

        // Calculate total
        $total = $afterDiscount + $taxAmount;
        $set('total', $total);
    }

    /**
     * Get UoM conversion info for display
     */
    private static function getUomConversionInfo(callable $get): ?string
    {
        $product = Product::find($get('product_id'));
        if ($product && $product->conversion_purchase_to_stock) {
            $conversionValue = static::formatNumber((float) $product->conversion_purchase_to_stock);
            return "1 {$product->uom_purchase} = {$conversionValue} {$product->uom_stock}";
        }
        return null;
    }

    /**
     * Format number by removing trailing zeros
     */
    private static function formatNumber(float $number): string
    {
        return rtrim(rtrim(number_format($number, 4, '.', ''), '0'), '.');
    }
}
