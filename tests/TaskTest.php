<?php


namespace Tests;

use PHPUnit\Framework\TestCase;
use TaskForce\actions\AvailableActions;
use TaskForce\actions\CancelAction;
use TaskForce\actions\DoneAction;
use TaskForce\actions\RefuseAction;
use TaskForce\actions\RespondAction;
use TaskForce\actions\TakeInWorkAction;
use TaskForce\classes\models\Task;
use TaskForce\classes\models\User;


class TaskTest extends TestCase
{
    /**
     * @test
     * @throws \Exception
     */
    public function testGetStatus()
    {
        $user = new User();
        $user->id = 1;
        $task = new Task($user);

        // проверка работы __get()
        $this->assertEquals($task->clientId, 1);

        $this->assertEquals($task->getNextStatus(CancelAction::class), Task::STATUS_CANCEL);
        $this->assertEquals($task->getNextStatus(RespondAction::class), Task::STATUS_HAS_RESPONSES);
        $this->assertEquals($task->getNextStatus(TakeInWorkAction::class), Task::STATUS_IN_WORK);
        $this->assertEquals($task->getNextStatus(DoneAction::class), Task::STATUS_DONE);
        $this->assertEquals($task->getNextStatus(RefuseAction::class), Task::STATUS_FAILED);

        // Установить статус заданию
        $task->setCurrentStatus(Task::STATUS_DONE);
        $this->assertEquals(Task::STATUS_DONE, $task->getCurrentStatus());
    }
}
