<?php

namespace App\Filament\Resources\UserResource\Widgets;

use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class UserStats extends BaseWidget
{
    public ?User $record; // connect a model with record

    protected function getStats(): array
    {
        return [
           Stat::make('Name', $this->record->name),
           Stat::make('Posts', $this->record->posts()->count()),
        ];
    }
}
