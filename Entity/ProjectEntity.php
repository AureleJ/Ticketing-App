<?php
require_once __DIR__ . '/Entity.php';

class ProjectEntity extends Entity 
{
    public int $id;
    public string $name;
    public string $description;
    public string $client;
    public int $client_id;
    public int $progress;
    public int $budget_h;
    public int $total_h;
    public string $status;
    public string $date;
    public string $owner;
    public int $owner_id;
    public string $priority;
    public array $team;

    public function __construct(array $data)
    {
        $this->id = (int) ($data['id'] ?? 0);
        $this->name = $data['name'] ?? '';
        $this->description = $data['description'] ?? '';
        $this->client = $data['client'] ?? '';
        $this->client_id = (int) ($data['client_id'] ?? 0);
        $this->progress = (int) ($data['progress'] ?? 0);
        $this->budget_h = (int) ($data['budget_h'] ?? 0);
        $this->total_h = (int) ($data['total_h'] ?? 0);
        $this->status = $data['status'] ?? 'En attente';
        $this->date = $data['date'] ?? date('Y-m-d');
        $this->owner = $data['owner'] ?? '';
        $this->owner_id = (int) ($data['owner_id'] ?? 0);
        $this->priority = $data['priority'] ?? 'low';
        $this->team = $data["team"] ?? [];
    }
}