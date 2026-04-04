<?php
require_once __DIR__ . '/Entity.php';

class ClientEntity extends Entity 
{
    public int $id;
    public string $company;      
    public string $contact_name; 
    public string $email;
    public string $phone;
    public string $status;       
    public string $avatar_color;   

    public function __construct(array $data)
    {
        $this->id = (int) ($data['id'] ?? 0);
        $this->company = $data['company'] ?? '';
        $this->contact_name = $data['contact_name'] ?? '';
        $this->email = $data['email'] ?? '';
        $this->phone = $data['phone'] ?? '';
        $this->status = $data['status'] ?? 'Active';
        $this->avatar_color = $data['avatar_color'] ?? 'blue';
    }

    public function getInitials(): string
    {
        return strtoupper(substr($this->company, 0, 1));
    }

    public function getAvatarColor(): string
    {
        if (!empty($this->avatar_color)) {
            return $this->avatar_color;
        }

        $colors = ['blue', 'yellow', 'green', 'red', 'purple', 'cyan'];

        $index = $this->id % count($colors);

        return $colors[$index];
    }
}