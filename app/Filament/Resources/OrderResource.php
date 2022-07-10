<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrderResource\Pages;
use App\Filament\Resources\OrderResource\Widgets;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static ?string $navigationIcon = 'heroicon-o-collection';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Group::make()
                    ->schema([
                        Forms\Components\Card::make()
                            ->schema([
                                Forms\Components\TextInput::make('number')
                                    ->default('OR-'.random_int(100000, 999999))
                                    ->disabled()
                                    ->required(),
                                Forms\Components\Select::make('customer_id')
                                    ->relationship('customer', 'name')
                                    ->searchable()
                                    ->getSearchResultsUsing(fn (string $query) => Customer::where('name', 'like', "%{$query}%")->pluck('name', 'id'))
                                    ->getOptionLabelUsing(fn ($value): ?string => Customer::find($value)?->name)
                                    ->required(),
                                Forms\Components\Select::make('status')
                                    ->options([
                                        'new' => 'New',
                                        'processing' => 'Processing',
                                        'shipped' => 'Shipped',
                                        'delivered' => 'Delivered',
                                        'cancelled' => 'Cancelled',
                                    ])
                                    ->default('new')
                                    ->required(),
                                Forms\Components\MarkdownEditor::make('notes')
                                    ->columnSpan([
                                        'sm' => 2,
                                    ]),

                                Forms\Components\Select::make('shipping_method')
                                    ->options([
                                        'free' => 'Free',
                                        'flat' => 'Flat',
                                        'flat_rate' => 'Flat rate',
                                        'flat_rate_per_item' => 'Flat rate per item',
                                    ])
                                    ->default('free')
                                    ->required(),
                            ])->columns([
                                'sm' => 2,
                            ]),
                        Forms\Components\Card::make()
                            ->schema([
                                Forms\Components\Placeholder::make('Products'),
                                Forms\Components\Repeater::make('products')
                                    ->relationship()
                                    ->schema([
                                        Forms\Components\Select::make('product_id')
                                            ->label('Product')
                                            ->options(Product::query()->pluck('name', 'id'))
                                            ->required()
                                            ->reactive()
                                            ->afterStateUpdated(fn ($state, callable $set) => $set('price', Product::find($state)?->price ?? 0))
                                            ->columnSpan([
                                                'md' => 5,
                                            ]),
                                        Forms\Components\TextInput::make('quantity')
                                            ->numeric()
                                            ->integer()
                                            ->default(1)
                                            ->columnSpan([
                                                'md' => 2,
                                            ])
                                            ->required(),
                                        Forms\Components\TextInput::make('price')
                                            ->disabled()
                                            ->numeric()
                                            ->required()
                                            ->columnSpan([
                                                'md' => 3,
                                            ]),
                                    ])
                                    ->dehydrated()
                                    ->defaultItems(1)
                                    ->disableLabel()
                                    ->columns([
                                        'md' => 10,
                                    ])
                                    ->required(),
                            ]),
                    ])
                    ->columnSpan([
                        'sm' => 2,
                    ]),
                Forms\Components\Card::make()
                    ->schema([
                        Forms\Components\Placeholder::make('created_at')
                            ->label('Created at')
                            ->content(fn (?Order $record): string => $record ? $record->created_at->diffForHumans() : '-'),
                        Forms\Components\Placeholder::make('updated_at')
                            ->label('Last modified at')
                            ->content(fn (?Order $record): string => $record ? $record->updated_at->diffForHumans() : '-'),
                    ])
                    ->columnSpan(1),
            ])
            ->columns([
                'sm' => 3,
                'lg' => null,
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('number')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('customer.name')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'danger' => 'cancelled',
                        'warning' => 'processing',
                        'success' => fn ($state) => in_array($state, ['delivered', 'shipped']),
                    ]),
                Tables\Columns\TextColumn::make('total_price')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('shipping_price')
                    ->label('Shipping cost')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Order Date')
                    ->date()
                    ->toggleable(),
            ])
            ->filters([
                Tables\Filters\Filter::make('created_at')
                    ->form([
                        Forms\Components\DatePicker::make('created_from')
                            ->placeholder(fn ($state): string => 'Dec 18, '.now()->subYear()->format('Y')),
                        Forms\Components\DatePicker::make('created_until')
                            ->placeholder(fn ($state): string => now()->format('M d, Y')),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['created_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['created_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                            );
                    }),
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\ViewAction::make(),
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
            'index' => Pages\ListOrders::route('/'),
            'create' => Pages\CreateOrder::route('/create'),
            'edit' => Pages\EditOrder::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with('customer')
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['number', 'customer.name'];
    }

    protected static function getNavigationBadge(): ?string
    {
        return static::$model::where('status', 'new')->count();
    }

    public static function getWidgets(): array
    {
        return [
            Widgets\OrderStats::class,
        ];
    }
}
