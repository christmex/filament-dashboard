<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Helpers\Helper;
use App\Models\Student;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Split;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\StudentResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\StudentResource\RelationManagers;

class StudentResource extends Resource
{
    protected static ?string $model = Student::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form->schema(self::getForm());
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('nis')
                    ->searchable(),
                Tables\Columns\TextColumn::make('nisn')
                    ->searchable(),
                Tables\Columns\TextColumn::make('born_place')
                    ->searchable(),
                Tables\Columns\TextColumn::make('born_date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('company.name')
                    ->label('Current School')
                    ->searchable(),
                Tables\Columns\TextColumn::make('sex')
                    ->formatStateUsing(fn (string $state): string => Helper::getGenderById($state))
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('religion')
                    ->formatStateUsing(fn (string $state): string => Helper::getReligionById($state))
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('status_in_family')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('sibling_order_in_family')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('address')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('phone')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('previous_education')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('father_name')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('mother_name')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('parent_address')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('parent_phone')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('father_job')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('mother_job')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('guardian_name')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('guardian_address')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('guardian_phone')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('guardian_job')
                    ->searchable()
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
                Tables\Filters\TrashedFilter::make(),
                SelectFilter::make('company_id')
                    ->label('Current School')
                    ->searchable()
                    ->multiple()
                    ->preload()
                    ->relationship('company', 'name')
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make(),
                    Tables\Actions\ForceDeleteAction::make(),
                    Tables\Actions\RestoreAction::make(),
                ])
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                ]),
            ])
            ->emptyStateActions([
                Tables\Actions\CreateAction::make(),
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
            'index' => Pages\ListStudents::route('/'),
            'create' => Pages\CreateStudent::route('/create'),
            'edit' => Pages\EditStudent::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }

    public static function getForm(): array 
    {
        return [
            Split::make([
                Tabs::make()
                    ->tabs([
                        Tabs\Tab::make('Student Details')
                            ->columns(2)
                            ->schema([
                                Forms\Components\TextInput::make('name')
                                    ->required()
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('nis')
                                    ->unique(ignoreRecord: true)
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('nisn')
                                    ->unique(ignoreRecord: true)
                                    ->maxLength(255),
                    
                                Forms\Components\TextInput::make('born_place')->maxLength(255),
                                Forms\Components\DatePicker::make('born_date'),
                                Forms\Components\Select::make('sex')
                                    ->options(fn()=>Helper::getGenders()),
                                Forms\Components\Select::make('religion')
                                    ->options(fn()=>Helper::getReligions()),
                                Forms\Components\TextInput::make('status_in_family')->maxLength(255)->placeholder('Ex: Anak'),
                                Forms\Components\TextInput::make('sibling_order_in_family')->integer()->minValue(1)->placeholder('Ex: 1'),
                                Forms\Components\TextInput::make('phone')->tel(),
                                Forms\Components\Textarea::make('address'),
                                
                            ]),
                        Tabs\Tab::make('Parent Details')
                            ->columns(2)
                            ->schema([
                                Forms\Components\TextInput::make('father_name')->maxLength(255),
                                Forms\Components\TextInput::make('father_job')->maxLength(255),
                                Forms\Components\TextInput::make('mother_job')->maxLength(255),
                                Forms\Components\TextInput::make('mother_name')->maxLength(255),
                                Forms\Components\TextInput::make('parent_phone')->tel(),
                                Forms\Components\Textarea::make('parent_address'),
                            ]),
                        Tabs\Tab::make('Guardian Details')
                            ->columns(2)
                            ->schema([
                                Forms\Components\TextInput::make('guardian_name')->maxLength(255),
                                Forms\Components\TextInput::make('guardian_phone')->tel(),
                                Forms\Components\TextInput::make('guardian_job')->maxLength(255),
                                Forms\Components\Textarea::make('guardian_address'),
                            ]),
                    ])
                    ->columnSpanFull()
                    ->persistTabInQueryString(),
                Section::make('School Details')
                ->schema([
                    Select::make('company_id')
                        ->label('Current School')
                        ->unique(ignoreRecord: true)
                        ->relationship('company','name'),
                    Forms\Components\TextInput::make('previous_education')->maxLength(255),
                    Forms\Components\TextInput::make('joined_at_class')->maxLength(255),
                    Forms\Components\DatePicker::make('joined_at'),
                ])->grow(false),
            ])->from('md')
            ->columnSpanFull(),

            
        ];
    }
}
