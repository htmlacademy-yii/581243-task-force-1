<?php


namespace TaskForce\classes\actions;


use frontend\models\Task;
use frontend\models\User;

/**
 * Class AbstractAction
 * @package TaskForce\classes\actions
 */
abstract class AbstractAction
{
    /**
     * @return string
     */
    abstract public static function getActionName(): string ;

    /**
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
