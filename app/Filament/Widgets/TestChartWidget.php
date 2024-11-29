<?php

namespace App\Filament\Widgets;

use App\Models\Category;
use App\Models\Post;
use App\Models\User;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;


class TestChartWidget extends ChartWidget
{
    use InteractsWithPageFilters;

    protected static ?string $heading = 'Test Line Chart';

    protected function getData(): array
    {
//        $start = $this->filters['startDate'];
//        $end = $this->filters['endDate'];

        $start = Carbon::parse('11-01-2024');
        $end = Carbon::parse('29-11-2024');

        $data = Trend::model(User::class)
            ->between(
                start: $start ? Carbon::parse($start) :  now()->subMonth(6),
                end: $end ? Carbon::parse($end) :  now(),
            )
            ->perWeek()
            ->count();

        return [
            'datasets' => [
                [
                    'label' => 'Users',
                    'data' => $data->map(fn (TrendValue $value) => $value->aggregate),
                    'borderColor' =>  'rgb(75, 192, 192)',
                ],
            ],
            'labels' => $data->map(fn (TrendValue $value) => $value->date),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
