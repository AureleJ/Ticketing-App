<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Ticket extends Model
{
    protected $fillable = [
        'project_id',
        'assigned_id',
        'title',
        'description',
        'status',
        'priority',
        'type',
    ];

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'open' => 'Ouvert',
            'in_progress' => 'En cours',
            'pending' => 'En attente',
            'closed' => 'Terminé',
            default => ucfirst(str_replace('_', ' ', (string) $this->status)),
        };
    }

    public function getStatusClassAttribute(): string
    {
        return match ($this->status) {
            'open' => 'badge-green',
            'in_progress' => 'badge-blue',
            'pending' => 'badge-yellow',
            'closed' => 'badge-outline',
            default => 'badge-outline',
        };
    }

    public function getPriorityLabelAttribute(): string
    {
        return match ($this->priority) {
            'high' => 'Haute',
            'medium' => 'Moyenne',
            'low' => 'Basse',
            default => ucfirst((string) $this->priority),
        };
    }

    public function getPriorityClassAttribute(): string
    {
        return match ($this->priority) {
            'high' => 'text-danger',
            'medium' => 'text-warning',
            'low' => '',
            default => '',
        };
    }

    public function getTypeLabelAttribute(): string
    {
        return match ($this->type) {
            'facturable' => 'Facturable',
            'non_facturable' => 'Non facturable',
            default => ucfirst(str_replace('_', ' ', (string) $this->type)),
        };
    }

    public function getTypeClassAttribute(): string
    {
        return match ($this->type) {
            'facturable' => 'badge-green',
            'non_facturable' => 'badge-red',
            default => 'badge-outline',
        };
    }

    public function getClientAttribute(): ?Client
    {
        return $this->project?->client;
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class, 'project_id');
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_id');
    }
}