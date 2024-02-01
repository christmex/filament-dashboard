<?php

namespace App\Filament\Resources\UserResource\RelationManagers;

use Filament\Forms;
use Filament\Tables;
use App\Helpers\Helper;
use Filament\Forms\Get;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Spatie\Permission\Models\Role;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Rules\Unique;
use App\Filament\Resources\UserResource;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\CompanyResource;
use App\Filament\Resources\ClassroomResource;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Resources\RelationManagers\RelationManager;

class MainTeachersRelationManager extends RelationManager
{
    protected static string $relationship = 'mainTeachers';

    protected static ?string $title = 'Main Teacher';

    public static function canViewForRecord(Model $ownerRecord, string $pageClass) : bool
    {
        return $ownerRecord->hasRole(Helper::$userDependOnRoleMainTeacher) ||$ownerRecord->mainTeachers->count();
    }

    public function isReadOnly(): bool
    {
        $ownerRecord = $this->getOwnerRecord();
        // dd($ownerRecord->hasRole(Helper::$userDependOnRoleMainTeacher));
        return !$ownerRecord->hasRole(Helper::$userDependOnRoleMainTeacher);
    }


    public function form(Form $form): Form
    {
        return $form
            // ->disabled(function(Get $get){
            //     return true;
            // })
            ->columns(3)
            ->schema([
                Select::make('company_id')
                    ->label('School')
                    ->required()
                    ->relationship('company','name')
                    ->unique(modifyRuleUsing: function (Unique $rule,$state, Get $get) {
                        return $rule
                                // ->where('school_term', $get('school_term'))
                                ->where('school_year', $get('school_year'))
                                ->where('classroom_id', $get('classroom_id'))
                                ;
                    }, ignoreRecord:true)
                    ->createOptionForm(CompanyResource::getForm()),
                Select::make('classroom_id')
                    ->label('Classroom')
                    ->required()
                    ->relationship('classroom','name')
                    ->unique(modifyRuleUsing: function (Unique $rule,$state, Get $get) {
                        return $rule
                                // ->where('school_term', $get('school_term'))
                                ->where('school_year', $get('school_year'))
                                ->where('company_id', $get('company_id'))
                                // ->where('classroom_id',$state)
                                ;
                    }, ignoreRecord:true)
                    ->createOptionForm(ClassroomResource::getForm()),
                Select::make('school_year')
                    ->required()
                    ->live(onBlur: true)
                    ->options(fn()=>Helper::getSchoolYears()),
                Hidden::make('school_term')->default(1)
                // Select::make('school_term')
                //     ->hidden(true)
                //     ->default(1)
                //     ->required()
                //     ->live(onBlur: true)
                //     ->options(fn()=>Helper::getTerms())
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('company.name'),
                Tables\Columns\TextColumn::make('classroom.name'),
                Tables\Columns\TextColumn::make('school_year')
                    ->formatStateUsing(fn (string $state): string =>  Helper::getSchoolYearById($state)),
                Tables\Columns\TextColumn::make('school_term')
                    ->hidden()
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
