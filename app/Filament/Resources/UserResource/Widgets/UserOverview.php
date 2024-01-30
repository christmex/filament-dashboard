<?php

namespace App\Filament\Resources\UserResource\Widgets;

use App\Models\User;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;

class UserOverview extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Total Employee', User::all()->count()),
            Stat::make('Permanent Employees', User::where('permanent_date','!=',NULL)->get()->count()),
            Stat::make('Contract Employees', User::where('finish_contract','!=',NULL)->get()->count()),
        ];
    }
}
