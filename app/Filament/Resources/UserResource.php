<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

use Illuminate\Support\Facades\Hash;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?int $navigationSort     = 200;

    protected static ?string $modelLabel = 'Пользователь';
    protected static ?string $pluralModelLabel = 'Пользователи';
    protected static ?string $navigationLabel = 'Пользователи';

    protected static ?string $navigationGroup = 'Аутентификация';

    protected static ?string $name      = 'ФИО';
    protected static ?string $email     = 'Адрес ел. почты';
    protected static ?string $email_verified_at= 'Почта подтверждена';
    protected static ?string $password  = 'Пароль';
    protected static ?string $roles     = 'Роли';
    
//    public static function canViewAny(): bool
//    {
//        return auth()->user()->can('управлять пользователями');
//    }    

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->label(__(static::$name)),
                Forms\Components\TextInput::make('email')
                    ->email()
                    ->required()
                    ->label(__(static::$email)),
                Forms\Components\TextInput::make('password')
                    ->label(static::$password)
                    ->password()
                    ->maxLength(255)
                    ->dehydrateStateUsing(static function ($state, $record) use ($form) {
                            return !empty($state)
                                ? Hash::make($state)
                                : $record->password;
                        }),

                Forms\Components\Select::make('roles')
                    ->relationship(name: 'roles', titleAttribute: 'name')
//                    ->saveRelationshipsUsing(function (Model $record, $state) {
//                            $record->roles()->syncWithPivotValues($state, [config('permission.column_names.team_foreign_key') => getPermissionsTeamId()]);
//                        })
                    ->preload()
                    ->searchable(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label(__(static::$name))                
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->label(__(static::$email)),
                Tables\Columns\IconColumn::make('email_verified_at')
                    ->boolean()
                    ->sortable()
                    ->searchable()
                    ->label(__(static::$email_verified_at)),
                Tables\Columns\TextColumn::make('roles.name')
                    ->label(__(static::$roles))  
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make()->iconButton()->tooltip(trans('filament-users::user.resource.title.show')),
                Tables\Actions\EditAction::make()->iconButton()->tooltip(trans('filament-users::user.resource.title.edit')),
                Tables\Actions\DeleteAction::make()->iconButton()->tooltip(trans('filament-users::user.resource.title.delete'))
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
