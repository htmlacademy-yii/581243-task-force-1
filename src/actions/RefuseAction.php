<?php

namespace TaskForce\actions;

use frontend\models\Status;
use frontend\models\Task;
use frontend\models\User;

/**
 * Class RefuseAction
 * @package TaskForce\actions
 */
class RefuseAction extends AbstractAction
{
    /**
     * @return string
     */
    public static function getActionName(): string
    {
        return 'Отказаться';
    }

    /**
     * @return string
     */
    public static function getInnerName(): string
    {
        return AvailableActions::ACTION_REFUSE;
    }

    /**
     * @param User $user
     * @param Task $task
     * @return bool
     */
    public static function checkRights(User $user, Task $task): bool
    {
        return ($user->id === $task->executor_id) && ($task->task_status_id === Status::STATUS_IN_WORK);
    }
}
