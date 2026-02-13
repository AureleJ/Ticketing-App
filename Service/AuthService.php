<?php
require_once __DIR__ . '/../Repository/UserRepository.php';
require_once __DIR__ . '/../Entity/UserEntity.php';

class AuthService
{
    private const MOCK_LOGGED_ID = 1; 

    public static function getAuthUser(): UserEntity
    {
        $userRepo = new UserRepository();
        $user = $userRepo->findById(self::MOCK_LOGGED_ID);

        if (!$user) {
            return new UserEntity([
                'id' => 0,
                'firstname' => 'Invité', 
                'lastname' => '', 
                'role' => 'Guest',
                'avatar_color' => 'gray'
            ]);
        }

        return $user;
    }

    public static function isOwner(int $userId): bool 
    {
        return self::MOCK_LOGGED_ID === $userId;
    }
}