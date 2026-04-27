<?php

namespace App\Filament\Resources\Articles\Schemas;

use App\Models\Article;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class ArticleForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('title')
                    ->label('Judul')
                    ->required()
                    ->maxLength(255)
                    ->live(onBlur: true)
                    ->afterStateUpdated(function (string $state, callable $set, callable $get, $record) {
                        if (! $record && empty($get('slug'))) {
                            $set('slug', Article::makeSlug($state));
                        }
                    })
                    ->columnSpanFull(),

                TextInput::make('slug')
                    ->label('Slug (URL)')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(255)
                    ->helperText('Akan tampil di URL: /artikel/<slug>')
                    ->columnSpanFull(),

                FileUpload::make('cover_image')
                    ->label('Gambar Cover')
                    ->image()
                    ->imageEditor()
                    ->disk('public')
                    ->directory('artikel-covers')
                    ->visibility('public')
                    ->maxSize(2048)
                    ->columnSpanFull(),

                Textarea::make('excerpt')
                    ->label('Ringkasan (1-2 kalimat)')
                    ->rows(2)
                    ->maxLength(255)
                    ->columnSpanFull(),

                RichEditor::make('content')
                    ->label('Isi Artikel')
                    ->required()
                    ->columnSpanFull(),

                Toggle::make('is_published')
                    ->label('Terbitkan')
                    ->default(true),

                DateTimePicker::make('published_at')
                    ->label('Tanggal Terbit')
                    ->default(now())
                    ->seconds(false),
            ]);
    }
}
