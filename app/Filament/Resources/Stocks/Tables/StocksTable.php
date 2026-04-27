<?php

namespace App\Filament\Resources\Stocks\Tables;

use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Table;
use Illuminate\Support\HtmlString;

class StocksTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label(new HtmlString('
                        <div style="display:flex; gap:10px; align-items:center;">
                            <input type="checkbox" onclick="document.querySelectorAll(\'.chk-stok\').forEach(el => el.checked = this.checked)" style="transform:scale(1.2); cursor:pointer;" title="Centang Semua">
                            <button type="button" onclick="
                                let vals = Array.from(document.querySelectorAll(\'.chk-stok:checked\')).map(el => el.value);
                                if(vals.length === 0) { alert(\'Centang minimal 1 data dulu!\'); return; }
                                if(confirm(\'Yakin ingin menghapus \' + vals.length + \' data terpilih?\')) {
                                    let lw = window.Livewire;
                                    if(lw.dispatch) lw.dispatch(\'hapusCentang\', {ids: vals}); // <-- Mengirimkan ID, bukan email
                                    else lw.emit(\'hapusCentang\', vals);
                                }
                            " style="background:#e3342f; color:white; border:none; border-radius:4px; padding:4px 8px; font-size:11px; font-weight:bold; cursor:pointer;">HAPUS</button>
                        </div>
                    '))
                    // 👇 PERUBAHAN UTAMA: value="'.$record->id.'"
                    ->formatStateUsing(fn ($record) => new HtmlString('<input type="checkbox" class="chk-stok" value="'.$record->id.'" style="transform:scale(1.2); cursor:pointer;">'))
                    ->html()
                    ->searchable(false)
                    ->sortable(false),

                TextColumn::make('variant.product.name')->label('Produk')->searchable(),
                TextColumn::make('variant.name')->label('Varian')->badge(),
                TextColumn::make('email_or_phone')->label('Email/No HP')->copyable()->searchable(),
                IconColumn::make('is_sold')
                    ->label('Status')
                    ->boolean()
                    ->trueColor('danger')
                    ->falseColor('success')
                    ->trueIcon('heroicon-o-x-circle')
                    ->falseIcon('heroicon-o-check-circle'),
                TextColumn::make('created_at')->label('Ditambahkan Pada')->dateTime()->sortable(),
            ]);
    }
}