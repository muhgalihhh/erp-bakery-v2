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
                Tabs::make('Product Information')
                    ->tabs([
                        // Tab 1: Basic Info
                        Tabs\Tab::make('Basic Information')
                            ->schema([
                                Fieldset::make('Product Identity')
                                    ->schema([
                                        TextInput::make('sku')
                                            ->label('SKU')
                                            ->required()
                                            ->unique(ignoreRecord: true)
                                            ->maxLength(255)
                                            ->placeholder('TP-001, RT-TAWAR-001')
                                            ->columnSpan(1),

                                        TextInput::make('name')
                                            ->label('Product Name')
                                            ->required()
                                            ->maxLength(255)
                                            ->placeholder('Tepung Segitiga Biru 1Kg')
                                            ->columnSpan(2),

                                        Select::make('type')
                                            ->label('Product Type')
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
                                            ->helperText('Raw=Tepung, WIP=Adonan, Finished=Roti')
                                            ->columnSpan(2),

                                        TextInput::make('barcode')
                                            ->label('Barcode')
                                            ->maxLength(255)
                                            ->placeholder('8991234567890')
                                            ->columnSpan(1),
                                    ])
                                    ->columns(3),

                                Fieldset::make('Description & Image')
                                    ->schema([
                                        Textarea::make('description')
                                            ->label('Description')
                                            ->rows(3)
                                            ->placeholder('Deskripsi detail produk...')
                                            ->columnSpan(2),

                                        FileUpload::make('image_url')
                                            ->label('Product Image')
                                            ->image()
                                            ->imageEditor()
                                            ->maxSize(2048)
                                            ->columnSpan(1),
                                    ])
                                    ->columns(3),

                                Fieldset::make('Product Flags')
                                    ->schema([
                                        Toggle::make('is_purchasable')
                                            ->label('Can be Purchased?')
                                            ->default(false)
                                            ->helperText('Centang jika produk ini DIBELI dari supplier')
                                            ->inline(false),

                                        Toggle::make('is_sellable')
                                            ->label('Can be Sold?')
                                            ->default(false)
                                            ->helperText('Centang jika produk ini DIJUAL ke customer')
                                            ->inline(false),

                                        Toggle::make('is_active')
                                            ->label('Active')
                                            ->default(true)
                                            ->helperText('Produk masih aktif digunakan?')
                                            ->inline(false),
                                    ])
                                    ->columns(3),
                            ]),

                        // Tab 2: Unit of Measure
                        Tabs\Tab::make('Unit of Measure (UoM)')
                            ->schema([
                                Fieldset::make('Units')
                                    ->schema([
                                        TextInput::make('uom_purchase')
                                            ->label('Purchase Unit')
                                            ->required()
                                            ->default('pcs')
                                            ->placeholder('Sak, Karton, Kg')
                                            ->helperText('Satuan saat BELI dari supplier'),

                                        TextInput::make('uom_stock')
                                            ->label('Stock Unit')
                                            ->required()
                                            ->default('pcs')
                                            ->placeholder('Kg, Liter, Pcs')
                                            ->helperText('Satuan PENYIMPANAN di gudang'),

                                        TextInput::make('uom_usage')
                                            ->label('Usage Unit (Recipe)')
                                            ->required()
                                            ->default('pcs')
                                            ->placeholder('Gram, ml, Pcs')
                                            ->helperText('Satuan PAKAI di resep'),
                                    ])
                                    ->columns(3),

                                Fieldset::make('Conversion Rates')
                                    ->schema([
                                        TextInput::make('conversion_purchase_to_stock')
                                            ->label('Purchase → Stock Conversion')
                                            ->required()
                                            ->numeric()
                                            ->default(1)
                                            ->step(0.0001)
                                            ->placeholder('1')
                                            ->helperText('Contoh: 1 Sak = 25 Kg → isi 25'),

                                        TextInput::make('conversion_stock_to_usage')
                                            ->label('Stock → Usage Conversion')
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
                        Tabs\Tab::make('Pricing & Inventory')
                            ->schema([
                                Fieldset::make('Pricing')
                                    ->schema([
                                        TextInput::make('purchase_price')
                                            ->label('Purchase Price')
                                            ->required()
                                            ->numeric()
                                            ->default(0)
                                            ->prefix('Rp')
                                            ->helperText('Harga beli dari supplier (per UoM Purchase)'),

                                        TextInput::make('selling_price')
                                            ->label('Selling Price')
                                            ->required()
                                            ->numeric()
                                            ->default(0)
                                            ->prefix('Rp')
                                            ->helperText('Harga jual ke customer'),

                                        TextInput::make('standard_cost')
                                            ->label('Standard Cost (HPP)')
                                            ->required()
                                            ->numeric()
                                            ->default(0)
                                            ->prefix('Rp')
                                            ->helperText('Auto-calculated dari BOM (bisa edit manual)'),
                                    ])
                                    ->columns(3),

                                Fieldset::make('Inventory Control')
                                    ->schema([
                                        TextInput::make('current_stock')
                                            ->label('Current Stock')
                                            ->required()
                                            ->numeric()
                                            ->default(0)
                                            ->suffix('units')
                                            ->helperText('Stok saat ini (dalam UoM Stock)'),

                                        TextInput::make('minimum_stock')
                                            ->label('Minimum Stock (Reorder Level)')
                                            ->required()
                                            ->numeric()
                                            ->default(0)
                                            ->suffix('units')
                                            ->helperText('Jika stok <= ini, perlu reorder'),

                                        TextInput::make('maximum_stock')
                                            ->label('Maximum Stock')
                                            ->required()
                                            ->numeric()
                                            ->default(0)
                                            ->suffix('units'),
                                    ])
                                    ->columns(3),
                            ]),

                        // Tab 4: Accounting Integration
                        Tabs\Tab::make('Accounting Integration')
                            ->schema([
                                Fieldset::make('Accounting Integration')
                                    ->schema([
                                        Select::make('income_account_id')
                                            ->label('Income Account (Pendapatan)')
                                            ->relationship('incomeAccount', 'name', fn($query) => $query->where('type', 'revenue'))
                                            ->searchable()
                                            ->preload()
                                            ->helperText('Akun untuk pendapatan penjualan (misal: 4-1100 Penjualan Roti)'),

                                        Select::make('expense_account_id')
                                            ->label('Expense Account (HPP)')
                                            ->relationship('expenseAccount', 'name', fn($query) => $query->where('type', 'expense'))
                                            ->searchable()
                                            ->preload()
                                            ->helperText('Akun untuk Harga Pokok Penjualan (misal: 5-1100 HPP Roti)'),

                                        Select::make('inventory_account_id')
                                            ->label('Inventory Account (Persediaan)')
                                            ->relationship('inventoryAccount', 'name', fn($query) => $query->where('type', 'asset'))
                                            ->searchable()
                                            ->preload()
                                            ->helperText('Akun aset persediaan (misal: 1-1310 Persediaan Bahan Baku)'),
                                    ]),
                            ]),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}

