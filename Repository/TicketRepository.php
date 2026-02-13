<?php
require_once __DIR__ . '/../Entity/TicketEntity.php';
require_once __DIR__ . '/../Service/AuthService.php';
require_once __DIR__ . '/../Utils/Debug.php';

class TicketRepository
{
    private array $mockData = [
        [
            "project_id" => 0,
            "id" => 0,
            "subject" => "Crash au lancement sur Android 12",
            "client_id" => 1,
            "assigned_id" => 5, 
            "date" => "2023-11-20 09:30",
            "status" => "En cours",
            "priority" => "Haute",
            "type" => "Inclus",
            "description" => "L'application se ferme immédiatement après le splash screen sur les Samsung S21/S22. Logs envoyés sur Sentry."
        ],
        [
            "project_id" => 0,
            "id" => 1,
            "subject" => "Intégration API Stripe v3",
            "client_id" => 1,
            "assigned_id" => 1, 
            "date" => "2023-11-19 14:00",
            "status" => "En cours",
            "priority" => "Haute",
            "type" => "Facturable",
            "description" => "Mise à jour des webhooks de paiement pour gérer la 3DSecure v2 obligatoires."
        ],
        [
            "project_id" => 0,
            "id" => 2,
            "subject" => "Traductions manquantes profil",
            "client_id" => 1,
            "assigned_id" => 5, 
            "date" => "2023-11-18 10:15",
            "status" => "Non traité",
            "priority" => "Basse",
            "type" => "Inclus",
            "description" => "La section 'Mes commandes' est restée en anglais dans la version FR."
        ],

        [
            "project_id" => 3,
            "id" => 3,
            "subject" => "Faille XSS Formulaire Contact",
            "client_id" => 4,
            "assigned_id" => 4, 
            "date" => "2023-11-20 11:45",
            "status" => "En cours",
            "priority" => "Haute",
            "type" => "Inclus",
            "description" => "Injection de script possible dans le champ 'Message' du formulaire public. Patch urgent requis."
        ],
        [
            "project_id" => 3,
            "id" => 4,
            "subject" => "Renouvellement Certificat SSL",
            "client_id" => 4,
            "assigned_id" => 4, 
            "date" => "2023-11-15 09:00",
            "status" => "Terminé",
            "priority" => "Haute",
            "type" => "Facturable",
            "description" => "Le certificat wildcard *.banquesa.fr expire dans 3 jours. Renouvellement via Let's Encrypt effectué."
        ],

        [
            "project_id" => 2,
            "id" => 5,
            "subject" => "Validation Maquette Home V2",
            "client_id" => 3,
            "assigned_id" => 3, 
            "date" => "2023-11-17 16:30",
            "status" => "En attente",
            "priority" => "Moyenne",
            "type" => "Inclus",
            "description" => "Attente retour client sur le choix des couleurs pastel pour le header."
        ],
        [
            "project_id" => 2,
            "id" => 6,
            "subject" => "Export Logo SVG",
            "client_id" => 3,
            "assigned_id" => 3, 
            "date" => "2023-11-14 11:00",
            "status" => "Terminé",
            "priority" => "Basse",
            "type" => "Inclus",
            "description" => "Fournir les fichiers sources pour l'imprimeur."
        ],

        [
            "project_id" => 1,
            "id" => 7,
            "subject" => "Mise à jour WordPress 6.4",
            "client_id" => 2,
            "assigned_id" => 2, 
            "date" => "2023-11-18 08:30",
            "status" => "En attente",
            "priority" => "Basse",
            "type" => "Inclus",
            "description" => "Backup complet effectué. Mise à jour en attente de validation sur le staging."
        ],
        [
            "project_id" => 1,
            "id" => 8,
            "subject" => "Lenteur chargement page Admin",
            "client_id" => 2,
            "assigned_id" => 2, 
            "date" => "2023-11-19 15:45",
            "status" => "En cours",
            "priority" => "Moyenne",
            "type" => "Facturable",
            "description" => "Optimisation des requêtes SQL sur le dashboard admin qui prend +10s à charger."
        ],

        [
            "project_id" => 4,
            "id" => 9,
            "subject" => "Connexion MQTT instable",
            "client_id" => 5,
            "assigned_id" => 2, 
            "date" => "2023-11-20 10:00",
            "status" => "Non traité",
            "priority" => "Haute",
            "type" => "Facturable",
            "description" => "Perte de paquets de données sur les capteurs de la zone Sud. Vérifier la config Broker."
        ],
        [
            "project_id" => 4,
            "id" => 10,
            "subject" => "Design Graphiques Consommation",
            "client_id" => 5,
            "assigned_id" => 5, 
            "date" => "2023-11-16 14:00",
            "status" => "En cours",
            "priority" => "Moyenne",
            "type" => "Inclus",
            "description" => "Intégration de la librairie Chart.js pour visualiser les pics de consommation."
        ],

        [
            "project_id" => 6,
            "id" => 11,
            "subject" => "Bug calcul congés payés",
            "client_id" => 7,
            "assigned_id" => 1, 
            "date" => "2023-11-20 08:00",
            "status" => "Non traité",
            "priority" => "Haute",
            "type" => "Inclus",
            "description" => "Le calcul des CP ne prend pas en compte les années bissextiles."
        ],
        [
            "project_id" => 6,
            "id" => 12,
            "subject" => "Setup CI/CD Pipeline",
            "client_id" => 7,
            "assigned_id" => 4, 
            "date" => "2023-11-10 10:00",
            "status" => "Terminé",
            "priority" => "Moyenne",
            "type" => "Facturable",
            "description" => "Mise en place de GitHub Actions pour le déploiement automatique."
        ]
    ];

    public function findProjectTicket(int $project_id): array
    {
        $tickets = []; 
        foreach ($this->mockData as $data) {
            if ($data['project_id'] === $project_id) {
                $tickets[] = new TicketEntity($data);
            }
        }
        return $tickets;
    }

    public function findAll(array $filters = []): array
    {
        $results = $this->mockData;
        $tickets = array_map(fn($item) => new TicketEntity($item), $results);

        if (!empty($filters['search'])) {
            $term = strtolower($filters['search']);
            $tickets = array_filter($tickets, fn($t) => str_contains(strtolower($t->subject), $term));
        }

        if (!empty($filters['status']) && $filters['status'] !== 'all') {
            $tickets = array_filter($tickets, fn($t) => $t->status === $filters['status']);
        }

        if (!empty($filters['tab'])) {
            if ($filters['tab'] === 'mine') {
                $currentUser = AuthService::getAuthUser();
                $tickets = array_filter($tickets, fn($t) => $t->assigned_id === $currentUser->id);
            } elseif ($filters['tab'] === 'finished') {
                $tickets = array_filter($tickets, fn($t) => $t->status === 'Terminé');
            }
        }

        usort($tickets, fn($a, $b) => strtotime($b->date) - strtotime($a->date));

        return $tickets;
    }

    public function create(array $data): void {
        $data['id'] = count($this->mockData) + 1;
        $this->mockData[] = $data;
    }

    public function findById(int $id): ?TicketEntity {
        foreach ($this->mockData as $data) {
            if ($data['id'] === $id) {
                return new TicketEntity($data);
            }
        }
        return null;
    }

    public function countTickets(array $tickets = []): array {
        $count = count($tickets);
        $traited = count(array_filter($tickets, fn($p) => $p->status === 'Terminé'));
        $inProgress = count(array_filter($tickets, fn($p) => $p->status === 'En cours'));
        $waiting = count(array_filter($tickets, fn($p) => $p->status === 'En attente'));
        $untraited = count(array_filter($tickets, fn($p) => $p->status === 'Non traité')); 
        
        return [$count, $traited, $inProgress, $waiting, $untraited];
    }
}