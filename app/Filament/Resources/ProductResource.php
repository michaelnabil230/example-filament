<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Str;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-o-collection';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Card::make()
                    ->schema([
                        Forms\Components\Select::make('category_id')
                            ->relationship('category', 'name')
                            ->searchable()
                            ->required()
                            ->columnSpan([
                                'sm' => 2,
                            ]),
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->reactive()
                            ->afterStateUpdated(fn ($state, callable $set) => $set('slug', Str::slug($state))),
                        Forms\Components\TextInput::make('slug')
                            ->disabled()
                            ->required()
                            ->unique(Product::class, 'slug', fn ($record) => $record),
                        Forms\Components\MarkdownEditor::make('description')
                            ->required()
                            ->columnSpan([
                                'sm' => 2,
                            ]),
                        SpatieMediaLibraryFileUpload::make('media')
                            ->collection('product-images')
                            ->multiple()
                            ->maxFiles(5)
                            ->columnSpan([
                                'sm' => 2,
                            ]),
                        Forms\Components\TextInput::make('price')
                            ->numeric()
                            ->rules(['regex:/^\d{1,6}(\.\d{0,2})?$/'])
                            ->required(),
                        Forms\Components\TextInput::make('sku')
                            ->label('SKU (Stock Keeping Unit)')
                            ->unique(Product::class, 'sku', fn ($record) => $record)
                            ->required(),
                        Forms\Components\TextInput::make('qty')
                            ->label('Quantity')
                            ->numeric()
                            ->rules(['integer', 'min:0'])
                            ->required(),
                        Forms\Components\Toggle::make('is_visible')
                            ->label('Visible')
                            ->helperText('This product will be hidden from all sales channels.')
                            ->default(true),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\SpatieMediaLibraryImageColumn::make('product-image')
                    ->label('Image')
                    ->collection('product-images'),

                Tables\Columns\TextColumn::make('name')
                    ->label('Name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('category.name')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('price')
                    ->label('Price')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('sku')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('quantity')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\BooleanColumn::make('is_visible')
                    ->label('Visibility')
                    ->sortable()
                    ->toggleable(),
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\ViewAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
                Tables\Actions\RestoreBulkAction::make(),
                Tables\Actions\ForceDeleteBulkAction::make(),
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
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
