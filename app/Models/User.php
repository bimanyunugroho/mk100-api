<?php

namespace App\Models;

use App\Enums\RoleUserEnum;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;


class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, HasApiTokens, HasRoles, HasUlids;

    protected string $guard_name = 'sanctum';

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'company_name',
        'phone'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'role' => RoleUserEnum::class
        ];
    }

    public function isEmployer(): bool
    {
        return $this->role === RoleUserEnum::EMPLOYER;
    }

    public function isFreelancer(): bool
    {
        return $this->role === RoleUserEnum::FREELANCER;
    }

    public function jobPostings(): HasMany
    {
        return $this->hasMany(Job::class, 'employer_id');
    }

    public function applications(): HasMany
    {
        return $this->hasMany(JobApplication::class, 'freelancer_id');
    }
}
