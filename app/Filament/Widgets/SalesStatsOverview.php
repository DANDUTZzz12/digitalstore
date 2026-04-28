<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use App\Models\Stock;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class SalesStatsOverview extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $todayRevenue = Order::where('status', Order::STATUS_PAID)
            ->whereDate('paid_at', today())
            ->sum('total_payment');

        $monthRevenue = Order::where('status', Order::STATUS_PAID)
            ->whereMonth('paid_at', now()->month)
            ->whereYear('paid_at', now()->year)
            ->sum('total_payment');

        $pendingCount = Order::where('status', Order::STATUS_PENDING)->count();
        $paidCount = Order::where('status', Order::STATUS_PAID)->count();
        $availableStock = Stock::where('is_sold', false)->count();

        return [
            Stat::make('Pendapatan Hari Ini', 'Rp '.number_format($todayRevenue, 0, ',', '.'))
                ->description('Order PAID hari ini')
                ->color('success'),

            Stat::make('Pendapatan Bulan Ini', 'Rp '.number_format($monthRevenue, 0, ',', '.'))
                ->color('success'),

            Stat::make('Pesanan Sukses', (string) $paidCount)
                ->description($pendingCount.' pending')
                ->color('primary'),

            Stat::make('Stok Tersedia', (string) $availableStock)
                ->description($availableStock < 10 ? 'Hampir habis!' : 'Cukup')
                ->color($availableStock < 10 ? 'danger' : 'success'),
        ];
    }
}
