<?php
require_once __DIR__ . '/../Entity/ProjectEntity.php';
require_once __DIR__ . '/../Service/AuthService.php';

class ProjectRepository
{
    private array $mockData = [
        [
            "id" => 0,
            "name" => "App Mobile E-commerce v2",
            "description" => "Refonte complète de l'application mobile sous Flutter. Intégration du module de fidélité et notifications push ciblées.",
            "client_id" => 1, 
            "progress" => 45,
            "budget_h" => 150,
            "total_h" => 200,
            "status" => "En cours",
            "date" => "2023-10-24",
            "team" => [
                ["user_id" => 1, "role" => "PM"], 
                ["user_id" => 5, "role" => "Dev Mobile"]
            ],
            "owner_id" => 1, 
            "priority" => "High"
        ],
        [
            "id" => 1,
            "name" => "TMA Site Web",
            "description" => "Tierce Maintenance Applicative mensuelle. Mises à jour de sécurité WordPress et monitoring serveur.",
            "client_id" => 2, 
            "progress" => 15,
            "budget_h" => 10,
            "total_h" => 50, 
            "status" => "En cours",
            "date" => "2023-10-20",
            "team" => [
                ["user_id" => 2, "role" => "Back-end"]
            ],
            "owner_id" => 2, 
            "priority" => "Medium"
        ],
        [
            "id" => 2,
            "name" => "Refonte Identité Visuelle",
            "description" => "Création du nouveau logo, charte graphique complète et déclinaison sur supports print (cartes de visite, packaging).",
            "client_id" => 3, 
            "progress" => 100,
            "budget_h" => 42,
            "total_h" => 40, 
            "status" => "Terminé",
            "date" => "2023-09-15",
            "team" => [
                ["user_id" => 3, "role" => "Lead Design"]
            ],
            "owner_id" => 3, 
            "priority" => "Low"
        ],
        [
            "id" => 3,
            "name" => "Audit de Sécurité Infra",
            "description" => "Pentest complet de l'infrastructure bancaire. Analyse des ports ouverts, tests d'intrusion et rapport de conformité RGPD.",
            "client_id" => 4, 
            "progress" => 10,
            "budget_h" => 5,
            "total_h" => 80,
            "status" => "En attente",
            "date" => "2023-11-01",
            "team" => [
                ["user_id" => 4, "role" => "Expert Sécu"],
                ["user_id" => 2, "role" => "Support"]
            ],
            "owner_id" => 1, 
            "priority" => "High"
        ],
        [
            "id" => 4,
            "name" => "Dashboard IoT & Big Data",
            "description" => "Développement d'un tableau de bord React pour visualiser les données des capteurs énergétiques en temps réel (MQTT/Websockets).",
            "client_id" => 5, 
            "progress" => 95,
            "budget_h" => 190,
            "total_h" => 200,
            "status" => "En cours",
            "date" => "2023-08-10",
            "team" => [
                ["user_id" => 2, "role" => "Lead Dev"],
                ["user_id" => 5, "role" => "Front-end"]
            ],
            "owner_id" => 2, 
            "priority" => "High"
        ],
        [
            "id" => 5,
            "name" => "Campagne SEO Q4",
            "description" => "Optimisation du référencement naturel : audit technique, rédaction de 10 articles de blog et netlinking.",
            "client_id" => 6,
            "progress" => 30,
            "budget_h" => 10,
            "total_h" => 35,
            "status" => "En cours",
            "date" => "2023-11-10",
            "team" => [
                ["user_id" => 6, "role" => "SEO Specialist"]
            ],
            "owner_id" => 6, 
            "priority" => "Medium"
        ],
        [
            "id" => 6,
            "name" => "Application SaaS RH",
            "description" => "MVP pour la gestion des congés et notes de frais. Stack : Laravel / Vue.js.",
            "client_id" => 7, 
            "progress" => 60,
            "budget_h" => 120,
            "total_h" => 300,
            "status" => "En cours",
            "date" => "2023-09-01",
            "team" => [
                ["user_id" => 1, "role" => "Lead"],
                ["user_id" => 2, "role" => "Back"],
                ["user_id" => 5, "role" => "Front"]
            ],
            "owner_id" => 1, 
            "priority" => "High"
        ]
    ];

    public function findAll(array $filters = []): array
    {
        $results = $this->mockData;

        $projects = array_map(fn($item) => new ProjectEntity($item), $results);

        if (!empty($filters['search'])) {
            $term = strtolower($filters['search']);
            $projects = array_filter($projects, fn($p) => str_contains(strtolower($p->name), $term) || (str_contains(strtolower($p->client), $term)));
        }

        if (!empty($filters['status']) && $filters['status'] !== 'all') {
            $projects = array_filter($projects, fn($p) => $p->status === $filters['status']);
        }

        if (!empty($filters['tab'])) {
            if ($filters['tab'] === 'mine') {
                $currentUser = AuthService::getAuthUser();
                $projects = array_filter($projects, fn($p) => $p->owner_id === $currentUser->id);
            } elseif ($filters['tab'] === 'finished') {
                $projects = array_filter($projects, fn($p) => $p->status === 'Terminé');
            }
        }

        $sortBy = $filters['sort'];
        usort($projects, function ($a, $b) use ($sortBy) {
            if ($sortBy === 'priority') {
                $priorities = ['High' => 3, 'Medium' => 2, 'Low' => 1];
                return ($priorities[$b->priority] ?? 0) - ($priorities[$a->priority] ?? 0);
            } 

            return strtotime($b->date) - strtotime($a->date);
        });

        return $projects;
    }

    public function create(array $data): void
    {
        $data['id'] = count($this->mockData);
        $data['owner_id'] = AuthService::getAuthUser()->id;
        $this->mockData[] = $data;
    }

    public function findById(int $id): ?ProjectEntity {
        foreach ($this->mockData as $data) {
            if ($data['id'] === $id) {
                return new ProjectEntity($data);
            }
        }
        return null;
    }
}