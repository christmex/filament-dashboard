<?php

namespace App\Models;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Casts\Attribute;
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

    public function company() :BelongsTo{
        return $this->belongsTo(Company::class);
    }
}
