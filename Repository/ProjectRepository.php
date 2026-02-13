<?php
require_once __DIR__ . '/../Entity/ProjectEntity.php';
require_once __DIR__ . '/../Service/AuthService.php';

class ProjectRepository
{
    private $db;
    private $tableName = 'projects';

    public function __construct()
    {
        $service = new DatabaseService();
        $this->db = $service->connect();
    }

    public function getAllProjects(array $filters = [])
    {
        try {
            $query = "SELECT * FROM $this->tableName";
            $stmt = $this->db->query($query);
            $data = $stmt->fetchAll();
            $projects = array_map(fn($item) => new ProjectEntity($item), $data);

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
        } catch (PDOException $e) {
            echo "<h2 style='color:red'> Erreur SQL :</h2>";
            echo "<pre>" . $e->getMessage() . "</pre>";
            return [];
        }
    }

    public function createProject(array $params)
    {
        try {
            $query = "INSERT INTO $this->tableName (name, description, client_id, owner_id, progress, budget_h, total_h, status, priority, date) VALUES (:name, :description, :client_id, :owner_id, :progress, :budget_h, :total_h, :status, :priority, :date)";
            $stmt = $this->db->prepare($query);
            $stmt->execute([
                ":name" => $params["name"],
                ":description" => $params["description"],
                ":client_id" => $params["client_id"],
                ":owner_id" => AuthService::getAuthUser()->id,
                ":progress" => $params["progress"],
                ":budget_h" => $params["budget_h"],
                ":total_h" => $params["total_h"],
                ":status" => $params["status"],
                ":priority" => $params["priority"],
                ":date" => $params["date"],
            ]);
        } catch (PDOException $e) {
            echo "<h2 style='color:red'> Erreur SQL :</h2>";
            echo "<pre>" . $e->getMessage() . "</pre>";
        }
    }

    public function deleteProject(int $id)
    {
        try {
            $ticketRepo = new TicketRepository();
            $clientTickets = $ticketRepo->findClientTicket($id);

            foreach ($clientTickets as $clientTicket) {
                $ticketRepo->deleteTicket($clientTicket->id);
            }
            
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

    public function getProjectsById($id)
    {
        try {
            $query = "SELECT * FROM $this->tableName WHERE id=:id";
            $stmt = $this->db->prepare($query);
            $stmt->execute([
                ":id" => $id
            ]);
            $data = $stmt->fetch();
            return new ProjectEntity($data);
        } catch (PDOException $e) {
            echo "<h2 style='color:red'> Erreur SQL :</h2>";
            echo "<pre>" . $e->getMessage() . "</pre>";
            return new ProjectEntity([]);
        }
    }

    public function getClientProjects($id)
    {
        try {
            $query = "SELECT * FROM $this->tableName WHERE client_id=:id";
            $stmt = $this->db->prepare($query);
            $stmt->execute([
                ":id" => $id
            ]);
            $data = $stmt->fetchAll();
            return array_map(fn($item) => new ProjectEntity($item), $data);
        } catch (PDOException $e) {
            echo "<h2 style='color:red'> Erreur SQL :</h2>";
            echo "<pre>" . $e->getMessage() . "</pre>";
            return [];
        }
    }
}