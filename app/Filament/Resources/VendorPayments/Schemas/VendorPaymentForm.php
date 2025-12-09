<?php

namespace App\Filament\Resources\VendorPayments\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Placeholder;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Schema;
use App\Models\Vendor;
use App\Models\PurchaseOrder;
use Illuminate\Support\Facades\Auth;

class VendorPaymentForm
{
    public static function make(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('Vendor Payment')
                    ->tabs([
                        // Tab 1: Payment Information
                        Tabs\Tab::make('Payment Info')
                            ->schema([
                                Fieldset::make('Payment Details')
                                    ->schema([
                                        Placeholder::make('payment_number')
                                            ->label('Payment Number')
                                            ->content(fn($record) => $record?->payment_number ?? 'Auto-generated after save')
                                            ->helperText('📝 Generated automatically: PAY-YYYYMM-XXXX')
                                            ->hidden(fn($context) => $context === 'create'),

                                        Select::make('vendor_id')
                                            ->label('Vendor')
                                            ->relationship('vendor', 'name')
                                            ->searchable()
                                            ->preload()
                                            ->required()
                                            ->helperText('Select vendor to pay')
                                            ->reactive()
                                            ->afterStateUpdated(function ($state, callable $set) {
                                                if ($state) {
                                                    $vendor = Vendor::find($state);
                                                    $set('_vendor_email', $vendor?->email);
                                                    $set('_vendor_phone', $vendor?->phone);
                                                }
                                            }),

                                        Placeholder::make('_vendor_info')
                                            ->label('Vendor Contact')
                                            ->content(
                                                fn($get) =>
                                                $get('_vendor_email') || $get('_vendor_phone')
                                                ? "📧 {$get('_vendor_email')} | 📱 {$get('_vendor_phone')}"
                                                : 'Select vendor to see contact info'
                                            )
                                            ->hidden(fn($context) => $context === 'create'),

                                        Select::make('purchase_order_id')
                                            ->label('Purchase Order Reference (Optional)')
                                            ->relationship(
                                                'purchaseOrder',
                                                'po_number',
                                                fn($query, $get) => $query
                                                    ->when(
                                                        $get('vendor_id'),
                                                        fn($q, $vendorId) =>
                                                        $q->where('vendor_id', $vendorId)
                                                    )
                                                    ->whereIn('status', ['approved', 'received', 'partially_received'])
                                            )
                                            ->searchable()
                                            ->preload()
                                            ->helperText('Link to specific PO (optional)')
                                            ->reactive()
                                            ->afterStateUpdated(function ($state, callable $set) {
                                                if ($state) {
                                                    $po = PurchaseOrder::find($state);
                                                    if ($po) {
                                                        $set('amount', $po->total);
                                                        $set('_po_total', 'Rp ' . number_format($po->total, 0, ',', '.'));
                                                    }
                                                }
                                            }),

                                        Placeholder::make('_po_total')
                                            ->label('PO Total')
                                            ->content(fn($get) => $get('_po_total') ?? 'No PO selected')
                                            ->hidden(fn($get) => !$get('purchase_order_id')),
                                    ])
                                    ->columns(2),

                                Fieldset::make('Payment Amount & Method')
                                    ->schema([
                                        DatePicker::make('payment_date')
                                            ->label('Payment Date')
                                            ->required()
                                            ->default(now())
                                            ->native(false)
                                            ->maxDate(now())
                                            ->helperText('When was this payment made?'),

                                        TextInput::make('amount')
                                            ->label('Payment Amount')
                                            ->required()
                                            ->numeric()
                                            ->prefix('Rp')
                                            ->step(0.01)
                                            ->helperText('Amount paid to vendor'),

                                        Select::make('payment_method')
                                            ->label('Payment Method')
                                            ->options([
                                                'cash' => 'Cash',
                                                'bank_transfer' => 'Bank Transfer',
                                                'check' => 'Check',
                                                'giro' => 'Giro',
                                                'other' => 'Other',
                                            ])
                                            ->default('bank_transfer')
                                            ->required()
                                            ->reactive()
                                            ->helperText('How was this payment made?'),

                                        TextInput::make('reference_number')
                                            ->label(fn($get) => match ($get('payment_method')) {
                                                'check' => 'Check Number',
                                                'giro' => 'Giro Number',
                                                'bank_transfer' => 'Transfer Reference',
                                                default => 'Reference Number',
                                            })
                                            ->maxLength(100)
                                            ->helperText(fn($get) => match ($get('payment_method')) {
                                                'check' => 'Enter check number',
                                                'giro' => 'Enter giro number',
                                                'bank_transfer' => 'Enter transfer/transaction ID',
                                                default => 'Optional reference number',
                                            }),

                                        TextInput::make('bank_account')
                                            ->label('Bank Account')
                                            ->maxLength(100)
                                            ->helperText('Destination/source bank account')
                                            ->visible(fn($get) => in_array($get('payment_method'), ['bank_transfer', 'check', 'giro'])),

                                        Select::make('paid_by')
                                            ->label('Paid By')
                                            ->relationship('paidBy', 'name')
                                            ->default(Auth::id())
                                            ->required()
                                            ->helperText('User who processed this payment'),

                                        Select::make('status')
                                            ->label('Status')
                                            ->options([
                                                'draft' => 'Draft',
                                                'confirmed' => 'Confirmed',
                                                'cancelled' => 'Cancelled',
                                            ])
                                            ->default('draft')
                                            ->required()
                                            ->helperText('Payment status'),
                                    ])
                                    ->columns(3),

                                Fieldset::make('Additional Information')
                                    ->schema([
                                        Textarea::make('notes')
                                            ->label('Notes')
                                            ->rows(3)
                                            ->placeholder('Any additional notes about this payment...')
                                            ->columnSpanFull(),
                                    ]),
                            ]),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
