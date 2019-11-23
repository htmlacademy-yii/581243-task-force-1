<?php


namespace TaskForce\classes\models;


class UserRoles
{
    /**
     * Роли
     */
    CONST ROLE_CLIENT = 12;
    CONST ROLE_EXECUTOR = 13;

    /**
     * @return array
     */
    static function getRoles(): array
    {
        return [
            static::ROLE_CLIENT,
            static::ROLE_EXECUTOR,
        ];
    }

    /**
     * Проверка существования роли
     * @param $role
     * @return bool
     */
    static function isRoleExist($role): bool
    {
        if (in_array($role, static::getRoles())) {
            return true;
        }

        return false;
    }
}
