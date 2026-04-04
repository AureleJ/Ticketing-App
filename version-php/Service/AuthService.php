<?php
require_once __DIR__ . '/../Repository/UserRepository.php';
require_once __DIR__ . '/../Entity/UserEntity.php';

class AuthService
{
    public static function getAuthUser(): UserEntity
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if (!isset($_SESSION['user_id'])) {
            return new UserEntity([]);
        }

        $userRepo = new UserRepository();
        return $userRepo->getUserById($_SESSION['user_id']);
    }

    public static function login($username, $password)
    {
        $userRepo = new UserRepository();
        $user = $userRepo->getUserByUsername($username);

        if ($user && password_verify($password, $user->password_hash)) {
            $_SESSION['user_id'] = $user->id;
            return true;
        }

        return false;
    }

    public static function logout()
    {
        session_destroy();
    }

    public static function register(array $data): array
    {
        $userRepo = new UserRepository();

        if ($userRepo->getUserByUsername($data['username'])->id > 0) {
            return ['success' => false, 'error' => 'Ce nom d\'utilisateur est déjà pris.'];
        }
        if ($userRepo->getUserByEmail($data['email'])) {
            return ['success' => false, 'error' => 'Cet email est déjà utilisé.'];
        }

        $colors = ['blue', 'green', 'purple', 'orange', 'red', 'pink'];

        $id = $userRepo->createUser([
            'type' => 'Member',
            'firstname' => $data['firstname'],
            'lastname' => $data['lastname'],
            'username' => $data['username'],
            'email' => $data['email'],
            'password_hash' => password_hash($data['password'], PASSWORD_DEFAULT),
            'role' => 'User',
            'status' => 'Active',
            'avatar_color' => $colors[array_rand($colors)],
            'client_id' => null,
        ]);

        if ($id) {
            $_SESSION['user_id'] = $id;
            return ['success' => true];
        }

        return ['success' => false, 'error' => 'Une erreur est survenue lors de la création du compte.'];
    }
}