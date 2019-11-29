<?php


namespace TaskForce\classes\models;


/**
 * Class User
 * @package TaskForce\classes\models
 */
class User extends Model
{
    protected $role;

    /**
     * @return int
     */
    public function getRole(): int
    {
        return $this->role;
    }

    /**
     * @param int $role
     * @return bool
     */
    public function setRole(int $role): bool
    {
        if (UserRoles::isRoleExist($role)) {
            $this->role = $role;
            return true;
        }

        return false;
    }

    // метод для тестов. id надо брать из базы
    public function setId(int $id)
    {
        $this->id = $id;
    }
}
