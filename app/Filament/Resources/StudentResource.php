<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Helpers\Helper;
use App\Models\Company;
use App\Models\Student;
use Filament\Forms\Get;
use Filament\Forms\Form;
use App\Models\Classroom;
use Filament\Tables\Table;
use App\Models\MainTeacher;
use App\Models\StudentClassroom;
use Filament\Resources\Resource;
use Illuminate\Support\Facades\DB;
use Filament\Forms\Components\Tabs;
use Filament\Tables\Filters\Filter;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Split;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Rules\Unique;
use Filament\Notifications\Notification;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\ToggleButtons;
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
                Tables\Columns\TextColumn::make('classroom.name')
                    ->label('Current Classroom')
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
                    ->visible(fn()=> !auth()->user()->mainTeachers->count())
                    ->searchable()
                    ->multiple()
                    ->preload()
                    ->relationship('company', 'name'),
                SelectFilter::make('classroom_id')
                    ->label('Current Classroom')
                    ->visible(fn()=> !auth()->user()->mainTeachers->count())
                    ->searchable()
                    ->multiple()
                    ->preload()
                    ->relationship('classroom', 'name'),
                Filter::make('mainTeacher')
                    ->visible(fn()=> auth()->user()->mainTeachers->count())
                    // ->columnSpanFull()
                    ->form([
                        Select::make('class_of')
                            ->options(function(){
                                return auth()->user()->mainTeachers->pluck('full_label','id')->toArray();
                            }),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        if($data['class_of'] == null){
                            $data['class_of'] = auth()->user()->mainTeachers->first()->id;
                        }
                        $studentIds = Helper::getStudentIdsByMainTeacherId($data['class_of']);
                        return $query->whereIn('id',array_unique($studentIds));
                    })
            ], layout: FiltersLayout::Modal)
            // ->filtersFormColumns(3)
            ->deferFilters()
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make(),
                    Tables\Actions\ForceDeleteAction::make(),
                    Tables\Actions\RestoreAction::make(),
                ])
            ])
            ->bulkActions([
                Tables\Actions\BulkAction::make('classrooms')
                    ->label('Manage Classroom')
                    ->form([
                        Group::make([
                            Select::make('company_id')
                                ->label('School')
                                ->required()
                                ->options(fn()=>Company::all()->pluck('name','id')->toArray()),
                            ToggleButtons::make('set_current_school')
                                ->label('Set this school as current school?')
                                ->boolean()
                                ->default(true)
                                ->grouped(),
                            Select::make('classroom_id')
                                ->label('Classroom')
                                ->required()
                                ->options(fn()=>Classroom::all()->pluck('name','id')->toArray()),
                            ToggleButtons::make('set_current_classroom')
                                ->label('Set this classroom as current classroom?')
                                ->boolean()
                                ->default(true)
                                ->grouped(),
                            Select::make('school_year')
                                ->required()
                                ->options(fn()=>Helper::getSchoolYears()),
                            Select::make('school_term')
                                ->required()
                                ->default(1)
                                ->options(fn()=>Helper::getTerms()),
                        ])
                        ->columns(2)
                    ])
                    ->action(function(array $data,$livewire){
                        DB::beginTransaction();
                        try {
                            $studentIds = $livewire->selectedTableRecords; #save student id so we an use it to update current_school/company in student table
                            $getMainTeacher = MainTeacher::query()
                            ->where('company_id',$data['company_id'])
                            ->where('classroom_id',$data['classroom_id'])
                            ->where('school_year',$data['school_year'])
                            ->where('school_term',$data['school_term'])
                            ->first()
                            ;
                            if($getMainTeacher == null){
                                throw new \Exception('There is no Main Teacher for this selected data.');
                            }

                            foreach ($livewire->getSelectedTableRecords() as $record) {
                                StudentClassroom::updateOrCreate(
                                    [
                                        'student_id'    => $record->id,
                                        'school_year'   => $data['school_year'],
                                        'school_term'   => $data['school_term'],
                                    ],
                                    [
                                        'company_id'    => $data['company_id'],
                                        'classroom_id'  => $data['classroom_id'],
                                        'user_id'       => $getMainTeacher->user_id,
                                    ]
                                );
                            }
                            // update current_company/school in student tabel
                            $update = [];
                            if($data['set_current_school']){
                                $update['company_id'] = $data['company_id'];
                            }
                            if($data['set_current_classroom']){
                                $update['classroom_id'] = $data['classroom_id'];
                            }
                            Student::whereIn('id',array_unique($studentIds))->update($update);

                            DB::commit();

                            Notification::make()
                                ->success()
                                ->title('Successfully managed student classroom')
                                ->send();
                        } catch (\Throwable $th) {
                            DB::rollback();
                            Notification::make()
                                ->danger()
                                ->title($th->getMessage())
                                ->send();
                        }
                    })
                    ->deselectRecordsAfterCompletion(),
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
            ->ownStudent()
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
                        ->disabledOn('edit')
                        ->helperText(function(string $operation){
                            if($operation != 'create'){
                                return 'Edit this value via manage classroom';
                            }
                        })
                        ->label('Current School')
                        ->relationship('company','name'),
                    Select::make('classroom_id')
                        ->disabledOn('edit')
                        ->helperText(function(string $operation){
                            if($operation != 'create'){
                                return 'Edit this value via manage classroom';
                            }
                        })
                        ->label('Current Classroom')
                        ->relationship('classroom','name'),
                    Forms\Components\TextInput::make('previous_education')->maxLength(255),
                    Forms\Components\TextInput::make('joined_at_class')->maxLength(255),
                    Forms\Components\DatePicker::make('joined_at'),
                ])->grow(false),
            ])->from('md')
            ->columnSpanFull(),

            
        ];
    }
}
