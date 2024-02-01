<?php

namespace App\Models;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class Student extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [];
    
    protected function name(): Attribute
    {
        return Attribute::make(
            get: fn (string $value) => Str::title($value),
            set: fn (string $value) => Str::title($value),
        );
    }

    
    /**
     * Scope a query to only include popular users.
     */
    public function scopeOwnStudent(Builder $query): void
    {
        $companyIds = [];
        $classroomIds = [];
        $schoolYears = [];
        $schoolTerms = [];
        $amainTeacher = MainTeacher::whereIn('id',auth()->user()->mainTeachers->pluck('id')->toArray())->get();
        foreach ($amainTeacher as $key => $value) {
            array_push($companyIds,$value->company_id);
            array_push($classroomIds,$value->classroom_id);
            array_push($schoolYears,$value->school_year);
            array_push($schoolTerms,$value->school_term);
        }
        $studentIds = StudentClassroom::query()
            ->whereIn('company_id',$companyIds)
            ->whereIn('classroom_id',$classroomIds)
            ->whereIn('school_year',$schoolYears)
            ->whereIn('school_term',$schoolTerms)
            ->get()
            ->pluck('student_id')
            ->toArray();
        $query->whereIn('id',$studentIds);
    }

    public function company() :BelongsTo{
        return $this->belongsTo(Company::class);
    }
    public function classroom() :BelongsTo{
        return $this->belongsTo(Classroom::class);
    }
    public function classrooms() :HasMany{
        return $this->hasMany(StudentClassroom::class);
    }
    
}
