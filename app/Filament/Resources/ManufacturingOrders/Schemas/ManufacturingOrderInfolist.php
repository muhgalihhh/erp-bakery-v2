<?php

namespace App\Filament\Resources\ManufacturingOrders\Schemas;

use App\Models\ManufacturingOrder;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class ManufacturingOrderInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('mo_number'),
                TextEntry::make('production_date')
                    ->date(),
                TextEntry::make('planned_start_date')
                    ->date()
                    ->placeholder('-'),
                TextEntry::make('planned_finish_date')
                    ->date()
                    ->placeholder('-'),
                TextEntry::make('actual_start_time')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('actual_finish_time')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('product.name')
                    ->label('Product'),
                TextEntry::make('bomHeader.id')
                    ->label('Bom header'),
                TextEntry::make('quantity_to_produce')
                    ->numeric(),
                TextEntry::make('quantity_produced')
                    ->numeric(),
                TextEntry::make('quantity_scrapped')
                    ->numeric(),
                TextEntry::make('status'),
                TextEntry::make('material_cost')
                    ->money(),
                TextEntry::make('labor_cost')
                    ->money(),
                TextEntry::make('overhead_cost')
                    ->money(),
                TextEntry::make('total_cost')
                    ->money(),
                TextEntry::make('cost_per_unit')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('work_center')
                    ->placeholder('-'),
                TextEntry::make('supervisor.name')
                    ->label('Supervisor')
                    ->placeholder('-'),
                TextEntry::make('notes')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('completion_notes')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('created_by')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('confirmed_by')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('completed_by')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('confirmed_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('completed_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('deleted_at')
                    ->dateTime()
                    ->visible(fn (ManufacturingOrder $record): bool => $record->trashed()),
            ]);
    }
}
