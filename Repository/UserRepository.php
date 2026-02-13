<?php
require_once __DIR__ . '/../Entity/UserEntity.php';

class UserRepository
{
    private array $mockData = [
        [
            "id" => 1,
            "firstname" => "Aurele",
            "lastname" => "Joblet",
            "email" => "aurele@ticketing.com",
            "role" => "Admin",
            "status" => "Active",
            "avatar_color" => "blue"
        ],
        [
            "id" => 2,
            "firstname" => "Jean",
            "lastname" => "Dev",
            "email" => "jean@ticketing.com",
            "role" => "Lead Dev",
            "status" => "Active",
            "avatar_color" => "yellow"
        ],
        [
            "id" => 3,
            "firstname" => "Sophie",
            "lastname" => "Graph",
            "email" => "sophie@design.com",
            "role" => "Designer UI/UX",
            "status" => "Active",
            "avatar_color" => "purple"
        ],
        [
            "id" => 4,
            "firstname" => "Paul",
            "lastname" => "Sysadmin",
            "email" => "paul@ops.com",
            "role" => "DevOps",
            "status" => "Inactive",
            "avatar_color" => "red"
        ],
        [
            "id" => 5,
            "firstname" => "Julie",
            "lastname" => "Front",
            "email" => "julie@ticketing.com",
            "role" => "Dev Front-end",
            "status" => "Active",
            "avatar_color" => "cyan"
        ],
        [
            "id" => 6,
            "firstname" => "Thomas",
            "lastname" => "Mark",
            "email" => "thomas@marketing.com",
            "role" => "Marketing",
            "status" => "Active",
            "avatar_color" => "green"
        ]
    ];

    public function findAll(): array
    {
        return array_map(fn($item) => new UserEntity($item), $this->mockData);
    }

    public function findById(int $id): ?UserEntity
    {
        foreach ($this->mockData as $data) {
            if ($data['id'] === $id) {
                return new UserEntity($data);
            }
        }
        return null;
    }

    public function create(array $data): void
    {
        $data['id'] = count($this->mockData) + 1;
        $this->mockData[] = $data;
    }
}