<?php


namespace TaskForce\classes\actions;


use TaskForce\classes\models\User;
use TaskForce\classes\models\UserRoles;
use TaskForce\classes\models\Task;

class CancelAction extends AbstractAction
{
    /**
     * @return string
     */
    public static function getActionName(): string
    {
        return 'Отменить';
    }

    /**
     * @return string
     */
    public static function getInnerName(): string
    {
        AvailableActions::ACTION_CANCEL;
    }

    /**
     * @param User $user
     * @param Task $task
     * @return bool
     */
    public static function checkRights(User $user, Task $task): bool
    {
        if ($user->role !== UserRoles::ROLE_CLIENT) {
            return false;
        }

        if ($task->currentStatus === Task::STATUS_NEW ||
            $task->currentStatus === Task::STATUS_HAS_RESPONSES) {
            return true;
        }

        return false;
    }
}
