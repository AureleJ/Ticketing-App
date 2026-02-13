<?php
require_once __DIR__ . '/FormService.php';

class ClientForm extends FormService
{
    public function formatData(): array
    {
        $colors = ['blue', 'green', 'yellow', 'red', 'purple', 'cyan'];
        $randomColor = $colors[array_rand($colors)];

        return [
            "company"      => $this->input("entreprise", "Nouvelle Entreprise"),
            "contact_name" => $this->input("contact", "Contact Inconnu"),
            "email"        => $this->input("mail", ""),
            "phone"        => $this->input("phone", ""),
            "status"       => "Active", 
            "avatarColor"  => $randomColor
        ];
    }
}