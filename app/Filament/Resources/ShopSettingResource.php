<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ShopSettingResource\Pages;
use App\Models\ShopSetting;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Components;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables;
use Filament\Tables\Table;
use UnitEnum;

class ShopSettingResource extends Resource
{
    protected static ?string $model = ShopSetting::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-building-storefront';

    protected static ?string $navigationLabel = 'Pengaturan Toko';

    protected static ?string $modelLabel = 'Pengaturan Toko';

    protected static UnitEnum|string|null $navigationGroup = 'Pengaturan';

    protected static ?int $navigationSort = 99;

    public static function schema(Schema $schema): Schema
    {
        return $schema
            ->components([
                Components\Section::make('Informasi Toko')
                    ->schema([
                        Components\TextInput::make('shop_name')
                            ->label('Nama Toko')
                            ->required()
                            ->maxLength(255),

                        Components\TextInput::make('tagline')
                            ->label('Tagline')
                            ->maxLength(255),

                        Components\Textarea::make('description')
                            ->label('Deskripsi')
                            ->rows(3)
                            ->columnSpanFull(),

                        Components\Textarea::make('about_us')
                            ->label('Tentang Kami')
                            ->rows(5)
                            ->columnSpanFull(),
                    ])
                    ->columns(2),

                Components\Section::make('Kontak')
                    ->schema([
                        Components\TextInput::make('email')
                            ->label('Email')
                            ->email()
                            ->maxLength(255),

                        Components\TextInput::make('phone')
                            ->label('Telepon')
                            ->tel()
                            ->maxLength(255),

                        Components\TextInput::make('whatsapp')
                            ->label('WhatsApp')
                            ->tel()
                            ->maxLength(255),

                        Components\Textarea::make('address')
                            ->label('Alamat')
                            ->rows(2),

                        Components\TextInput::make('city')
                            ->label('Kota')
                            ->maxLength(255),

                        Components\TextInput::make('postal_code')
                            ->label('Kode Pos')
                            ->maxLength(255),
                    ])
                    ->columns(2),

                Components\Section::make('Media Sosial')
                    ->schema([
                        Components\TextInput::make('facebook_url')
                            ->label('Facebook')
                            ->url()
                            ->maxLength(255),

                        Components\TextInput::make('instagram_url')
                            ->label('Instagram')
                            ->url()
                            ->maxLength(255),

                        Components\TextInput::make('twitter_url')
                            ->label('Twitter')
                            ->url()
                            ->maxLength(255),

                        Components\TextInput::make('youtube_url')
                            ->label('YouTube')
                            ->url()
                            ->maxLength(255),
                    ])
                    ->columns(2),

                Components\Section::make('Gambar')
                    ->schema([
                        Components\FileUpload::make('logo')
                            ->label('Logo')
                            ->image()
                            ->directory('shop-images'),

                        Components\FileUpload::make('hero_image')
                            ->label('Hero Image')
                            ->image()
                            ->directory('shop-images'),

                        Components\FileUpload::make('about_image')
                            ->label('About Image')
                            ->image()
                            ->directory('shop-images'),
                    ])
                    ->columns(3),

                Components\Section::make('Lainnya')
                    ->schema([
                        Components\Toggle::make('is_open')
                            ->label('Toko Buka')
                            ->default(true),

                        Components\Textarea::make('announcement')
                            ->label('Pengumuman')
                            ->rows(2)
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('shop_name')
                    ->label('Nama Toko')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('email')
                    ->label('Email')
                    ->searchable(),

                Tables\Columns\TextColumn::make('phone')
                    ->label('Telepon'),

                Tables\Columns\IconColumn::make('is_open')
                    ->label('Status')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger'),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Terakhir Diubah')
                    ->dateTime('d M Y H:i')
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                // Disable bulk actions for single record
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageShopSettings::route('/'),
        ];
    }

    // Disable create and delete for single record resource
    public static function canCreate(): bool
    {
        return false;
    }

    public static function canDeleteAny(): bool
    {
        return false;
    }

    public static function canDelete($record): bool
    {
        return false;
    }
}
