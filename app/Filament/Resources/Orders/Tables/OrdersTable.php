<?php

namespace App\Filament\Resources\Orders\Tables;

use App\Models\Order;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class OrdersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('order_code')->label('Kode')->searchable()->copyable(),
                TextColumn::make('product.name')->label('Produk')->searchable(),
                TextColumn::make('variant.name')->label('Paket')->badge(),
                TextColumn::make('customer_email')->label('Email')->searchable()->copyable(),
                TextColumn::make('total_payment')
                    ->label('Total')
                    ->money('IDR', locale: 'id')
                    ->sortable(),
                TextColumn::make('status')
                    ->badge()
                    ->colors([
                        'warning' => Order::STATUS_PENDING,
                        'success' => Order::STATUS_PAID,
                        'danger' => [Order::STATUS_FAILED, Order::STATUS_EXPIRED, Order::STATUS_CANCELLED],
                        'secondary' => Order::STATUS_REFUNDED,
                    ]),
                TextColumn::make('created_at')->label('Dibuat')->dateTime()->sortable(),
                TextColumn::make('paid_at')->label('Dibayar')->dateTime()->sortable()->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')->options([
                    Order::STATUS_PENDING => 'Pending',
                    Order::STATUS_PAID => 'Paid',
                    Order::STATUS_FAILED => 'Failed',
                    Order::STATUS_EXPIRED => 'Expired',
                    Order::STATUS_CANCELLED => 'Cancelled',
                    Order::STATUS_REFUNDED => 'Refunded',
                ]),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
