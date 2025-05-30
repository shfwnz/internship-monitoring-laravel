<?php

namespace App\Filament\Admin\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

// Model
use App\Models\Industry;
use App\Models\Student;
use App\Models\Teacher;

class InternshipStatsOverview extends BaseWidget
{
    protected static ?int $sort = 1;
    protected function getStats(): array
    {
        return [
            Stat::make('Industries', Industry::count())
                ->description('Industry Total')
                ->descriptionIcon('heroicon-o-building-office-2')
                ->color('primary'),
            Stat::make('Teachers', Teacher::count())
                ->description('Teacher Total')
                ->descriptionIcon('heroicon-o-academic-cap')
                ->color('primary'),
            Stat::make('Students', Student::count())
                ->description('Student Total')
                ->descriptionIcon('heroicon-o-user-group')
                ->color('primary'),
        ];
    }
}
