<?php

namespace App\Filament\Resources\GoodReceipts\Schemas;

use App\Models\GoodReceipt;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class GoodReceiptInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('purchaseOrder.id')
                    ->label('Purchase order'),
                TextEntry::make('received_by')
                    ->numeric(),
                TextEntry::make('receipt_number'),
                TextEntry::make('receipt_date')
                    ->date(),
                TextEntry::make('status'),
                TextEntry::make('delivery_note_number')
                    ->label('No. Surat Jalan (dari Vendor)')
                    ->placeholder('Tidak ada surat jalan')
                    ->icon('heroicon-o-document-text')
                    ->iconColor('info')
                    ->copyable()
                    ->columnSpan(1),
                TextEntry::make('notes')
                    ->label('Catatan')
                    ->placeholder('Tidak ada catatan')
                    ->columnSpanFull(),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('deleted_at')
                    ->dateTime()
                    ->visible(fn(GoodReceipt $record): bool => $record->trashed()),
            ]);
    }
}
