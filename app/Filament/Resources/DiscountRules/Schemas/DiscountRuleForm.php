<?php

namespace App\Filament\Resources\DiscountRules\Schemas;

use Filament\Schemas\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\DatePicker;
use App\Models\CustomerTier;
use App\Models\Product;

class DiscountRuleForm
{
    public static function schema(): array
    {
        return [

            // ========== SECTION 1: BASIC INFO ==========
            Section::make('📋 Informasi Dasar')
                ->description('Informasi umum tentang promosi/diskon')
                ->columns(3)
                ->columnSpan(3)
                ->schema([
                    TextInput::make('code')
                        ->label('Kode Diskon')
                        ->placeholder('DISC-0001 (auto generate)')
                        ->helperText('Akan otomatis di-generate')
                        ->disabled()
                        ->dehydrated(false)
                        ->columnSpan(1),

                    TextInput::make('name')
                        ->label('Nama Promosi')
                        ->required()
                        ->placeholder('Diskon Hari Raya')
                        ->helperText('Nama yang mudah dikenali untuk promosi ini')
                        ->columnSpan(2),

                    Textarea::make('description')
                        ->label('Deskripsi')
                        ->placeholder('Deskripsi detail tentang promosi ini...')
                        ->rows(2)
                        ->helperText('Penjelasan lengkap tentang promosi (opsional)')
                        ->columnSpan(3),
                ]),

            // ========== SECTION 2: DATE RANGE ==========
            Section::make('📅 Periode Berlaku')
                ->description('Kapan promosi ini berlaku')
                ->columns(2)
                ->columnSpan(2)
                ->schema([
                    DatePicker::make('valid_from')
                        ->label('Mulai Berlaku')
                        ->required()
                        ->default(now())
                        ->helperText('Tanggal mulai promosi berlaku')
                        ->columnSpan(1),

                    DatePicker::make('valid_to')
                        ->label('Berakhir')
                        ->helperText('Kosongkan jika tidak ada batas waktu')
                        ->columnSpan(1),
                ]),

            // ========== SECTION 3: ACTIVE STATUS ==========
            Section::make('⚡ Status')
                ->description('Aktifkan/nonaktifkan promosi')
                ->columnSpan(1)
                ->schema([
                    Toggle::make('is_active')
                        ->label('Promosi Aktif')
                        ->default(true)
                        ->helperText('ON = Promosi berjalan | OFF = Promosi dimatikan')
                        ->columnSpan(1),
                ]),

            // ========== SECTION 4: PRIORITY ==========
            Section::make('🎯 Prioritas')
                ->description('Urutan penerapan diskon (angka lebih kecil = lebih dulu)')
                ->columnSpan(3)
                ->schema([
                    TextInput::make('priority')
                        ->label('Prioritas')
                        ->numeric()
                        ->default(10)
                        ->minValue(1)
                        ->helperText('Angka 1-100. Semakin kecil, semakin prioritas.')
                        ->columnSpan(1),
                ]),

            // ========== SECTION 5: CONDITIONS (USER FRIENDLY) ==========
            Section::make('✅ Syarat & Kondisi Promosi')
                ->description('Tentukan kapan/dimana promosi ini bisa dipakai')
                ->collapsible()
                ->collapsed(false)
                ->columns(3)
                ->columnSpan(3)
                ->schema([

                    TextInput::make('condition_min_subtotal')
                        ->label('💵 Minimal Belanja')
                        ->numeric()
                        ->prefix('Rp')
                        ->placeholder('0 (kosongkan untuk tidak ada minimal)')
                        ->helperText('Minimal total belanja agar diskon berlaku')
                        ->columnSpan(1),

                    TextInput::make('condition_max_subtotal')
                        ->label('💎 Maksimal Belanja')
                        ->numeric()
                        ->prefix('Rp')
                        ->placeholder('Kosongkan untuk tidak ada maksimal')
                        ->helperText('Maksimal total belanja agar diskon berlaku')
                        ->columnSpan(1),

                    TextInput::make('condition_min_quantity')
                        ->label('📦 Minimal Item')
                        ->numeric()
                        ->placeholder('0 (kosongkan untuk tidak ada minimal)')
                        ->helperText('Minimal jumlah item yang dibeli')
                        ->columnSpan(1),

                    Select::make('condition_customer_tiers')
                        ->label('👑 Hanya Tier Tertentu')
                        ->multiple()
                        ->searchable()
                        ->options(function () {
                            return CustomerTier::pluck('name', 'id')->toArray();
                        })
                        ->placeholder('Semua tier (kosongkan untuk semua)')
                        ->helperText('Promosi hanya untuk tier customer tertentu')
                        ->columnSpan(1),

                    Select::make('condition_required_products')
                        ->label('🛒 Harus Beli Produk Ini')
                        ->multiple()
                        ->searchable()
                        ->options(function () {
                            return Product::where('type', 'finished')
                                ->where('is_sellable', true)
                                ->pluck('name', 'id')
                                ->toArray();
                        })
                        ->placeholder('Semua produk (kosongkan untuk semua)')
                        ->helperText('Promosi hanya jika membeli produk tertentu')
                        ->columnSpan(1),

                    Select::make('condition_day_of_week')
                        ->label('📆 Hanya Hari Tertentu')
                        ->multiple()
                        ->options([
                            0 => 'Minggu',
                            1 => 'Senin',
                            2 => 'Selasa',
                            3 => 'Rabu',
                            4 => 'Kamis',
                            5 => 'Jumat',
                            6 => 'Sabtu',
                        ])
                        ->placeholder('Semua hari (kosongkan untuk semua hari)')
                        ->helperText('Promosi hanya berlaku di hari tertentu')
                        ->columnSpan(1),

                    Toggle::make('condition_is_birthday')
                        ->label('🎂 Hanya Ulang Tahun')
                        ->helperText('ON = Hanya untuk customer yang ulang tahun hari ini')
                        ->columnSpan(1),

                    Toggle::make('condition_is_first_purchase')
                        ->label('🎉 Hanya Pembelian Pertama')
                        ->helperText('ON = Hanya untuk customer yang belanja pertama kali')
                        ->columnSpan(1),
                ]),

            // ========== SECTION 6: ACTIONS (USER FRIENDLY) ==========
            Section::make('🎯 Diskon yang Diberikan')
                ->description('Tentukan diskon/benefit apa yang akan diberikan')
                ->collapsible()
                ->collapsed(false)
                ->columns(3)
                ->columnSpan(3)
                ->schema([

                    Select::make('action_discount_type')
                        ->label('Jenis Diskon')
                        ->required()
                        ->options([
                            'percentage' => '💯 Diskon Persentase (%)',
                            'fixed' => '💵 Diskon Nominal (Rp)',
                            'free_item' => '🎁 Gratis Produk',
                        ])
                        ->default('percentage')
                        ->live()
                        ->helperText('Pilih jenis diskon yang akan diberikan')
                        ->columnSpan(1),

                    TextInput::make('action_discount_value')
                        ->label('Nilai Diskon')
                        ->required()
                        ->numeric()
                        ->placeholder('20')
                        ->helperText(fn ($get) =>
                            $get('action_discount_type') === 'percentage'
                                ? 'Masukkan angka persentase (contoh: 20 untuk 20%)'
                                : 'Masukkan nominal rupiah (contoh: 50000)'
                        )
                        ->prefix(fn ($get) => $get('action_discount_type') === 'percentage' ? '' : 'Rp')
                        ->suffix(fn ($get) => $get('action_discount_type') === 'percentage' ? '%' : '')
                        ->visible(fn ($get) => $get('action_discount_type') !== 'free_item')
                        ->columnSpan(1),

                    TextInput::make('action_max_discount')
                        ->label('Maksimal Potongan')
                        ->numeric()
                        ->prefix('Rp')
                        ->placeholder('100000')
                        ->helperText('Maksimal potongan dalam rupiah (untuk batasi diskon %)')
                        ->visible(fn ($get) => $get('action_discount_type') === 'percentage')
                        ->columnSpan(1),

                    Select::make('action_free_product')
                        ->label('Produk Gratis')
                        ->searchable()
                        ->options(function () {
                            return Product::where('type', 'finished')
                                ->where('is_sellable', true)
                                ->pluck('name', 'id')
                                ->toArray();
                        })
                        ->placeholder('Pilih produk yang akan gratis')
                        ->helperText('Produk yang akan diberikan gratis')
                        ->visible(fn ($get) => $get('action_discount_type') === 'free_item')
                        ->columnSpan(2),

                    TextInput::make('action_free_quantity')
                        ->label('Jumlah Gratis')
                        ->numeric()
                        ->default(1)
                        ->minValue(1)
                        ->placeholder('1')
                        ->helperText('Berapa banyak produk gratis yang diberikan')
                        ->visible(fn ($get) => $get('action_discount_type') === 'free_item')
                        ->columnSpan(1),

                    Select::make('action_apply_to')
                        ->label('Terapkan Diskon Ke')
                        ->options([
                            'order' => '🛒 Total Order (semua item)',
                            'cheapest' => '💰 Item Termurah',
                            'most_expensive' => '💎 Item Termahal',
                        ])
                        ->default('order')
                        ->helperText('Tentukan diskon diterapkan ke mana')
                        ->columnSpan(3),
                ]),

            // ========== SECTION 7: USAGE LIMITS ==========
            Section::make('🔢 Batasan Penggunaan')
                ->description('Atur berapa kali promosi bisa dipakai')
                ->collapsible()
                ->columns(2)
                ->columnSpan(2)
                ->schema([
                    TextInput::make('usage_limit')
                        ->label('Total Limit Penggunaan')
                        ->numeric()
                        ->minValue(0)
                        ->placeholder('Kosongkan untuk unlimited')
                        ->helperText('Total berapa kali promosi bisa dipakai (semua customer)')
                        ->columnSpan(1),

                    TextInput::make('usage_per_customer')
                        ->label('Limit Per Customer')
                        ->numeric()
                        ->minValue(0)
                        ->placeholder('Kosongkan untuk unlimited')
                        ->helperText('Berapa kali 1 customer bisa pakai promosi ini')
                        ->columnSpan(1),
                ]),

            // ========== SECTION 8: CURRENT STATS (READ ONLY) ==========
            Section::make('📊 Statistik Penggunaan')
                ->description('Tracking penggunaan promosi')
                ->columnSpan(1)
                ->schema([
                    TextInput::make('usage_count')
                        ->label('Sudah Dipakai')
                        ->default(0)
                        ->disabled()
                        ->helperText('Total berapa kali promosi sudah dipakai')
                        ->columnSpan(1),
                ]),

            // ========== HIDDEN FIELDS: ACTUAL JSON STORAGE ==========
            Textarea::make('conditions')
                ->label('Conditions JSON (Hidden)')
                ->hidden()
                ->dehydrated()
                ->helperText('Auto-generated from user-friendly fields above'),

            Textarea::make('actions')
                ->label('Actions JSON (Hidden)')
                ->hidden()
                ->dehydrated()
                ->helperText('Auto-generated from user-friendly fields above'),
        ];
    }
}
