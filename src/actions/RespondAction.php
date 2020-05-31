<?php

namespace TaskForce\actions;

use frontend\models\Status;
use frontend\models\Task;
use frontend\models\User;

/**
 * Class RespondAction
 * @package TaskForce\actions
 */
class RespondAction extends AbstractAction
{
    /**
     * @return string
     */
    public static function getActionName(): string
    {
        return 'Откликнуться';
    }

    /**
     * @return string
     */
    public static function getInnerName(): string
    {
        return AvailableActions::ACTION_RESPOND;
    }

    /**
     * @param User $user
     * @param Task $task
     * @return bool
     */
    public static function checkRights(User $user, Task $task): bool
    {
        if ($user->user_status !== User::ROLE_EXECUTOR) {
            return false;
        }

        if ($user->getReplies()->where(['task_id' => $task->id])->one()) {
            return false;
        }

        if ($task->task_status_id === Status::STATUS_NEW ||
            $task->task_status_id === Status::STATUS_HAS_RESPONSES) {
            return true;
        }

        return false;
    }
}
