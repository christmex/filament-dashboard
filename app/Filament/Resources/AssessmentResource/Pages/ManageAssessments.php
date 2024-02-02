<?php

namespace App\Filament\Resources\AssessmentResource\Pages;

use Filament\Actions;
use App\Helpers\Helper;
use App\Models\Student;
use Filament\Forms\Get;
use Filament\Forms\Set;
use App\Models\TeacherSubject;
use App\Models\StudentClassroom;
use Illuminate\Support\Facades\DB;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Forms\Components\CheckboxList;
use Filament\Resources\Pages\ManageRecords;
use App\Filament\Resources\AssessmentResource;

class ManageAssessments extends ManageRecords
{
    protected static string $resource = AssessmentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ActionGroup::make([
                Actions\Action::make('Single Class')
                ->form([
                    Group::make([
                        Select::make('teacher_subject_id')
                            ->label('subject')
                            ->options(function(){
                                return auth()->user()->teacherSubjects->pluck('full_label','id')->toArray();
                            })
                            ->required()
                            ->searchable()
                            ->live()
                            ->columnSpanFull()
                            ->selectablePlaceholder(false)
                            ,
                        TextInput::make('topic_name')
                            ->helperText('Format : (Assessment Method Setting Name) - (Topic Name) | Example: Penugasan 1 - Berhitung 1-10')
                            ->columnSpanFull()
                            ->required(),  
                        Select::make('topic_setting_id')
                            ->relationship('topicSetting','name')
                            ->required()
                            ->searchable()
                            ->helperText('Topic 1 also called Chapter 1 or bab 1, etc, they are all the same 🤩')
                            ->preload(),
                        Select::make('assessment_method_setting_id')
                            ->relationship('assessmentMethodSetting','name')
                            ->required()
                            ->searchable()
                            ->preload(),
                        
                        CheckboxList::make('student_id')
                            ->label('Students')
                            ->options(function(Get $get){
                                $studentIds = Helper::getStudentIdsFromStudentClassroom($get('teacher_subject_id'), TeacherSubject::class);
                                return Student::whereIn('id',$studentIds)->get()->pluck('name','id')->toArray();
                            })
                            ->searchable()
                            ->bulkToggleable()
                            ->columnSpanFull(),
                        
                    ])
                    ->columns(2)
                ])
                ->icon('heroicon-o-pencil')
                ->action(function (array $data): void {
                    
                    DB::beginTransaction();
                    try {

                        $dataArray = [];
                        $getCLassroomStudentIds = $data['student_id'];
                        if(!count($getCLassroomStudentIds)){
                            throw new \Exception('There is no student selected.');
                        }

                        $teacherSubjectData = auth()->user()->teacherSubjects->find($data['teacher_subject_id']);
                        for($i=0; $i < count($getCLassroomStudentIds); $i++) {
                            $dataArray[] = [
                                'user_id' => $teacherSubjectData->user_id,
                                'company_id' => $teacherSubjectData->company_id,
                                'classroom_id' => $teacherSubjectData->classroom_id,
                                'subject_id' => $teacherSubjectData->subject_id,
                                'school_year' => $teacherSubjectData->school_year,
                                'school_term' => $teacherSubjectData->school_term,
                                'student_id' => $getCLassroomStudentIds[$i],
                                'assessment_method_setting_id' => $data['assessment_method_setting_id'],
                                'topic_setting_id' => $data['topic_setting_id'],
                                'topic_name' => $data['topic_name'],
                            ];
                        }
                        if(DB::table('assessments')->insertOrIgnore($dataArray)){
                            DB::commit();
                            Notification::make()
                                ->success()
                                ->title('Successfully added student assessment')
                                ->send();
                        }

                        
                    } catch (\Throwable $th) {
                        DB::rollback();
                        Notification::make()
                            ->danger()
                            ->title($th->getMessage())
                            ->send();
                    }
                    
                })
                ,
            ])
            ->label('Create Assessment')
            ->icon('heroicon-o-pencil')
            // ->color('success')
            ->button(),
        ];
    }
}
