<?php

namespace App\Filament\Widgets;

use App\Models\Customer;
use Filament\Widgets\LineChartWidget;
use Illuminate\Support\Facades\DB;

class CustomersChart extends LineChartWidget
{
    protected static ?string $heading = 'Total customers';

    protected static ?int $sort = 2;

    protected function getData(): array
    {
        $data = Customer::query()
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
                    'label' => 'Customers',
                    'data' => $formattedData,
                ],
            ],
            'labels' => $months,
        ];
    }
}
