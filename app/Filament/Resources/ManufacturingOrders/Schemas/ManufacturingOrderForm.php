<?php

namespace App\Filament\Resources\ManufacturingOrders\Schemas;

use App\Models\BomHeader;
use App\Models\Product;
use App\Models\User;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class ManufacturingOrderForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(3)
            ->components([
                // Production Information
                DatePicker::make('production_date')
                    ->label('Tanggal Produksi')
                    ->required()
                    ->default(now())
                    ->native(false),

                TextInput::make('mo_number')
                    ->label('No. Order Produksi')
                    ->disabled()
                    ->dehydrated(false)
                    ->placeholder('Otomatis')
                    ->columnSpan(2),

                // Product & BOM Selection
                Select::make('product_id')
                    ->label('Produk yang Akan Dibuat')
                    ->required()
                    ->searchable()
                    ->preload()
                    ->relationship(
                        'product',
                        'name',
                        fn($query) => $query
                            ->where('type', 'finished') // Only finished goods
                            ->whereHas('boms', function ($q) {
                                $q->where('is_active', true); // Must have active BOM
                            })
                            ->where('is_active', true)
                    )
                    ->getOptionLabelFromRecordUsing(fn($record) => "{$record->name} ({$record->sku})")
                    ->live()
                    ->afterStateUpdated(function ($state, callable $set) {
                        if ($state) {
                            // Auto-select default BOM for this product
                            $defaultBom = BomHeader::where('product_id', $state)
                                ->where('is_default', true)
                                ->where('is_active', true)
                                ->first();

                            if ($defaultBom) {
                                $set('bom_header_id', $defaultBom->id);
                            }
                        }
                    })
                    ->helperText('Hanya produk jadi yang memiliki resep aktif')
                    ->columnSpan(2),

                Select::make('bom_header_id')
                    ->label('Resep / BOM')
                    ->required()
                    ->searchable()
                    ->options(function (callable $get) {
                        $productId = $get('product_id');
                        if (!$productId) {
                            return [];
                        }

                        return BomHeader::where('product_id', $productId)
                            ->where('is_active', true)
                            ->get()
                            ->mapWithKeys(fn($bom) => [
                                $bom->id => "{$bom->bom_code} (v{$bom->version}) - Hasil: " . number_format((float) $bom->quantity_produced, 0)
                            ]);
                    }),

                // Quantity Planning
                TextInput::make('quantity_to_produce')
                    ->label('Jumlah Target Produksi')
                    ->required()
                    ->numeric()
                    ->minValue(1)
                    ->suffix('unit')
                    ->live()
                    ->helperText('Target jumlah produksi'),

                DatePicker::make('planned_start_date')
                    ->label('Rencana Mulai')
                    ->native(false),

                DatePicker::make('planned_finish_date')
                    ->label('Rencana Selesai')
                    ->native(false),

                // Supervisor
                Select::make('supervisor_id')
                    ->label('Supervisor / PIC')
                    ->searchable()
                    ->preload()
                    ->relationship('supervisor', 'name')
                    ->columnSpan(2),

                // Production Results (visible after start)
                TextInput::make('quantity_produced')
                    ->label('Jumlah Aktual Diproduksi')
                    ->numeric()
                    ->default(0)
                    ->suffix('unit')
                    ->disabled(fn($record) => !$record || $record->status === 'draft'),

                TextInput::make('quantity_scrapped')
                    ->label('Jumlah Reject/Gagal')
                    ->numeric()
                    ->default(0)
                    ->suffix('unit')
                    ->helperText('Unit yang cacat atau ditolak')
                    ->disabled(fn($record) => !$record || $record->status === 'draft'),

                // Costing (visible after production)
                TextInput::make('labor_cost')
                    ->label('Biaya Tenaga Kerja')
                    ->numeric()
                    ->default(0)
                    ->prefix('Rp')
                    ->helperText('Total biaya tenaga kerja untuk produksi ini')
                    ->disabled(fn($record) => !$record || $record->status !== 'in_progress'),

                TextInput::make('overhead_cost')
                    ->label('Biaya Overhead')
                    ->numeric()
                    ->default(0)
                    ->prefix('Rp')
                    ->disabled(fn($record) => !$record || $record->status !== 'in_progress')
                    ->helperText('Listrik, gas, depresiasi mesin'),

                // Notes
                Textarea::make('notes')
                    ->label('Catatan Produksi')
                    ->rows(3)
                    ->placeholder('Catatan atau instruksi khusus untuk produksi ini...')
                    ->columnSpanFull(),

                Textarea::make('completion_notes')
                    ->label('Catatan Penyelesaian')
                    ->rows(3)
                    ->placeholder('Hasil, kendala, atau catatan saat produksi selesai...')
                    ->columnSpanFull()
                    ->visible(fn($record) => $record && $record->status === 'in_progress'),

                // Summary (Read-only, visible after confirm)
                Placeholder::make('material_cost_display')
                    ->label('Biaya Bahan Baku')
                    ->content(fn($record) => $record ? 'Rp ' . number_format((float) $record->material_cost, 0) : '-')
                    ->visible(fn($record) => $record && $record->status !== 'draft'),

                Placeholder::make('total_cost_display')
                    ->label('Total HPP')
                    ->content(fn($record) => $record ? 'Rp ' . number_format((float) ($record->material_cost + $record->labor_cost + $record->overhead_cost), 0) : '-')
                    ->visible(fn($record) => $record && $record->status !== 'draft'),

                Placeholder::make('cost_per_unit_display')
                    ->label('HPP per Unit')
                    ->content(fn($record) => $record && $record->cost_per_unit ? 'Rp ' . number_format((float) $record->cost_per_unit, 0) : '-')
                    ->visible(fn($record) => $record && $record->status === 'completed'),
            ]);
    }
}

