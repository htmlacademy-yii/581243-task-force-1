<?php

namespace TaskForce\actions;

use frontend\models\Reply;
use frontend\models\Status;
use frontend\models\Task;
use frontend\models\User;

/**
 * Class RefuseAction
 * @package TaskForce\actions
 */
class RejectAction extends AbstractAction
{
    /**
     * @return string
     */
    public static function getActionName(): string
    {
        return 'Отклонить исполнителя';
    }

    /**
     * @return string
     */
    public static function getInnerName(): string
    {
        return AvailableActions::ACTION_REJECT;
    }

    /**
     * @param User $user
     * @param Task $task
     * @param Reply|null $reply
     * @return bool
     */
    public static function checkRights(User $user, Task $task, Reply $reply = null): bool
    {
        return $reply && ($user->id === $task->client_id) &&
            ($task->task_status_id === Status::STATUS_HAS_RESPONSES) &&
            (!$reply->rejected);
    }
}
