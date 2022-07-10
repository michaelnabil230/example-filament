<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\OrderResource;
use Filament\Tables;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;

class LatestOrders extends TableWidget
{
    protected int | string | array $columnSpan = 'full';

    protected static ?int $sort = 2;

    public function getDefaultTableRecordsPerPageSelectOption(): int
    {
        return 5;
    }

    protected function getDefaultTableSortColumn(): ?string
    {
        return 'created_at';
    }

    protected function getDefaultTableSortDirection(): ?string
    {
        return 'desc';
    }

    protected function getTableQuery(): Builder
    {
        return OrderResource::getEloquentQuery();
    }

    protected function getTableColumns(): array
    {
        return [
            Tables\Columns\TextColumn::make('created_at')
                ->label('Order Date')
                ->date()
                ->sortable(),
            Tables\Columns\TextColumn::make('number')
                ->searchable()
                ->sortable(),
            Tables\Columns\TextColumn::make('customer.name')
                ->searchable()
                ->sortable(),
            Tables\Columns\BadgeColumn::make('status')
                ->colors([
                    'secondary',
                    'danger' => 'cancelled',
                    'warning' => 'processing',
                    'success' => 'delivered',
                ]),
            Tables\Columns\TextColumn::make('total_price')
                ->searchable()
                ->sortable(),
            Tables\Columns\TextColumn::make('shipping_price')
                ->label('Shipping cost')
                ->searchable()
                ->sortable(),
        ];
    }
}
