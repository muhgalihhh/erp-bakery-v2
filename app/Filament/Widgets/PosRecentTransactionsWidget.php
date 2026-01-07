<?php

namespace App\Filament\Widgets;

use App\Models\SalesOrder;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class PosRecentTransactionsWidget extends BaseWidget
{
  protected static ?string $heading = 'Transaksi Terbaru';

  protected int|string|array $columnSpan = 1;

  public function table(Table $table): Table
  {
    return $table
      ->query(
        SalesOrder::query()
          ->with('customer')
          ->where('status', 'completed')
          ->latest('created_at')
          ->limit(10)
      )
      ->columns([
        Tables\Columns\TextColumn::make('order_number')
          ->label('No. Order')
          ->searchable()
          ->weight('bold')
          ->color('primary'),

        Tables\Columns\TextColumn::make('created_at')
          ->label('Waktu')
          ->dateTime('d M Y, H:i')
          ->sortable()
          ->size('sm')
          ->color('gray'),

        Tables\Columns\TextColumn::make('customer.name')
          ->label('Pelanggan')
          ->default('Walk-in Customer')
          ->icon('heroicon-m-user')
          ->searchable(),

        Tables\Columns\TextColumn::make('payment_method')
          ->label('Pembayaran')
          ->badge()
          ->formatStateUsing(fn(string $state): string => match ($state) {
            'cash' => 'Tunai',
            'qris' => 'QRIS',
            'transfer' => 'Transfer',
            'debit' => 'Debit',
            'credit' => 'Kredit',
            default => ucfirst($state),
          })
          ->color(fn(string $state): string => match ($state) {
            'cash' => 'success',
            'qris' => 'info',
            'transfer' => 'warning',
            'debit' => 'primary',
            'credit' => 'danger',
            default => 'gray',
          }),

        Tables\Columns\TextColumn::make('total')
          ->label('Total')
          ->money('IDR')
          ->weight('bold')
          ->alignEnd()
          ->summarize([
            Tables\Columns\Summarizers\Sum::make()
              ->money('IDR')
              ->label('Total'),
          ]),
      ])
      ->defaultSort('created_at', 'desc')
      ->paginated(false)
      ->striped();
  }
}
