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
                Tabs::make('Bill of Materials')
                    ->tabs([
                        // Tab 1: Basic Info
                        Tabs\Tab::make('Recipe Information')
                            ->schema([
                                Fieldset::make('Product & Version')
                                    ->schema([
                                        Select::make('product_id')
                                            ->label('Output Product (Hasil Produksi)')
                                            ->relationship(
                                                'product',
                                                'name',
                                                fn($query) =>
                                                $query->whereIn('type', ['finished', 'wip'])
                                            )
                                            ->searchable()
                                            ->preload()
                                            ->required()
                                            ->helperText('Produk HASIL dari resep ini (Roti Tawar, Croissant, dll)'),

                                        TextInput::make('bom_code')
                                            ->label('BOM Code')
                                            ->required()
                                            ->unique(ignoreRecord: true)
                                            ->placeholder('BOM-2025-001')
                                            ->helperText('Auto-generated, bisa edit manual'),

                                        TextInput::make('version')
                                            ->required()
                                            ->default('1.0')
                                            ->placeholder('1.0, 1.1, 2.0')
                                            ->helperText('Version resep (untuk tracking perubahan)'),
                                    ])
                                    ->columns(3),

                                Fieldset::make('Production Details')
                                    ->schema([
                                        TextInput::make('quantity_produced')
                                            ->label('Quantity Produced')
                                            ->required()
                                            ->numeric()
                                            ->default(1)
                                            ->suffix('units')
                                            ->helperText('Berapa banyak output yang dihasilkan (misal: 10 roti)'),

                                        TextInput::make('production_time_minutes')
                                            ->label('Production Time')
                                            ->numeric()
                                            ->suffix('minutes')
                                            ->placeholder('60')
                                            ->helperText('Lama waktu produksi (opsional)'),
                                    ])
                                    ->columns(2),

                                Fieldset::make('Description & Instructions')
                                    ->schema([
                                        Textarea::make('description')
                                            ->rows(2)
                                            ->placeholder('Deskripsi resep singkat...'),

                                        Textarea::make('instructions')
                                            ->rows(4)
                                            ->placeholder('Langkah-langkah pembuatan:\n1. Campur tepung dan gula\n2. Tambahkan telur...')
                                            ->helperText('Instruksi detail untuk Head Baker'),
                                    ]),

                                Fieldset::make('Status')
                                    ->schema([
                                        Toggle::make('is_active')
                                            ->label('Active')
                                            ->default(true)
                                            ->helperText('Resep masih digunakan?'),

                                        Toggle::make('is_default')
                                            ->label('Default Recipe')
                                            ->default(false)
                                            ->helperText('Centang jika ini resep utama untuk produk ini'),
                                    ])
                                    ->columns(2),
                            ]),

                        // Tab 2: Ingredients (Repeater!)
                        Tabs\Tab::make('Ingredients (Bahan Baku)')
                            ->schema([
                                Repeater::make('items')
                                    ->relationship('items')
                                    ->schema([
                                        // Row 1: Ingredient Selection - Full Width
                                        Select::make('product_id')
                                            ->label('Ingredient (Bahan Baku)')
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
                                            ->label('Quantity (Jumlah) 📏')
                                            ->required()
                                            ->numeric()
                                            ->default(1)
                                            ->step(0.0001)
                                            ->helperText('Jumlah dalam satuan UoM Usage')
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
                                            ->label('Step Number 🔢')
                                            ->numeric()
                                            ->default(1)
                                            ->helperText('Urutan langkah resep')
                                            ->columnSpan(1),

                                        // Row 3: Notes - Full Width
                                        Textarea::make('notes')
                                            ->label('Notes (Catatan Khusus)')
                                            ->rows(2)
                                            ->placeholder('Contoh: "Kocok telur terlebih dahulu", "Ayak tepung agar tidak menggumpal"')
                                            ->columnSpanFull(),
                                    ])
                                    ->columns(3)
                                    ->defaultItems(0)
                                    ->addActionLabel('➕ Tambah Bahan Baku')
                                    ->reorderable()
                                    ->reorderableWithButtons()
                                    ->collapsible()
                                    ->cloneable()
                                    ->deleteAction(
                                        fn($action) => $action
                                            ->requiresConfirmation()
                                            ->modalHeading('Hapus Bahan Baku?')
                                            ->modalDescription('Apakah Anda yakin ingin menghapus bahan baku ini dari resep?')
                                    )
                                    ->itemLabel(
                                        fn(array $state): ?string =>
                                        $state['product_id']
                                        ? '📦 Step ' . ($state['sequence'] ?? 1) . ': ' .
                                        \App\Models\Product::find($state['product_id'])?->name .
                                        ' • Qty: ' . ($state['quantity'] ?? 0) . ' ' .
                                        (\App\Models\Product::find($state['product_id'])?->uom_usage ?? 'unit') .
                                        (($state['waste_percentage'] ?? 0) > 0 ? ' • Waste: ' . $state['waste_percentage'] . '%' : '')
                                        : '🆕 Bahan Baku Baru'
                                    )
                                    ->helperText('💡 Tips: Klik item untuk expand/collapse • Gunakan ↑↓ untuk urutan • 📋 untuk duplicate bahan')
                                    ->columnSpanFull()
                                    ->live(),
                            ]),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}

