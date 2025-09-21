<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\Player;

class PlayerCountStat extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Total Players', Player::count())
                ->description('New this month: ' . $this->getMonthlyGrowth())
                ->icon('heroicon-o-users')
                ->color('primary'),
        ];
    }

    protected function getMonthlyGrowth(): int
    {
        return Player::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();
    }
}
