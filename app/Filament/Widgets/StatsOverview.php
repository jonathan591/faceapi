<?php

namespace App\Filament\Widgets;

use App\Models\Cargo;
use App\Models\Empleado;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected static ?string $navigationGroup = 'char';
    protected static ?int $sort = 1;
    protected function getStats(): array
    {
        return [
            Stat::make('Usuarios', User::count())
                ->chart([7, 2, 10, 3, 15, 4, 17])
                ->color('success'),
            Stat::make('Empleados', Empleado::count())
                ->chart([17, 3, 8, 2, 12, 4, 14])
                ->color('danger'),
            Stat::make('Cargos', Cargo::count())
                ->chart([14, 2, 7, 1, 9, 3, 11])

                ->color('info'),
        ];
    }
}
