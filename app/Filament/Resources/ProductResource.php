<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Filament\Resources\ProductResource\RelationManagers;
use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder as ElBuilder;;
use Illuminate\Database\Eloquent\SoftDeletingScope;

use Filament\Forms\Components\Builder;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\ViewField;


class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?int $navigationSort     = 1;
    protected static ?string $navigationLabel = 'Продукция';
    protected static ?string $modelLabel = 'Продукция';
    protected static ?string $pluralModelLabel = 'Продукция';

    protected static ?string $path        = 'Фото';
    protected static ?string $title       = 'Название';
    protected static ?string $description = 'Описание';
//    protected static ?string $       = '';


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                FileUpload::make('path')
                    ->label(__(static::$path))
                    ->image()
                    ->imageEditor()
                    ->required()
                    ->storeFileNamesIn('attachment_file_names')
                    ->maxSize(1024)
                    ->directory('uploads')
                    ->columnSpanFull(),

                Textarea::make('title')
                    ->label('Название')
                    ->columnSpanFull(),
    
                Textarea::make('description')
                    ->label('Описание')
                    ->columnSpanFull(),

                Repeater::make('techno')
                    ->label('Конструктор техпроцесса')
                    ->extraAttributes(['class' => 'bg-gray-800'])

                    ->addActionLabel('Добавить в техпроцесс')
                    ->schema([
                        Textarea::make('title')
                            ->label(''),
                        Repeater::make('status')
                            ->label('Конструктор статуса')
                            ->addActionLabel('Добавить в статус')
                            ->schema([
                                Textarea::make('title')
                                    ->label(''),
                                Select::make('level')
                                    ->label('Тип')
                                    ->options([
                                        'common'  => 'Информация (нейтральный)',
                                        'ok'      => 'Хорошо (зеленый)',
                                        'warning' => 'Предупреждение (желтый)',
                                        'failure' => 'Ошибка (красный)',
                                    ])
            
                            ])
                    ])
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('path')
                    ->label(__(static::$path))
                    ->disk('public'),
                Tables\Columns\TextColumn::make('title')
                    ->label(__(static::$title))
                    ->searchable(),
                Tables\Columns\TextColumn::make('description')
                    ->label(__(static::$description))
                    ->searchable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): ElBuilder
    {
        return parent::getEloquentQuery()->where('title', '<>', 'none');
    }    
}
