<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected $fillable = [
        'type',
        'firstname',
        'lastname',
        'username',
        'email',
        'password',
        'role',
        'avatar_color',
        'client_id',
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
        ];
    }

    public function getFullName(): string
    {
        return trim($this->firstname . ' ' . $this->lastname);
    }

    public function getInitials(): string
    {
        return strtoupper(mb_substr($this->firstname, 0, 1) . mb_substr($this->lastname, 0, 1));
    }

    public function getAvatarColor(): string
    {
        if (!empty($this->avatar_color)) {
            return $this->avatar_color;
        }
        $colors = ['blue', 'yellow', 'green', 'red', 'purple', 'cyan'];
        return $colors[$this->id % count($colors)];
    }

    public function isAdmin(): bool
    {
        return $this->type === 'admin';
    }
    public function isMember(): bool
    {
        return $this->type === 'member';
    }
    public function isClient(): bool
    {
        return $this->type === 'client';
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class, 'client_id');
    }

    public function projects(): BelongsToMany
    {
        return $this->belongsToMany(Project::class, 'project_user')
            ->withPivot('role')
            ->withTimestamps();
    }

    public function memberProjects(): BelongsToMany
    {
        return $this->projects()->wherePivot('role', 'member');
    }

    public function clientProjects(): BelongsToMany
    {
        return $this->projects()->wherePivot('role', 'client');
    }

    public function ownedProjects(): HasMany
    {
        return $this->hasMany(Project::class, 'owner_id');
    }

    public function assignedTickets(): HasMany
    {
        return $this->hasMany(Ticket::class, 'assigned_id');
    }
}