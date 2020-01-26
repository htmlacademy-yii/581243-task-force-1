<?php


namespace TaskForce\classes\actions;


use frontend\models\Status;
use frontend\models\Task;
use frontend\models\User;

/**
 * Class CancelAction
 * @package TaskForce\classes\actions
 */
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
        return AvailableActions::ACTION_CANCEL;
    }

    /**
     * @param User $user
     * @param Task $task
     * @return bool
     */
    public static function checkRights(User $user, Task $task): bool
    {
        if ($user->id !== $task->client_id) {
            return false;
        }

        if ($task->task_status_id === Status::STATUS_NEW ||
            $task->task_status_id === Status::STATUS_HAS_RESPONSES) {
            return true;
        }

        return false;
    }
}
