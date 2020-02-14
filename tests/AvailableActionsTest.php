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
use TaskForce\classes\models\UserRoles;

class AvailableActionsTest extends TestCase
{
    /**
     * @test
     * @throws \Exception
     */
    public function testGetNextAction()
    {
        $client = new User();
        $client->role = UserRoles::ROLE_CLIENT;
        $client->id = 1;
        $executor = new User();
        $executor->role = UserRoles::ROLE_EXECUTOR;
        $executor->id = 2;

        $task = new Task($client);
        $this->assertEquals(AvailableActions::getNextAction($client, $task), [CancelAction::class]);
        $this->assertEquals(AvailableActions::getNextAction($executor, $task), [RespondAction::class]);

        $task->currentStatus = Task::STATUS_HAS_RESPONSES;
        $this->assertEquals(AvailableActions::getNextAction($client, $task), [
            CancelAction::class,
            TakeInWorkAction::class,
            ]);
        $this->assertEquals(AvailableActions::getNextAction($executor, $task), [RespondAction::class]);

        $task->currentStatus = Task::STATUS_CANCEL;
        $this->assertEquals(AvailableActions::getNextAction($client, $task), []);
        $this->assertEquals(AvailableActions::getNextAction($executor, $task), []);

        $task->currentStatus = Task::STATUS_IN_WORK;
        $this->assertEquals(AvailableActions::getNextAction($client, $task), [DoneAction::class]);
        $this->assertEquals(AvailableActions::getNextAction($executor, $task), [RefuseAction::class]);

        $task->currentStatus = Task::STATUS_DONE;
        $this->assertEquals(AvailableActions::getNextAction($client, $task), []);
        $this->assertEquals(AvailableActions::getNextAction($executor, $task), []);

        $task->currentStatus = Task::STATUS_FAILED;
        $this->assertEquals(AvailableActions::getNextAction($client, $task), []);
        $this->assertEquals(AvailableActions::getNextAction($executor, $task), []);
    }
}
