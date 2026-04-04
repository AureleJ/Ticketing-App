<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Client extends Model
{
    protected $fillable = [
        'company',
        'name',
        'email',
        'phone',
        'status',
        'avatar_color',
    ];

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'active' => 'Actif',
            'inactive' => 'Inactif',
            'prospect' => 'Prospect',
            default => ucfirst(str_replace('_', ' ', (string) $this->status)),
        };
    }

    public function getStatusClassAttribute(): string
    {
        return match ($this->status) {
            'active' => 'badge-green',
            'inactive' => 'badge-red',
            'prospect' => 'badge-yellow',
            default => 'badge-outline',
        };
    }

    public function getInitials(): string
    {
        return strtoupper(mb_substr($this->company, 0, 2));
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'client_id');
    }

    public function projects(): HasMany
    {
        return $this->hasMany(Project::class, 'client_id');
    }
}