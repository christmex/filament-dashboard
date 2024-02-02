<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TopicSetting extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [];

    protected static function booted(): void
    {
        static::addGlobalScope('sortOrder', function (Builder $builder) {
            $builder->orderBy('order');
        });
    }


    public function setNameAttribute($value)
    {
        $this->attributes['name'] = ucwords($value);
    }
}
