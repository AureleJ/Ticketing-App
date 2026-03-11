<?php
require_once __DIR__ . '/../Entity/TicketEntity.php';
require_once __DIR__ . '/../Service/AuthService.php';
require_once __DIR__ . '/../Service/DatabaseService.php';
require_once __DIR__ . '/../Utils/Debug.php';

class TicketRepository
{
    private $db;
    private $tableName = 'tickets';

    public function __construct()
    {
        $service = new DatabaseService();
        $this->db = $service->connect();
    }
    public function getAllTickets(array $filters = []): array
    {
        try {
            $authService = new AuthService();
            $currentUser = $authService->getAuthUser();

            $params = [];

            if ($currentUser->type === 'Client') {
                $query = "SELECT * FROM $this->tableName WHERE client_id = :client_id";
                $params[':client_id'] = $currentUser->client_id;
            } elseif ($currentUser->type === 'Admin') {
                $query = "SELECT * FROM $this->tableName";
            } else {
                $query = "SELECT DISTINCT t.* FROM $this->tableName t
                    LEFT JOIN project_members pm ON t.project_id = pm.project_id
                    WHERE t.assigned_id = :user_id OR pm.user_id = :user_id2";
                $params[':user_id'] = $currentUser->id;
                $params[':user_id2'] = $currentUser->id;
            }

            $stmt = $this->db->prepare($query);
            $stmt->execute($params);
            $data = $stmt->fetchAll();
            $tickets = array_map(fn($item) => new TicketEntity($item), $data);

            if (!empty($filters['search'])) {
                $term = strtolower($filters['search']);
                $tickets = array_filter($tickets, fn($t) => str_contains(strtolower($t->subject), $term));
            }

            if (!empty($filters['status']) && $filters['status'] !== 'all') {
                $tickets = array_filter($tickets, fn($t) => $t->status === $filters['status']);
            }

            if (!empty($filters['priority']) && $filters['priority'] !== 'all') {
                $tickets = array_filter($tickets, fn($t) => $t->priority === $filters['priority']);
            }

            if (!empty($filters['type']) && $filters['type'] !== 'all') {
                $tickets = array_filter($tickets, fn($t) => $t->type === $filters['type']);
            }

            if (!empty($filters['tab'])) {
                if ($filters['tab'] === 'mine') {
                    $tickets = array_filter($tickets, fn($t) => $t->assigned_id === $currentUser->id);
                } elseif ($filters['tab'] === 'finished') {
                    $tickets = array_filter($tickets, fn($t) => $t->status === 'Terminé');
                }
            }

            $sortBy = $filters['sort'] ?? 'recent';
            usort($tickets, function ($a, $b) use ($sortBy) {
                if ($sortBy === 'priority') {
                    $priorities = ['Haute' => 3, 'Moyenne' => 2, 'Basse' => 1];
                    return ($priorities[$b->priority] ?? 0) - ($priorities[$a->priority] ?? 0);
                }
                return strtotime($b->date) - strtotime($a->date);
            });

            return $tickets;
        } catch (PDOException $e) {
            echo "<h2 style='color:red'> Erreur SQL :</h2>";
            echo "<pre>" . $e->getMessage() . "</pre>";
            return [];
        }
    }

    public function deleteTicket(int $id)
    {
        try {
            $query = "DELETE FROM $this->tableName WHERE id = :id";
            $stmt = $this->db->prepare($query);
            $stmt->execute([
                ":id" => $id,
            ]);
        } catch (PDOException $e) {
            echo "<h2 style='color:red'> Erreur SQL :</h2>";
            echo "<pre>" . $e->getMessage() . "</pre>";
        }
    }

    public function createTicket(array $params)
    {
        try {
            $projectRepo = new ProjectRepository();
            $project = $projectRepo->getProjectsById($params["project_id"]);

            $query = "INSERT INTO $this->tableName (subject, description, project_id, client_id, assigned_id, status, priority, type, date) VALUES (:subject, :description, :project_id, :client_id, :assigned_id, :status, :priority, :type, :date)";
            $stmt = $this->db->prepare($query);
            $stmt->execute([
                ":subject" => $params["subject"],
                ":description" => $params["description"],
                ":project_id" => $params["project_id"],
                ":client_id" => $project->client_id,
                ":assigned_id" => $params["assigned_id"],
                ":status" => $params["status"],
                ":priority" => $params["priority"],
                ":type" => $params["type"],
                ":date" => $params["date"],
            ]);
        } catch (PDOException $e) {
            echo "<h2 style='color:red'> Erreur SQL :</h2>";
            echo "<pre>" . $e->getMessage() . "</pre>";
        }
    }

    public function editTicket(int $id, array $params)
    {
        try {
            $query = "UPDATE $this->tableName SET subject = :subject, description = :description, project_id = :project_id, assigned_id = :assigned_id, status = :status, priority = :priority, type = :type WHERE id = :id";
            $stmt = $this->db->prepare($query);
            $stmt->execute([
                ":id" => $id,
                ":subject" => $params["subject"],
                ":description" => $params["description"],
                ":project_id" => $params["project_id"],
                ":assigned_id" => $params["assigned_id"],
                ":status" => $params["status"],
                ":priority" => $params["priority"],
                ":type" => $params["type"],
            ]);
        } catch (PDOException $e) {
            echo "<h2 style='color:red'> Erreur SQL :</h2>";
            echo "<pre>" . htmlspecialchars($e->getMessage()) . "</pre>";
        }
    }

    public function closeTicket(int $id)
    {
        try {
            $query = "UPDATE $this->tableName SET status = 'Terminé' WHERE id = :id";
            $stmt = $this->db->prepare($query);
            $stmt->execute([":id" => $id]);
        } catch (PDOException $e) {
            echo "<h2 style='color:red'> Erreur SQL :</h2>";
            echo "<pre>" . htmlspecialchars($e->getMessage()) . "</pre>";
        }
    }

    public function getTicketsById($id)
    {
        try {
            $query = "SELECT * FROM $this->tableName WHERE id=:id";
            $stmt = $this->db->prepare($query);
            $stmt->execute([
                ":id" => $id
            ]);
            $data = $stmt->fetch();
            if (!$data) {
                return new TicketEntity([]);
            }
            return new TicketEntity($data);
        } catch (PDOException $e) {
            echo "<h2 style='color:red'> Erreur SQL :</h2>";
            echo "<pre>" . $e->getMessage() . "</pre>";
            return new TicketEntity([]);
        }
    }

    public function findProjectTicket($id)
    {
        try {
            $query = "SELECT * FROM $this->tableName WHERE project_id=:id";
            $stmt = $this->db->prepare($query);
            $stmt->execute([
                ":id" => $id
            ]);
            $data = $stmt->fetchAll();
            return array_map(fn($item) => new TicketEntity($item), $data);
        } catch (PDOException $e) {
            echo "<h2 style='color:red'> Erreur SQL :</h2>";
            echo "<pre>" . $e->getMessage() . "</pre>";
            return [];
        }
    }

    public function findClientTicket($id)
    {
        try {
            $query = "SELECT * FROM $this->tableName WHERE client_id=:id";
            $stmt = $this->db->prepare($query);
            $stmt->execute([
                ":id" => $id
            ]);
            $data = $stmt->fetchAll();
            return array_map(fn($item) => new TicketEntity($item), $data);
        } catch (PDOException $e) {
            echo "<h2 style='color:red'> Erreur SQL :</h2>";
            echo "<pre>" . $e->getMessage() . "</pre>";
            return [];
        }
    }
}