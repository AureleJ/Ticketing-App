<?php
require_once __DIR__ . '/Entity.php';

class UserEntity extends Entity
{
    public int $id;
    public string $firstname;
    public string $lastname;
    public string $email;
    public string $role;
    public string $status;
    public string $avatar_color;

    public function __construct(array $data)
    {
        $this->id = (int) ($data['id'] ?? 0);
        $this->firstname = $data['firstname'] ?? '';
        $this->lastname = $data['lastname'] ?? '';
        $this->email = $data['email'] ?? '';
        $this->role = $data['role'] ?? 'User';
        $this->status = $data['status'] ?? 'Active';
        $this->avatar_color = $data['avatar_color'] ?? 'blue';
    }

    public function getFullName(): string
    {
        return trim($this->firstname . ' ' . $this->lastname);
    }

    public function getInitials(): string
    {
        $f = strtoupper(substr($this->firstname, 0, 1));
        $l = strtoupper(substr($this->lastname, 0, 1));
        return $f . $l;
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