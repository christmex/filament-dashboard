<?php

namespace App\Models;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Company extends Model
{
    use HasFactory;

    protected $guarded = [];
    
    protected function name(): Attribute
    {
        return Attribute::make(
            get: fn (string $value) => Str::title($value),
            set: fn (string $value) => Str::title($value),
        );
    }

}
