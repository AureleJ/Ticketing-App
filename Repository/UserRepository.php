<?php
require_once __DIR__ . '/../Service/DatabaseService.php';
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
            $users = array_map(fn($item) => new UserEntity($item), $data);
            return $users;
        } catch (PDOException $e) {
            echo "<h2 style='color:red'> Erreur SQL :</h2>";
            echo "<pre>" . $e->getMessage() . "</pre>";
            return [];
        }
    }

    public function getUserById($id)
    {
        try {
            $query = "SELECT * FROM $this->tableName WHERE id=:id";
            $stmt = $this->db->prepare($query);
            $stmt->execute([
                ":id" => $id
            ]);
            $data = $stmt->fetch();
            if (!$data) {
                return new UserEntity([]);
            }
            return new UserEntity($data);
        } catch (PDOException $e) {
            echo "<h2 style='color:red'> Erreur SQL :</h2>";
            echo "<pre>" . $e->getMessage() . "</pre>";
            return new UserEntity([]);
        }
    }

    public function getUserByUsername($username)
    {
        try {
            $query = "SELECT * FROM $this->tableName WHERE username=:username";
            $stmt = $this->db->prepare($query);
            $stmt->execute([
                ":username" => $username
            ]);
            $data = $stmt->fetch();
            if (!$data) {
                return new UserEntity([]);
            }
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
            $query = "INSERT INTO $this->tableName (type, firstname, lastname, username, email, password_hash, role, status, avatar_color, client_id) VALUES (:type, :firstname, :lastname, :username, :email, :password_hash, :role, :status, :avatar_color, :client_id)";
            $stmt = $this->db->prepare($query);
            $stmt->execute([
                ":type" => $params["type"],
                ":firstname" => $params["firstname"],
                ":lastname" => $params["lastname"],
                ":username" => $params["username"],
                ":email" => $params["email"],
                ":password_hash" => $params["password_hash"],
                ":role" => $params["role"],
                ":status" => $params["status"],
                ":avatar_color" => $params["avatar_color"],
                ":client_id" => $params["client_id"],
            ]);
            return (int) $this->db->lastInsertId();
        } catch (PDOException $e) {
            echo "<h2 style='color:red'> Erreur SQL :</h2>";
            echo "<pre>" . $e->getMessage() . "</pre>";
            return false;
        }
    }

    public function getUserByEmail($email)
    {
        try {
            $query = "SELECT * FROM $this->tableName WHERE email=:email";
            $stmt = $this->db->prepare($query);
            $stmt->execute([":email" => $email]);
            $data = $stmt->fetch();
            if (!$data) {
                return null;
            }
            return new UserEntity($data);
        } catch (PDOException $e) {
            return null;
        }
    }
}