<?php
require_once __DIR__ . '/../Entity/ProjectEntity.php';
require_once __DIR__ . '/../Service/AuthService.php';
require_once __DIR__ . '/../Service/DatabaseService.php';

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
            $authService = new AuthService();
            $currentUser = $authService->getAuthUser();

            $params = [];

            if ($currentUser->type === 'Client') {
                $query = "SELECT * FROM $this->tableName WHERE client_id = :client_id";
                $params[':client_id'] = $currentUser->client_id;
            } elseif ($currentUser->type === 'Admin') {
                $query = "SELECT * FROM $this->tableName";
            } else {
                $query = "SELECT DISTINCT p.* FROM $this->tableName p
                    LEFT JOIN project_members pm ON p.id = pm.project_id
                    WHERE p.owner_id = :user_id OR pm.user_id = :user_id2";
                $params[':user_id'] = $currentUser->id;
                $params[':user_id2'] = $currentUser->id;
            }

            $stmt = $this->db->prepare($query);
            $stmt->execute($params);
            $data = $stmt->fetchAll();
            $projects = array_map(fn($item) => new ProjectEntity($item), $data);

            if (!empty($filters['search'])) {
                $term = strtolower($filters['search']);
                $projects = array_filter($projects, fn($p) => str_contains(strtolower($p->name), $term) || (str_contains(strtolower($p->client), $term)));
            }

            if (!empty($filters['status']) && $filters['status'] !== 'all') {
                $projects = array_filter($projects, fn($p) => $p->status === $filters['status']);
            }

            if (!empty($filters['priority']) && $filters['priority'] !== 'all') {
                $projects = array_filter($projects, fn($p) => $p->priority === $filters['priority']);
            }

            if (!empty($filters['client_id']) && $filters['client_id'] !== 'all') {
                $cid = (int) $filters['client_id'];
                $projects = array_filter($projects, fn($p) => $p->client_id === $cid);
            }

            if (!empty($filters['tab'])) {
                if ($filters['tab'] === 'mine') {
                    $projects = array_filter($projects, fn($p) => $p->owner_id === $currentUser->id);
                } elseif ($filters['tab'] === 'finished') {
                    $projects = array_filter($projects, fn($p) => $p->status === 'Terminé');
                }
            }

            $sortBy = $filters['sort'] ?? 'recent';
            usort($projects, function ($a, $b) use ($sortBy) {
                if ($sortBy === 'priority') {
                    $priorities = ['Haute' => 3, 'Moyenne' => 2, 'Basse' => 1];
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
            $projectTickets = $ticketRepo->findProjectTicket($id);

            foreach ($projectTickets as $projectTicket) {
                $ticketRepo->deleteTicket($projectTicket->id);
            }
            
            $stmt = $this->db->prepare("DELETE FROM project_members WHERE project_id = :id");
            $stmt->execute([":id" => $id]);

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

    public function editProject(int $id, array $params)
    {
        try {
            $query = "UPDATE $this->tableName SET name = :name, description = :description, client_id = :client_id, status = :status, priority = :priority, progress = :progress, budget_h = :budget_h, total_h = :total_h WHERE id = :id";
            $stmt = $this->db->prepare($query);
            $stmt->execute([
                ":id" => $id,
                ":name" => $params["name"],
                ":description" => $params["description"],
                ":client_id" => $params["client_id"],
                ":status" => $params["status"],
                ":priority" => $params["priority"],
                ":progress" => $params["progress"],
                ":budget_h" => $params["budget_h"],
                ":total_h" => $params["total_h"],
            ]);
        } catch (PDOException $e) {
            echo "<h2 style='color:red'> Erreur SQL :</h2>";
            echo "<pre>" . htmlspecialchars($e->getMessage()) . "</pre>";
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

            if (!$data) {
                return new ProjectEntity([]);
            }

            $memberQuery = "SELECT user_id, role FROM project_members WHERE project_id = :id";
            $memberStmt = $this->db->prepare($memberQuery);
            $memberStmt->execute([":id" => $id]);
            $data['team'] = $memberStmt->fetchAll(PDO::FETCH_ASSOC);

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