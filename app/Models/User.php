<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Filament\Panel;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Notifications\Notifiable;
use Filament\Models\Contracts\FilamentUser;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use BezhanSalleh\FilamentShield\Traits\HasPanelShield;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable implements FilamentUser
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles, HasPanelShield, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'company_id',
        'email',
        'password',
        'notes',
        'read_employee_terms_date',
        'kemnaker_join_date',
        'jht_join_date',
        'bpjs_join_date',
        'join_date',
        'finish_contract',
        'permanent_date',
        'citizenship_number',
        'born_date',
        'born_place',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];


    public static $domain = '@sekolahbasic.sch.id';

    public function canAccessPanel(Panel $panel): bool
    {
        return str_ends_with($this->email, $this->domain);
    }

    public function company() :BelongsTo{
        return $this->belongsTo(Company::class);
    }

    /**
     * Get the comments for the blog post.
     */
    public function mainTeachers(): HasMany
    {
        return $this->hasMany(MainTeacher::class);
    }
}
