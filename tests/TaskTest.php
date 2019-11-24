<?php


namespace Tests;

use PHPUnit\Framework\TestCase;
use TaskForce\classes\Task;


class TaskTest extends TestCase
{
    public function testGetStatus()
    {
        $task = new Task(1);

        // проверка работы __get()
        $this->assertEquals($task->clientId, 1);

        // Установить статус заданию
        $task->setCurrentStatus(Task::STATUS_DONE);
        $this->assertEquals(Task::STATUS_DONE, $task->getCurrentStatus());

        $this->assertEquals($task->getNextStatus(Task::ACTION_CANCEL), Task::STATUS_CANCEL);
        $this->assertEquals($task->getNextStatus(Task::ACTION_RESPOND), false);
        $this->assertEquals($task->getNextStatus(Task::ACTION_TAKE_IN_WORK), Task::STATUS_IN_WORK);
        $this->assertEquals($task->getNextStatus(Task::ACTION_DONE), Task::STATUS_DONE);
        $this->assertEquals($task->getNextStatus(Task::ACTION_REFUSE), Task::STATUS_FAILED);
    }
}
