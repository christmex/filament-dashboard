<?php

namespace App\Models;

use App\Helpers\Helper;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Assessment extends Model
{
    use HasFactory;
    protected $guarded = [];

    /**
     * The "booted" method of the model.
     */
    protected static function booted(): void
    {
        static::addGlobalScope('teacherSubject', function (Builder $builder) {
            if(auth()->id()){     
                $builder->where('user_id',auth()->id());
            }
        });
    }

    public function setTopicNameAttribute($value)
    {
        $this->attributes['topic_name'] = ucwords($value);
    }

    public function user() :BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    
    public function classroom() :BelongsTo
    {
        return $this->belongsTo(Classroom::class);
    }
    public function company() :BelongsTo
    {
        return $this->belongsTo(Company::class);
    }
    public function subject() :BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }
    

    public function assessmentMethodSetting(): BelongsTo
    {
        return $this->belongsTo(AssessmentMethodSetting::class);
    }
    public function topicSetting(): BelongsTo
    {
        return $this->belongsTo(TopicSetting::class);
    }
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

}
