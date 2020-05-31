<?php

namespace TaskForce\actions;

use frontend\models\Task;
use frontend\models\User;
use TaskForce\exceptions\ActionException;

/**
 * Class AvailableActions
 * @package TaskForce\actions
 */
class AvailableActions
{
    /**
     * Действия
     */
    CONST ACTION_ADD_NEW = 1;
    CONST ACTION_CANCEL = 2;
    CONST ACTION_RESPOND = 3;
    CONST ACTION_REJECT = 7;
    CONST ACTION_TAKE_IN_WORK = 4;
    CONST ACTION_DONE = 5;
    CONST ACTION_REFUSE = 6;
    CONST ACTION_GET_PROBLEM = 8;

    protected $actions = [];

    /**
     * @return array
     */
    public static function getAllActions(): array
    {
        return [
            static::ACTION_RESPOND => RespondAction::class,
            static::ACTION_CANCEL => CancelAction::class,
            static::ACTION_REJECT => RejectAction::class,
            static::ACTION_TAKE_IN_WORK => TakeInWorkAction::class,
            static::ACTION_DONE => DoneAction::class,
            static::ACTION_REFUSE => RefuseAction::class,
            static::ACTION_GET_PROBLEM => GetProblemAction::class,
        ];
    }

    /**
     * @param User $user
     * @param Task $task
     * @return array
     * @throws \Exception
     */
    public static function getNextAction(User $user, Task $task): array
    {
        $actions = [];
        foreach (static::getAllActions() as $action) {
            if (!class_exists($action)) {
                throw new ActionException('Action doesn\'t exist');
            }

            if ($action::checkRights($user, $task)) {
                $actions[] = $action::getInnerName();
            }
        }

        return $actions;
    }
}
