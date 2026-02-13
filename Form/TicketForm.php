<?php
require_once __DIR__ . '/FormService.php';

class TicketForm extends FormService
{
    public function formatData(): array
    {
        return [
            "subject"     => $this->input("subject", "Nouveau Ticket"),
            "description" => $this->input("description", ""),
            "project_id"  => $this->inputInt("project_id"),
            "client_id"   => $this->inputInt("client_id"),
            "assigned_id" => $this->inputInt("assigned_id"),
            "date"        => date('Y-m-d H:i:s'),
            "status"      => "Non traité", 
            "priority"    => $this->input("priority", "low"),
            "type"        => $this->input("type", "Inclus")
        ];
    }
}