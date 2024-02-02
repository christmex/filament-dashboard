<?php

namespace App\Filament\Clusters\Settings\Resources;

use App\Filament\Clusters\Settings;
use App\Filament\Clusters\Settings\Resources\SubjectResource\Pages;
use App\Filament\Clusters\Settings\Resources\SubjectResource\RelationManagers;
use App\Models\Subject;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class SubjectResource extends Resource
{
    protected static ?string $model = Subject::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $cluster = Settings::class;

    protected static ?string $navigationGroup = 'Raport Merdeka';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('subject_group_id')
                    ->relationship('subjectGroup', 'name')
                    ->createOptionForm([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('order')
                            ->minValue(1)
                            ->required()
                            ->default(1)
                            ->numeric(),
                    ])
                    ->editOptionForm([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('order')
                            ->minValue(1)
                            ->required()
                            ->default(1)
                            ->numeric(),
                    ]),
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                // Forms\Components\TextInput::make('code')
                //     ->maxLength(255),
                Forms\Components\TextInput::make('order')
                    
                    ->minValue(1)
                    ->required()
                    ->default(1)
                    ->numeric(),
                Forms\Components\Toggle::make('is_curiculum_basic')
                    ->onIcon('heroicon-s-bolt')
                    ->offIcon('heroicon-o-bolt')
                    ->inline(false)
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('subjectGroup.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                // Tables\Columns\TextColumn::make('code')
                //     ->searchable(),
                // Tables\Columns\IconColumn::make('is_curiculum_basic')
                //     ->label('Curicullum Basic')
                //     ->boolean(),
                Tables\Columns\ToggleColumn::make('is_curiculum_basic')
                    ->label('Curicullum Basic'),
                Tables\Columns\TextInputColumn::make('order')
                    ->rules(['required','integer','min:1'])
                    ->sortable(),
                Tables\Columns\TextColumn::make('deleted_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->defaultSort('order', 'asc')
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageSubjects::route('/'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
