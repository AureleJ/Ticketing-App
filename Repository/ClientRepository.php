<?php
require_once __DIR__ . '/../Entity/ClientEntity.php';


class ClientRepository
{
    private array $mockData = [
        [
            "id" => 1,
            "company" => "Bio Store",
            "contact_name" => "Patrick Fabre",
            "email" => "direction@biostore.fr",
            "phone" => "01 45 89 23 11",
            "status" => "Active",
            "avatarColor" => "green"
        ],
        [
            "id" => 2,
            "company" => "Tech Consult",
            "contact_name" => "Béatrice Joly",
            "email" => "support@techconsult.io",
            "phone" => "09 78 54 12 30",
            "status" => "Active",
            "avatarColor" => "blue" 
        ],
        [
            "id" => 3,
            "company" => "Bakery & Co",
            "contact_name" => "Sophie Martin",
            "email" => "commande@bakery.co",
            "phone" => "06 12 34 56 78",
            "status" => "Active",
            "avatarColor" => "yellow"
        ],
        [
            "id" => 4,
            "company" => "Banque S.A.",
            "contact_name" => "M. Bernard (DSI)",
            "email" => "secu@banquesa.fr",
            "phone" => "01 00 00 00 00",
            "status" => "Active",
            "avatarColor" => "red" 
        ],
        [
            "id" => 5,
            "company" => "Green Energy",
            "contact_name" => "Lucie Power",
            "email" => "lucie@greenenergy.io",
            "phone" => "07 99 88 77 66",
            "status" => "Prospect",
            "avatarColor" => "cyan"
        ],
        [
            "id" => 6,
            "company" => "Garage Auto 2000",
            "contact_name" => "André Mécano",
            "email" => "garage2000@orange.fr",
            "phone" => "02 44 55 66 77",
            "status" => "Inactive",
            "avatarColor" => "gray"
        ],
        [
            "id" => 7,
            "company" => "Start-up Nation",
            "contact_name" => "Kevin Founder",
            "email" => "ceo@startupnation.com",
            "phone" => "06 00 11 22 33",
            "status" => "Active",
            "avatarColor" => "purple"
        ],
        [
            "id" => 8,
            "company" => "Mairie de Lyon",
            "contact_name" => "Service Com",
            "email" => "communication@mairie-lyon.fr",
            "phone" => "04 72 10 30 30",
            "status" => "Active",
            "avatarColor" => "blue"
        ]
    ];

    public function findAll(): array
    {
        return array_map(fn($item) => new ClientEntity($item), $this->mockData);
    }

    public function findById(int $id): ?ClientEntity
    {
        foreach ($this->mockData as $data) {
            if ($data['id'] === $id) {
                return new ClientEntity($data);
            }
        }
        return null; 
    }

    public function create(array $data): void
    {
        $data['id'] = count($this->mockData) + 1;
        if(empty($data['avatarColor'])) {
            $colors = ['blue', 'green', 'yellow', 'red', 'purple', 'cyan'];
            $data['avatarColor'] = $colors[array_rand($colors)];
        }
        $this->mockData[] = $data;
    }
}