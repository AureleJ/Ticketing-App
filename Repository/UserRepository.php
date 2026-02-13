<?php
require_once __DIR__ . '/../Entity/UserEntity.php';

class UserRepository
{
    private $db;
    private $tableName = 'users';

    public function __construct()
    {
        $service = new DatabaseService();
        $this->db = $service->connect();
    }

    public function getAllUser()
    {
        try {
            $query = "SELECT * FROM $this->tableName";
            $stmt = $this->db->query($query);
            $data = $stmt->fetchAll();
            $clients = array_map(fn($item) => new UserEntity($item), $data);
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
            return new UserEntity($data);
        } catch (PDOException $e) {
            echo "<h2 style='color:red'> Erreur SQL :</h2>";
            echo "<pre>" . $e->getMessage() . "</pre>";
            return new UserEntity([]);
        }
    }

     public function createUser(array $params)
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
            echo "<h2 style='color:red'> Erreur SQL :</h2>";
            echo "<pre>" . $e->getMessage() . "</pre>";
        }
    }
}