<?php


namespace TaskForce\classes\actions;


use TaskForce\classes\models\Task;
use TaskForce\classes\models\User;

class AvailableActions
{
    /**
     * Действия
     */
    CONST ACTION_ADD_NEW = 1;
    CONST ACTION_CANCEL = 2;
    CONST ACTION_RESPOND = 3;
    CONST ACTION_TAKE_IN_WORK = 4;
    CONST ACTION_DONE = 5;
    CONST ACTION_REFUSE = 6;

    protected $actions = [];

    /**
     * @return array
     */
    public static function getAllActions(): array
    {
        return [
            static::ACTION_RESPOND => RespondAction::class,
            static::ACTION_CANCEL => CancelAction::class,
            static::ACTION_TAKE_IN_WORK => TakeInWorkAction::class,
            static::ACTION_DONE => DoneAction::class,
            static::ACTION_REFUSE => RefuseAction::class,
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
                throw new \Exception('Action doesn\'t exist');
            }

            if ($action::checkRights($user, $task)) {
                $actions[] = $action;
            }
        }

        return $actions;
    }
}
