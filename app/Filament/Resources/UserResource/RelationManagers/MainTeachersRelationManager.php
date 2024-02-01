<?php

namespace App\Filament\Resources\UserResource\RelationManagers;

use Filament\Forms;
use Filament\Tables;
use App\Helpers\Helper;
use Filament\Forms\Get;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Forms\Components\Select;
use Illuminate\Validation\Rules\Unique;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\ClassroomResource;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Resources\RelationManagers\RelationManager;

class MainTeachersRelationManager extends RelationManager
{
    protected static string $relationship = 'mainTeachers';

    public function form(Form $form): Form
    {
        return $form
            ->columns(3)
            ->schema([
                Select::make('classroom_id')
                    ->label('Classroom')
                    ->required()
                    ->relationship('classroom','name')
                    ->unique(modifyRuleUsing: function (Unique $rule,$state, Get $get) {
                        return $rule
                                ->where('school_term', $get('school_term'))
                                ->where('school_year', $get('school_year'))
                                // ->where('classroom_id',$state)
                                ;
                    }, ignoreRecord:true)
                    ->createOptionForm(ClassroomResource::getForm()),
                Select::make('school_year')
                    ->required()
                    ->live(onBlur: true)
                    ->options(fn()=>Helper::getSchoolYears()),
                Select::make('school_term')
                    ->required()
                    ->live(onBlur: true)
                    ->options(fn()=>Helper::getTerms())
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('classroom.name'),
                Tables\Columns\TextColumn::make('school_year')
                    ->formatStateUsing(fn (string $state): string =>  Helper::getSchoolYearById($state)),
                Tables\Columns\TextColumn::make('school_term')
                    ->formatStateUsing(fn (string $state): string =>  Helper::getTermById($state)),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
