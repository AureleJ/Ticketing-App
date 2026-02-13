<?php
require_once __DIR__ . '/Entity.php';

class TicketEntity extends Entity
{
    public int $id;
    public int $project_id;
    public string $subject;
    public int $client_id;
    public int $assigned_id;
    public string $date;
    public string $status;
    public string $priority;
    public string $type;
    public string $description;

    public function __construct(array $data)
    {
        $this->project_id = (int) ($data['project_id'] ?? 0);
        $this->id = (int) ($data['id'] ?? 0);
        $this->subject = $data['subject'] ?? '';
        $this->client_id = (int) ($data['client_id'] ?? 0);
        $this->assigned_id = (int) ($data['assigned_id'] ?? 0);
        $this->date = $data['date'] ?? '';
        $this->status = $data['status'] ?? 'Non traité';
        $this->priority = $data['priority'] ?? 'low';
        $this->type = $data['type'] ?? 'Inclus';
        $this->description = $data['description'] ?? '';
    }
}