<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

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

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class);
    }

    public function hasRole(string $slug): bool
    {
        return $this->roles()->where('slug', $slug)->exists();
    }

    public function isAdmin(): bool
    {
        return $this->hasRole('admin');
    }

    public function isChiefPmd(): bool
    {
        return $this->hasRole('chief-pmd');
    }

    public function isPmdDivision(): bool
    {
        return $this->hasRole('pmd-division');
    }

    public function isOtherDivision(): bool
    {
        return $this->hasRole('other-division');
    }

    public function isPenro(): bool
    {
        return $this->hasRole('penro');
    }

    public function canManageUsers(): bool
    {
        return $this->isAdmin();
    }

    public function canApproveDisapprove(): bool
    {
        return $this->isAdmin() || $this->isChiefPmd() || $this->isPmdDivision();
    }

    public function canAssignDivision(): bool
    {
        return $this->isAdmin() || $this->isChiefPmd() || $this->isPmdDivision() || $this->isOtherDivision();
    }

    public function canAssignPenro(): bool
    {
        return $this->isAdmin() || $this->isChiefPmd() || $this->isOtherDivision();
    }
}
