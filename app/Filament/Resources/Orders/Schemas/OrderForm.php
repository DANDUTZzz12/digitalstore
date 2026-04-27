<?php

namespace App\Filament\Resources\Orders\Schemas;

use App\Models\Order;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class OrderForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('order_code')->disabled(),
                Select::make('status')
                    ->options([
                        Order::STATUS_PENDING => 'Pending',
                        Order::STATUS_PAID => 'Paid',
                        Order::STATUS_FAILED => 'Failed',
                        Order::STATUS_EXPIRED => 'Expired',
                        Order::STATUS_CANCELLED => 'Cancelled',
                        Order::STATUS_REFUNDED => 'Refunded',
                    ])
                    ->required(),
                TextInput::make('customer_email')->email()->disabled(),
                TextInput::make('customer_phone')->disabled(),
                TextInput::make('total_payment')->prefix('Rp')->disabled(),
                TextInput::make('payment_method')->disabled(),
                TextInput::make('payment_ref')->disabled(),
                DateTimePicker::make('paid_at')->disabled(),
                DateTimePicker::make('expired_at')->disabled(),
            ]);
    }
}
