<?php

namespace App\Filament\Resources\BomHeaders\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Repeater;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Schema;

class BomHeaderForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('Resep Produksi (BOM)')
                    ->tabs([
                        // Tab 1: Basic Info
                        Tabs\Tab::make('Informasi Resep')
                            ->schema([
                                Fieldset::make('Produk & Versi')
                                    ->schema([
                                        Select::make('product_id')
                                            ->label('Produk Hasil')
                                            ->relationship(
                                                'product',
                                                'name',
                                                fn($query) =>
                                                $query->whereIn('type', ['finished', 'wip'])
                                            )
                                            ->searchable()
                                            ->preload()
                                            ->required()
                                            ->helperText('Produk yang DIHASILKAN dari resep ini (Roti Tawar, Croissant, dll)'),

                                        TextInput::make('bom_code')
                                            ->label('Kode Resep')
                                            ->required()
                                            ->unique(ignoreRecord: true)
                                            ->placeholder('BOM-2025-001')
                                            ->helperText('Auto-generated, bisa edit manual'),

                                        TextInput::make('version')
                                            ->label('Versi')
                                            ->required()
                                            ->default('1.0')
                                            ->placeholder('1.0, 1.1, 2.0')
                                            ->helperText('Versi resep (untuk tracking perubahan)'),
                                    ])
                                    ->columns(3),

                                Fieldset::make('Detail Produksi')
                                    ->schema([
                                        TextInput::make('quantity_produced')
                                            ->label('Jumlah Hasil')
                                            ->required()
                                            ->numeric()
                                            ->default(1)
                                            ->suffix('unit')
                                            ->helperText('Berapa banyak output yang dihasilkan (misal: 10 roti)'),

                                        TextInput::make('production_time_minutes')
                                            ->label('Waktu Produksi')
                                            ->numeric()
                                            ->suffix('menit')
                                            ->placeholder('60')
                                            ->helperText('Lama waktu produksi (opsional)'),
                                    ])
                                    ->columns(2),

                                Fieldset::make('Deskripsi & Instruksi')
                                    ->schema([
                                        Textarea::make('description')
                                            ->label('Deskripsi')
                                            ->rows(2)
                                            ->placeholder('Deskripsi resep singkat...'),

                                        Textarea::make('instructions')
                                            ->label('Instruksi Pembuatan')
                                            ->rows(4)
                                            ->placeholder('Langkah-langkah pembuatan:\n1. Campur tepung dan gula\n2. Tambahkan telur...')
                                            ->helperText('Instruksi detail untuk operator produksi'),
                                    ]),

                                Fieldset::make('Status')
                                    ->schema([
                                        Toggle::make('is_active')
                                            ->label('Aktif')
                                            ->default(true)
                                            ->helperText('Resep masih digunakan?'),

                                        Toggle::make('is_default')
                                            ->label('Resep Utama')
                                            ->default(false)
                                            ->helperText('Centang jika ini resep utama untuk produk ini'),
                                    ])
                                    ->columns(2),
                            ]),

                        // Tab 2: Ingredients (Repeater!)
                        Tabs\Tab::make('Bahan-Bahan')
                            ->schema([
                                Repeater::make('items')
                                    ->relationship('items')
                                    ->schema([
                                        // Row 1: Ingredient Selection - Full Width
                                        Select::make('product_id')
                                            ->label('Bahan Baku')
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
                                            ->helperText('Pilih bahan baku dari daftar produk yang tersedia')
                                            ->columnSpanFull(),

                                        // Row 2: Quantity & Waste - 3 columns
                                        TextInput::make('quantity')
                                            ->label('Jumlah 📏')
                                            ->required()
                                            ->numeric()
                                            ->default(1)
                                            ->step(0.0001)
                                            ->helperText('Jumlah dalam satuan pemakaian')
                                            ->columnSpan(1),

                                        TextInput::make('waste_percentage')
                                            ->label('Waste % 🗑️')
                                            ->numeric()
                                            ->default(0)
                                            ->suffix('%')
                                            ->step(0.01)
                                            ->minValue(0)
                                            ->maxValue(100)
                                            ->helperText('% waste/susut (opsional)')
                                            ->columnSpan(1),

                                        TextInput::make('sequence')
                                            ->label('Urutan 🔢')
                                            ->numeric()
                                            ->default(1)
                                            ->helperText('Urutan langkah resep')
                                            ->columnSpan(1),

                                        // Row 3: Notes - Full Width
                                        Textarea::make('notes')
                                            ->label('Catatan Khusus')
                                            ->rows(2)
                                            ->placeholder('Contoh: "Kocok telur terlebih dahulu", "Ayak tepung agar tidak menggumpal"')
                                            ->columnSpanFull(),
                                    ])
                                    ->columns(3)
                                    ->defaultItems(0)
                                    ->addActionLabel('➕ Tambah Bahan')
                                    ->reorderable()
                                    ->reorderableWithButtons()
                                    ->collapsible()
                                    ->cloneable()
                                    ->deleteAction(
                                        fn($action) => $action
                                            ->requiresConfirmation()
                                            ->modalHeading('Hapus Bahan?')
                                            ->modalDescription('Apakah Anda yakin ingin menghapus bahan ini dari resep?')
                                    )
                                    ->itemLabel(
                                        fn(array $state): ?string =>
                                        $state['product_id']
                                        ? '📦 Urutan ' . ($state['sequence'] ?? 1) . ': ' .
                                        \App\Models\Product::find($state['product_id'])?->name .
                                        ' • Jml: ' . ($state['quantity'] ?? 0) . ' ' .
                                        (\App\Models\Product::find($state['product_id'])?->uom_usage ?? 'unit') .
                                        (($state['waste_percentage'] ?? 0) > 0 ? ' • Waste: ' . $state['waste_percentage'] . '%' : '')
                                        : '🆕 Bahan Baru'
                                    )
                                    ->helperText('💡 Tips: Klik item untuk expand/collapse • Gunakan ↑↓ untuk urutan • 📋 untuk duplikat bahan')
                                    ->columnSpanFull()
                                    ->live(),
                            ]),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}

