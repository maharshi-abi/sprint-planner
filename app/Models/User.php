<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function sprints(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Sprint::class);
    }

    public function workSessions(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(WorkSession::class);
    }

    public function dailySummaries(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(DailySummary::class);
    }
}
