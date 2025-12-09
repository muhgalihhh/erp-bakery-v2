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
                    ->label('Production Date')
                    ->required()
                    ->default(now())
                    ->native(false),

                TextInput::make('mo_number')
                    ->label('MO Number')
                    ->disabled()
                    ->dehydrated(false)
                    ->placeholder('Auto-generated')
                    ->columnSpan(2),

                // Product & BOM Selection
                Select::make('product_id')
                    ->label('Product to Manufacture')
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
                    ->helperText('Only finished goods with active BOM are shown')
                    ->columnSpan(2),

                Select::make('bom_header_id')
                    ->label('BOM / Recipe')
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
                                $bom->id => "{$bom->bom_code} (v{$bom->version}) - Yield: " . number_format((float) $bom->quantity_produced, 0)
                            ]);
                    }),

                // Quantity Planning
                TextInput::make('quantity_to_produce')
                    ->label('Quantity to Produce')
                    ->required()
                    ->numeric()
                    ->minValue(1)
                    ->suffix('units')
                    ->live()
                    ->helperText('Target production quantity'),

                DatePicker::make('planned_start_date')
                    ->label('Planned Start')
                    ->native(false),

                DatePicker::make('planned_finish_date')
                    ->label('Planned Finish')
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
                    ->label('Actual Qty Produced')
                    ->numeric()
                    ->default(0)
                    ->suffix('units')
                    ->disabled(fn($record) => !$record || $record->status === 'draft'),

                TextInput::make('quantity_scrapped')
                    ->label('Qty Scrapped/Reject')
                    ->numeric()
                    ->default(0)
                    ->suffix('units')
                    ->disabled(fn($record) => !$record || $record->status === 'draft'),

                // Costing (visible after production)
                TextInput::make('labor_cost')
                    ->label('Labor Cost')
                    ->numeric()
                    ->default(0)
                    ->prefix('Rp')
                    ->disabled(fn($record) => !$record || $record->status !== 'in_progress'),

                TextInput::make('overhead_cost')
                    ->label('Overhead Cost')
                    ->numeric()
                    ->default(0)
                    ->prefix('Rp')
                    ->disabled(fn($record) => !$record || $record->status !== 'in_progress')
                    ->helperText('Listrik, gas, depresiasi mesin'),

                // Notes
                Textarea::make('notes')
                    ->label('Production Notes')
                    ->rows(3)
                    ->columnSpanFull(),

                Textarea::make('completion_notes')
                    ->label('Completion Notes')
                    ->rows(3)
                    ->columnSpanFull()
                    ->visible(fn($record) => $record && $record->status === 'in_progress'),

                // Summary (Read-only, visible after confirm)
                Placeholder::make('material_cost_display')
                    ->label('Material Cost')
                    ->content(fn($record) => $record ? 'Rp ' . number_format($record->material_cost, 2) : '-')
                    ->visible(fn($record) => $record && $record->status !== 'draft'),

                Placeholder::make('total_cost_display')
                    ->label('Total HPP')
                    ->content(fn($record) => $record ? 'Rp ' . number_format($record->material_cost + $record->labor_cost + $record->overhead_cost, 2) : '-')
                    ->visible(fn($record) => $record && $record->status !== 'draft'),

                Placeholder::make('cost_per_unit_display')
                    ->label('HPP per Unit')
                    ->content(fn($record) => $record && $record->cost_per_unit ? 'Rp ' . number_format($record->cost_per_unit, 2) : '-')
                    ->visible(fn($record) => $record && $record->status === 'completed'),
            ]);
    }
}

