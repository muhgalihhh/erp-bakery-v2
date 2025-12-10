<?php

namespace App\Filament\Resources\Products\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Schema;

class ProductForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('Informasi Produk')
                    ->tabs([
                        // Tab 1: Basic Info
                        Tabs\Tab::make('Informasi Dasar')
                            ->schema([
                                Fieldset::make('Identitas Produk')
                                    ->schema([
                                        TextInput::make('sku')
                                            ->label('SKU')
                                            ->required()
                                            ->unique(ignoreRecord: true)
                                            ->maxLength(255)
                                            ->placeholder('TP-001, RT-TAWAR-001')
                                            ->helperText('Kode unik produk')
                                            ->columnSpan(1),

                                        TextInput::make('name')
                                            ->label('Nama Produk')
                                            ->required()
                                            ->maxLength(255)
                                            ->placeholder('Tepung Segitiga Biru 1Kg')
                                            ->columnSpan(2),

                                        Select::make('type')
                                            ->label('Tipe Produk')
                                            ->required()
                                            ->options([
                                                'raw' => 'Raw Material (Bahan Baku)',
                                                'wip' => 'Work In Progress (Barang Dalam Proses)',
                                                'finished' => 'Finished Goods (Barang Jadi)',
                                                'service' => 'Service (Jasa)',
                                                'consumable' => 'Consumable (Habis Pakai)',
                                            ])
                                            ->native(false)
                                            ->searchable()
                                            ->helperText('Raw=Tepung, WIP=Adonan, Finished=Roti, Consumable=Gas/Listrik')
                                            ->columnSpan(2),

                                        TextInput::make('barcode')
                                            ->label('Barcode')
                                            ->maxLength(255)
                                            ->placeholder('8991234567890')
                                            ->helperText('Kode barcode produk (opsional)')
                                            ->columnSpan(1),
                                    ])
                                    ->columns(3),

                                Fieldset::make('Deskripsi & Gambar')
                                    ->schema([
                                        Textarea::make('description')
                                            ->label('Deskripsi')
                                            ->rows(3)
                                            ->placeholder('Deskripsi detail produk...')
                                            ->columnSpan(2),

                                        FileUpload::make('image_url')
                                            ->label('Gambar Produk')
                                            ->image()
                                            ->imageEditor()
                                            ->maxSize(2048)
                                            ->helperText('Maks 2MB')
                                            ->columnSpan(1),
                                    ])
                                    ->columns(3),

                                Fieldset::make('Status Produk')
                                    ->schema([
                                        Toggle::make('is_purchasable')
                                            ->label('Bisa Dibeli?')
                                            ->default(false)
                                            ->helperText('Centang jika produk ini DIBELI dari supplier')
                                            ->inline(false),

                                        Toggle::make('is_sellable')
                                            ->label('Bisa Dijual?')
                                            ->default(false)
                                            ->helperText('Centang jika produk ini DIJUAL ke customer')
                                            ->inline(false),

                                        Toggle::make('is_active')
                                            ->label('Aktif')
                                            ->default(true)
                                            ->helperText('Produk masih aktif digunakan?')
                                            ->inline(false),
                                    ])
                                    ->columns(3),
                            ]),

                        // Tab 2: Unit of Measure
                        Tabs\Tab::make('Satuan (UoM)')
                            ->schema([
                                Fieldset::make('Satuan Produk')
                                    ->schema([
                                        TextInput::make('uom_purchase')
                                            ->label('Satuan Pembelian')
                                            ->required()
                                            ->default('pcs')
                                            ->placeholder('Sak, Karton, Kg')
                                            ->helperText('Satuan saat BELI dari supplier'),

                                        TextInput::make('uom_stock')
                                            ->label('Satuan Stok')
                                            ->required()
                                            ->default('pcs')
                                            ->placeholder('Kg, Liter, Pcs')
                                            ->helperText('Satuan PENYIMPANAN di gudang'),

                                        TextInput::make('uom_usage')
                                            ->label('Satuan Pemakaian')
                                            ->required()
                                            ->default('pcs')
                                            ->placeholder('Gram, ml, Pcs')
                                            ->helperText('Satuan PAKAI di resep'),
                                    ])
                                    ->columns(3),

                                Fieldset::make('Konversi Satuan')
                                    ->schema([
                                        TextInput::make('conversion_purchase_to_stock')
                                            ->label('Pembelian → Stok')
                                            ->required()
                                            ->numeric()
                                            ->default(1)
                                            ->step(0.0001)
                                            ->placeholder('1')
                                            ->helperText('Contoh: 1 Sak = 25 Kg → isi 25'),

                                        TextInput::make('conversion_stock_to_usage')
                                            ->label('Stok → Pemakaian')
                                            ->required()
                                            ->numeric()
                                            ->default(1)
                                            ->step(0.0001)
                                            ->placeholder('1')
                                            ->helperText('Contoh: 1 Kg = 1000 Gram → isi 1000'),
                                    ])
                                    ->columns(2),
                            ]),

                        // Tab 3: Pricing & Cost
                        Tabs\Tab::make('Harga & Stok')
                            ->schema([
                                Fieldset::make('Harga')
                                    ->schema([
                                        TextInput::make('purchase_price')
                                            ->label('Harga Beli')
                                            ->required()
                                            ->numeric()
                                            ->default(0)
                                            ->prefix('Rp')
                                            ->helperText('Harga beli dari supplier (per Satuan Pembelian)'),

                                        TextInput::make('selling_price')
                                            ->label('Harga Jual')
                                            ->required()
                                            ->numeric()
                                            ->default(0)
                                            ->prefix('Rp')
                                            ->helperText('Harga jual ke customer'),

                                        TextInput::make('standard_cost')
                                            ->label('HPP Standard')
                                            ->required()
                                            ->numeric()
                                            ->default(0)
                                            ->prefix('Rp')
                                            ->helperText('Auto-calculated dari BOM (bisa edit manual)'),
                                    ])
                                    ->columns(3),

                                Fieldset::make('Kontrol Stok')
                                    ->schema([
                                        TextInput::make('current_stock')
                                            ->label('Stok Saat Ini')
                                            ->required()
                                            ->numeric()
                                            ->default(0)
                                            ->suffix('unit')
                                            ->helperText('Stok saat ini (dalam Satuan Stok)'),

                                        TextInput::make('minimum_stock')
                                            ->label('Stok Minimum')
                                            ->required()
                                            ->numeric()
                                            ->default(0)
                                            ->suffix('unit')
                                            ->helperText('Jika stok <= ini, perlu reorder dari supplier'),

                                        TextInput::make('maximum_stock')
                                            ->label('Stok Maksimum')
                                            ->required()
                                            ->numeric()
                                            ->default(0)
                                            ->suffix('unit')
                                            ->helperText('Batas atas stok gudang'),
                                    ])
                                    ->columns(3),
                            ]),

                        // Tab 4: Accounting Integration
                        Tabs\Tab::make('Akun Akuntansi')
                            ->schema([
                                Fieldset::make('Keterkaitan Akun')
                                    ->schema([
                                        Select::make('income_account_id')
                                            ->label('Akun Pendapatan')
                                            ->relationship('incomeAccount', 'name', fn($query) => $query->where('type', 'revenue'))
                                            ->searchable()
                                            ->preload()
                                            ->helperText('Akun untuk pendapatan penjualan (misal: 4-1000 Pendapatan Penjualan)'),

                                        Select::make('expense_account_id')
                                            ->label('Akun HPP')
                                            ->relationship('expenseAccount', 'name', fn($query) => $query->where('type', 'expense'))
                                            ->searchable()
                                            ->preload()
                                            ->helperText('Akun untuk Harga Pokok Penjualan (misal: 5-1000 HPP)'),

                                        Select::make('inventory_account_id')
                                            ->label('Akun Persediaan')
                                            ->relationship('inventoryAccount', 'name', fn($query) => $query->where('type', 'asset'))
                                            ->searchable()
                                            ->preload()
                                            ->helperText('Akun aset persediaan (1-1400 Bahan Baku atau 1-1500 Barang Jadi)'),
                                    ]),
                            ]),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}

