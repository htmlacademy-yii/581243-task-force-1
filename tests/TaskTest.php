<?php


namespace Tests;

use PHPUnit\Framework\TestCase;
use Src\classes\Task;


class TaskTest extends TestCase
{
    /**
     * Статусы
     */
    //CONST STATUS_NEW = 'Новое';
    CONST STATUS_CANCEL = 'Отменено';
    CONST STATUS_IN_WORK = 'На исполнении';
    CONST STATUS_DONE = 'Завершено';
    CONST STATUS_FAILED = 'Провалено';

    /**
     * Действия
     */
    //CONST ACTION_NEW = 'Добавление задания';
    CONST ACTION_CANCEL = 'Отменить';
    CONST ACTION_RESPOND = 'Откликнуться';
    CONST ACTION_TAKE_IN_WORK = 'Принять';
    CONST ACTION_DONE = 'Завершение задания';
    CONST ACTION_REFUSE = 'Отказ от задания';

    public function testGetStatus()
    {
        $task = new Task(1);

        $this->assertEquals($task->getNextStatus(self::ACTION_CANCEL), self::STATUS_CANCEL);
        $this->assertEquals($task->getNextStatus(self::ACTION_RESPOND), false);
        $this->assertEquals($task->getNextStatus(self::ACTION_TAKE_IN_WORK), self::STATUS_IN_WORK);
        $this->assertEquals($task->getNextStatus(self::ACTION_DONE), self::STATUS_DONE);
        $this->assertEquals($task->getNextStatus(self::ACTION_REFUSE), self::STATUS_FAILED);
    }
}
