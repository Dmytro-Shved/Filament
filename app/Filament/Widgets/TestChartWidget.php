<?php

namespace App\Filament\Widgets;

use App\Models\Category;
use App\Models\Post;
use App\Models\User;
use Filament\Widgets\ChartWidget;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;

class TestChartWidget extends ChartWidget
{
    protected static ?string $heading = 'Test Line Chart';

//    protected static ?string $maxHeight = '300px';

    protected function getData(): array
    {
        /**[Trend package]
         * Helpfull package
         *  command: composer require flowframe/laravel-trend
         * allows you to simply add a data to chart
         */
        $data = Trend::model(User::class) // model
            ->between(
                start: now()->subMonth(6), // current date - 6 months
                end: now() // current date
            )
            ->perMonth() // interval
            ->count(); // number of users


        /**[line chart]
         * line chart example below
         * (don't forget to return type 'line' in getType() at the bottom)
         */
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

        /**[doughnut chart]
         * doughnut chart example below
         * (don't forget to return type 'doughnut' in getType() at the bottom)
         */
//        return [
//            'datasets' => [
//                [
//                    'label' => 'Posts',
//                    'data' => [
//                        Post::count(),
//                        User::count(),
//                        Category::count(),
//                    ],
//                    'backgroundColor' => [
//                        'rgb(255, 99, 132)',
//                        'rgb(54, 162, 235)',
//                        'rgb(21, 231, 122)',
//                    ],
//                ],
//            ],
//            'labels' => ["Posts", "Users", "Categories"],
//        ];
    }

    protected function getType(): string
    {
        return 'line';
//        return 'doughnut';
    }
}
