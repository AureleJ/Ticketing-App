<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Project extends Model
{
    protected $fillable = [
        'name',
        'description',
        'progress',
        'budget_h',
        'total_h',
        'status',
        'client_id',
        'owner_id',
    ];

    public function getProgressClassAttribute(): string
    {
        return match (true) {
            $this->progress <= 24 => 'linear-gradient(90deg, #f87171, #ef4444)',
            $this->progress <= 49 => 'linear-gradient(90deg, #fbbf24, #f59e0b)',
            $this->progress <= 74 => 'linear-gradient(90deg, #facc15, #eab308)',
            default => 'linear-gradient(90deg, #4ade80, #22c55e)',
        };
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'planning' => 'Planification',
            'in_progress' => 'En cours',
            'on_hold' => 'En attente',
            'completed' => 'Terminé',
            default => ucfirst(str_replace('_', ' ', (string) $this->status)),
        };
    }

    public function getStatusClassAttribute(): string
    {
        return match ($this->status) {
            'planning' => 'badge-purple',
            'in_progress' => 'badge-blue',
            'on_hold' => 'badge-yellow',
            'completed' => 'badge-green',
            default => 'badge-outline',
        };
    }

    public function getTeam()
    {
        return $this->users()->get();
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class, 'client_id');
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'project_user')
            ->withPivot('role')
            ->withTimestamps();
    }

    public function members(): BelongsToMany
    {
        return $this->users()->wherePivot('role', 'member');
    }

    public function clientContacts(): BelongsToMany
    {
        return $this->users()->wherePivot('role', 'client');
    }

    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class, 'project_id');
    }
}