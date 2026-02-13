<?php
require_once __DIR__ . '/../Entity/ClientEntity.php';
require_once __DIR__ . '/../Service/DatabaseService.php';

class ClientRepository
{
    private $db;
    private $tableName = 'clients';

    public function __construct()
    {
        $service = new DatabaseService();
        $this->db = $service->connect();
    }

    public function createClient(array $params)
    {
        try {
            $query = "INSERT INTO $this->tableName (company, contact_name, email, phone, status, avatar_color) VALUES (:company, :contact_name, :email, :phone, :status, :avatar_color)";
            $stmt = $this->db->prepare($query);
            $stmt->execute([
                ":company" => $params["company"],
                ":contact_name" => $params["contact_name"],
                ":email" => $params["email"],
                ":phone" => $params["phone"],
                ":status" => $params["status"],
                ":avatar_color" => $params["avatar_color"],
            ]);
        } catch (PDOException $e) {
            echo "<h2 style='color:red'>❌ Erreur SQL :</h2>";
            echo "<pre>" . $e->getMessage() . "</pre>";
        }
    }

    public function updateClient(array $params)
    {

    }

    public function deleteClient(int $id)
    {
        try {
            $ticketRepo = new TicketRepository();
            $projectRepo = new ProjectRepository();

            $clientTickets = $ticketRepo->findClientTicket($id);
            $clientProjects = $projectRepo->getClientProjects($id);

            foreach ($clientTickets as $clientTicket) {
                $ticketRepo->deleteTicket($clientTicket->id);
            }

            foreach ($clientProjects as $clientProject) {
                $projectRepo->deleteProject($clientProject->id);
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

    public function getAllClients()
    {
        try {
            $query = "SELECT * FROM $this->tableName";
            $stmt = $this->db->query($query);
            $data = $stmt->fetchAll();
            $clients = array_map(fn($item) => new ClientEntity($item), $data);
            return $clients;
        } catch (PDOException $e) {
            echo "<h2 style='color:red'> Erreur SQL :</h2>";
            echo "<pre>" . $e->getMessage() . "</pre>";
            return [];
        }
    }

    public function getClientsById($id)
    {
        try {
            $query = "SELECT * FROM $this->tableName WHERE id=:id";
            $stmt = $this->db->prepare($query);
            $stmt->execute([
                ":id" => $id
            ]);
            $data = $stmt->fetch();
            return new ClientEntity($data);
        } catch (PDOException $e) {
            echo "<h2 style='color:red'> Erreur SQL :</h2>";
            echo "<pre>" . $e->getMessage() . "</pre>";
            return new ClientEntity([]);
        }
    }
}