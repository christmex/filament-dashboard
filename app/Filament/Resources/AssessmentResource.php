<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Helpers\Helper;
use Filament\Forms\Form;
use App\Models\Assessment;
use Filament\Tables\Table;
use App\Models\TeacherSubject;
use Filament\Resources\Resource;
use Filament\Tables\Filters\Filter;
use Filament\Forms\Components\Select;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use App\Tables\Columns\GradingTextInputColumn;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\AssessmentResource\Pages;
use App\Filament\Resources\AssessmentResource\RelationManagers;

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
                GradingTextInputColumn::make('grading')
                    ->type('number')
                    ->searchable(isIndividual:true, isGlobal:false)
                    ->rules(['nullable','integer','min:0', 'max:100'])
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
                Filter::make('subject')
                    ->visible(fn()=> auth()->user()->teacherSubjects->count())
                    ->form([
                        Select::make('teacher_subject_id')
                            ->label('Subject')
                            ->options(function(){
                                return auth()->user()->teacherSubjects->pluck('full_label','id')->toArray();
                            })
                            ->searchable(),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        // $studentIds = Helper::getStudentIdsFromStudentClassroom($data['teacher_subject_id'], TeacherSubject::class);
                        if($data['teacher_subject_id'] == null){
                            return $query->where('user_id',auth()->id());
                        }
                        $data = TeacherSubject::find($data['teacher_subject_id']);

                        return $query
                            ->where('user_id',$data->user_id)
                            ->where('company_id',$data->company_id)
                            ->where('classroom_id',$data->classroom_id)
                            ->where('subject_id',$data->subject_id)
                            ->where('school_year',$data->school_year)
                            ->where('school_term',$data->school_term)
                        ;
                    })->columnSpanFull(),
                SelectFilter::make('topic_setting')
                    ->preload()
                    ->visible(fn()=> auth()->user()->teacherSubjects->count())
                    ->relationship('topicSetting', 'name'),
                SelectFilter::make('assessmentMethodSetting')
                    ->preload()
                    ->visible(fn()=> auth()->user()->teacherSubjects->count())
                    ->relationship('assessmentMethodSetting', 'name'),
                
            ], layout: FiltersLayout::Modal)
            ->filtersFormColumns(2)
            ->deferFilters()
            ->actions([
                // Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()->label(''),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->paginated([10,20,50])
            ->defaultPaginationPageOption(10);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageAssessments::route('/'),
        ];
    }

}
