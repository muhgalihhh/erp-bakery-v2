<?php

namespace App\Filament\Resources\StockAdjustments\StockAdjustments\Schemas;

use App\Models\Product;
use App\Models\StockAdjustment;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class StockAdjustmentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(3)
            ->components([
                Section::make('Informasi Penyesuaian Stok')
                    ->schema([
                        TextInput::make('adjustment_number')
                            ->label('No. Adjustment')
                            ->disabled()
                            ->dehydrated(false)
                            ->placeholder('Otomatis setelah disimpan')
                            ->columnSpan(1),

                        DatePicker::make('adjustment_date')
                            ->label('Tanggal Adjustment')
                            ->required()
                            ->default(now())
                            ->native(false)
                            ->columnSpan(1),

                        Select::make('status')
                            ->label('Status')
                            ->options([
                                StockAdjustment::STATUS_DRAFT => 'Draft',
                                StockAdjustment::STATUS_APPROVED => 'Disetujui',
                                StockAdjustment::STATUS_CANCELLED => 'Dibatalkan',
                            ])
                            ->default(StockAdjustment::STATUS_DRAFT)
                            ->disabled()
                            ->dehydrated(false)
                            ->columnSpan(1),
                    ])
                    ->columns(3)
                    ->columnSpanFull(),

                Section::make('Detail Produk')
                    ->schema([
                        Select::make('product_id')
                            ->label('Produk')
                            ->required()
                            ->searchable()
                            ->preload()
                            ->relationship('product', 'name')
                            ->getOptionLabelFromRecordUsing(fn(Product $record) => "{$record->name} ({$record->sku})")
                            ->reactive()
                            ->afterStateUpdated(function (callable $set, ?string $state) {
                                if ($state) {
                                    $product = Product::find($state);
                                    if ($product) {
                                        // Auto-fill system quantity dari current stock
                                        $set('system_quantity', $product->current_stock);
                                        $set('uom', $product->uom_stock);
                                    }
                                }
                            })
                            ->columnSpan(2),

                        TextInput::make('uom')
                            ->label('Satuan')
                            ->disabled()
                            ->dehydrated(true)
                            ->columnSpan(1),
                    ])
                    ->columns(3)
                    ->columnSpanFull(),

                Section::make('Kuantitas')
                    ->schema([
                        TextInput::make('system_quantity')
                            ->label('Stok di Sistem')
                            ->numeric()
                            ->disabled()
                            ->dehydrated(true)
                            ->suffix(fn(callable $get) => $get('uom') ?? '')
                            ->helperText('Stok saat ini berdasarkan sistem')
                            ->columnSpan(1),

                        TextInput::make('actual_quantity')
                            ->label('Stok Aktual/Fisik 📦')
                            ->numeric()
                            ->required()
                            ->suffix(fn(callable $get) => $get('uom') ?? '')
                            ->helperText('Masukkan jumlah hasil cek fisik')
                            ->reactive()
                            ->afterStateUpdated(function (callable $set, callable $get, ?string $state) {
                                $system = (float) ($get('system_quantity') ?? 0);
                                $actual = (float) ($state ?? 0);
                                $difference = $actual - $system;
                                $set('difference_quantity', $difference);
                            })
                            ->columnSpan(1),

                        Placeholder::make('difference_display')
                            ->label('Selisih')
                            ->content(function (callable $get) {
                                $diff = (float) ($get('difference_quantity') ?? 0);
                                $uom = $get('uom') ?? '';

                                if ($diff == 0) {
                                    return "0 {$uom} (Sama)";
                                } elseif ($diff > 0) {
                                    return "+{$diff} {$uom} (Lebih/Ketemu)";
                                } else {
                                    return "{$diff} {$uom} (Kurang/Hilang)";
                                }
                            })
                            ->columnSpan(1),

                        TextInput::make('difference_quantity')
                            ->label('Selisih (Hidden)')
                            ->numeric()
                            ->hidden()
                            ->dehydrated(true),
                    ])
                    ->columns(3)
                    ->columnSpanFull(),

                Section::make('Alasan & Catatan')
                    ->schema([
                        Select::make('type')
                            ->label('Tipe Adjustment')
                            ->required()
                            ->options([
                                StockAdjustment::TYPE_STOCK_OPNAME => 'Stock Opname (Cek Fisik)',
                                StockAdjustment::TYPE_DAMAGED => 'Barang Rusak',
                                StockAdjustment::TYPE_EXPIRED => 'Kadaluarsa',
                                StockAdjustment::TYPE_OTHER => 'Lainnya',
                            ])
                            ->default(StockAdjustment::TYPE_STOCK_OPNAME)
                            ->columnSpan(1),

                        Select::make('reason')
                            ->label('Alasan')
                            ->required()
                            ->options([
                                StockAdjustment::REASON_STOCK_COUNT => 'Stock Opname/Cek Fisik',
                                StockAdjustment::REASON_DAMAGED => 'Rusak',
                                StockAdjustment::REASON_EXPIRED => 'Kadaluarsa',
                                StockAdjustment::REASON_LOST => 'Hilang',
                                StockAdjustment::REASON_FOUND => 'Ketemu',
                                StockAdjustment::REASON_OTHER => 'Lainnya',
                            ])
                            ->default(StockAdjustment::REASON_STOCK_COUNT)
                            ->columnSpan(1),

                        Textarea::make('notes')
                            ->label('Catatan')
                            ->placeholder('Catatan detail tentang adjustment ini...')
                            ->rows(3)
                            ->columnSpan(1),
                    ])
                    ->columns(3)
                    ->columnSpanFull(),
            ]);
    }
}
