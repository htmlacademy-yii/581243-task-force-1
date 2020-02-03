<?php


namespace TaskForce\classes\actions;


use frontend\models\Status;
use frontend\models\Task;
use frontend\models\User;

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
        return AvailableActions::ACTION_DONE;
    }

    /**
     * @param User $user
     * @param Task $task
     * @return bool
     */
    public static function checkRights(User $user, Task $task): bool
    {
        return ($user->id === $task->client_id) && ($task->task_status_id === Status::STATUS_IN_WORK);
    }
}
