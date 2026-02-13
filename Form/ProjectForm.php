<?php
require_once __DIR__ . '/FormService.php';
require_once __DIR__ . '/../Service/AuthService.php'; 

class ProjectForm extends FormService
{
    public function formatData(): array
    {
        $currentUser = AuthService::getAuthUser();

        return [
            "name"        => $this->input("name", "Nouveau Projet"),
            "description" => $this->input("description", ""),
            "client_id"   => $this->inputInt("client_id"), 
            "progress"    => 0,
            "status"      => "En attente",
            "date"        => date('Y-m-d'),
            "budget_h"    => $this->inputInt("budget_h", 0),
            "total_h"     => $this->inputInt("total_h", 100), 
            "owner_id"    => $currentUser->id,
            "priority"    => $this->input("priority", "Medium"),
            "team"        => [] 
        ];
    }
}