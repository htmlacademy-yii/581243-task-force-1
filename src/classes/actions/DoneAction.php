<?php


namespace TaskForce\classes\actions;


use TaskForce\classes\models\User;
use TaskForce\classes\models\UserRoles;
use TaskForce\classes\models\Task;

/**
 * Class DoneAction
 * @package TaskForce\classes\actions
 */
class DoneAction extends AbstractAction
{
    /**
     * @return string
     */
    public static function getActionName(): string
    {
        return 'Принять';
    }

    /**
     * @return string
     */
    public static function getInnerName(): string
    {
        AvailableActions::ACTION_DONE;
    }

    /**
     * @param User $user
     * @param Task $task
     * @return bool
     */
    public static function checkRights(User $user, Task $task): bool
    {
        return ($user->role === UserRoles::ROLE_CLIENT) && ($task->currentStatus === Task::STATUS_IN_WORK);
    }
}
