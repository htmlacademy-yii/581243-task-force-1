<?php


namespace TaskForce\actions;


use frontend\models\Status;
use frontend\models\Task;
use frontend\models\User;

/**
 * Class TakeInWorkAction
 * @package TaskForce\actions
 */
class TakeInWorkAction extends AbstractAction
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
        return AvailableActions::ACTION_TAKE_IN_WORK;
    }

    /**
     * @param User $user
     * @param Task $task
     * @return bool
     */
    public static function checkRights(User $user, Task $task): bool
    {
        return ($user->id === $task->client_id) && ($task->task_status_id === Status::STATUS_HAS_RESPONSES);
    }
}
