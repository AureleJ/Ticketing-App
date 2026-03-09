<?php
require_once __DIR__ . '/../Repository/UserRepository.php';
require_once __DIR__ . '/../Entity/UserEntity.php';
require_once __DIR__ . '/../Service/DatabaseService.php';

class AuthService
{
    private $db;
    private $tableName = 'users';

    public function __construct()
    {
        $service = new DatabaseService();
        $this->db = $service->connect();
    }

    public function getAuthUser(): UserEntity
    {
        session_start();
        if (!isset($_SESSION['user_id'])) {
            return new UserEntity([]);
        }

        $userRepo = new UserRepository();
        return $userRepo->getUserById($_SESSION['user_id']);
    }

    public function login($username, $password)
    {
        $userRepo = new UserRepository();
        $user = $userRepo->getUserByUsername($username);

        if ($user && password_verify($password, $user->password_hash)) {
            $_SESSION['user_id'] = $user->id;
            return true;
        }

        return false;
    }

    public function logout()
    {
        session_destroy();
    }
}