<?php


namespace TaskForce\classes\actions;


use TaskForce\classes\models\User;
use TaskForce\classes\models\Task;

abstract class AbstractAction
{
    /**
     * TODO Надо вернуть название действия, как оно будет выводиться на фронте?
     * @return string
     */
    abstract public static function getActionName(): string ;

    /**
     * TODO Я не понял, зачем это надо. И скорее всего неверно реализовал
     * @return string
     */
    abstract public static function getInnerName(): string ;

    /**
     * @param User $user
     * @param Task $task
     * @return bool
     */
    abstract public static function checkRights(User $user, Task $task): bool ;
}
