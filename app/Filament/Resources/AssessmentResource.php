<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AssessmentResource\Pages;
use App\Filament\Resources\AssessmentResource\RelationManagers;
use App\Helpers\Helper;
use App\Models\Assessment;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class AssessmentResource extends Resource
{
    protected static ?string $model = Assessment::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('user_id')
                    ->relationship('user', 'name')
                    ->required(),
                Forms\Components\Select::make('company_id')
                    ->relationship('company', 'name')
                    ->required(),
                Forms\Components\Select::make('classroom_id')
                    ->relationship('classroom', 'name')
                    ->required(),
                Forms\Components\TextInput::make('subject_id')
                    ->required()
                    ->numeric(),
                Forms\Components\Select::make('student_id')
                    ->relationship('student', 'name')
                    ->required(),
                Forms\Components\Select::make('assessment_method_setting_id')
                    ->relationship('assessmentMethodSetting', 'name')
                    ->required(),
                Forms\Components\Select::make('topic_setting_id')
                    ->relationship('topicSetting', 'name')
                    ->required(),
                Forms\Components\Toggle::make('school_year')
                    ->required(),
                Forms\Components\Toggle::make('school_term')
                    ->required(),
                Forms\Components\TextInput::make('grading')
                    ->numeric(),
                Forms\Components\TextInput::make('topic_name')
                    ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->sortable(),
                Tables\Columns\TextColumn::make('company.name')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->sortable(),
                Tables\Columns\TextColumn::make('classroom.name')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->sortable(),
                Tables\Columns\TextColumn::make('student.name')
                    ->searchable(isIndividual:true, isGlobal:false)
                    ->sortable(),
                Tables\Columns\TextColumn::make('subject.name')
                    ->toggleable()
                    ->searchable(isIndividual:true, isGlobal:false)
                    ->sortable(),
                Tables\Columns\TextColumn::make('assessmentMethodSetting.name')
                    ->label('Assessment Method')
                    ->toggleable()
                    ->searchable(isIndividual:true, isGlobal:false)
                    ->sortable(),
                Tables\Columns\TextColumn::make('topicSetting.name')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->sortable(),
                Tables\Columns\TextColumn::make('school_year')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->formatStateUsing(fn (string $state): string => Helper::getTermById($state)),
                Tables\Columns\TextColumn::make('school_term')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->formatStateUsing(fn (string $state): string => Helper::getSchoolYearById($state)),
                Tables\Columns\TextColumn::make('topic_name')
                    ->searchable(isIndividual:true, isGlobal:false),
                Tables\Columns\TextColumn::make('grading')
                    ->searchable(isIndividual:true, isGlobal:false)
                    ->numeric()
                    ->sortable(),
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
                // Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()->label(''),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageAssessments::route('/'),
        ];
    }
}
