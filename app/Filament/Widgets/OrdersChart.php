<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use Filament\Widgets\LineChartWidget;
use Illuminate\Support\Facades\DB;

class OrdersChart extends LineChartWidget
{
    protected static ?string $heading = 'Orders per month';

    protected static ?int $sort = 1;

    protected function getData(): array
    {
        $data = Order::query()
            ->selectRaw(DB::raw('DATE_FORMAT(created_at, "%b") as mb , count(*) as count'))
            ->groupBy('mb')
            ->get()
            ->keyBy('mb')
            ->toArray();

        $months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];

        $formattedData = [];
        foreach ($months as $month) {
            $formattedData[$month] = $data[$month]['count'] ?? 0;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Orders',
                    'data' => $formattedData,
                ],
            ],
            'labels' => $months,
        ];
    }
}
