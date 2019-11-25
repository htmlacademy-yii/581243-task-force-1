<?php


namespace TaskForce\classes\models;


use TaskForce\classes\actions\AvailableActions;
use TaskForce\classes\actions\CancelAction;
use TaskForce\classes\actions\DoneAction;
use TaskForce\classes\actions\RefuseAction;
use TaskForce\classes\actions\RespondAction;
use TaskForce\classes\actions\TakeInWorkAction;

class Task extends Model
{
    /**
     * Статусы
     */
    CONST STATUS_NEW = 1;               // новое задание
    CONST STATUS_CANCEL = 2;            // исполнитель отменил
    const STATUS_HAS_RESPONSES = 3;     // есть отклики
    CONST STATUS_IN_WORK = 4;           // в работе
    CONST STATUS_DONE = 5;              // заказчик принимает работу
    CONST STATUS_FAILED = 6;            // исполнитель отказался

    protected $clientId;
    protected $executorId = null;
    protected $completionDate = null;
    protected $currentStatus;

    /**
     * Task constructor.
     * @param $clientId
     * @param null $completionDate
     */
    public function __construct(User $user, $completionDate = null)
    {
        $this->clientId = $user->id;
        $this->completionDate = $completionDate;
        $this->currentStatus = self::STATUS_NEW;
    }

    /**
     * @return int
     */
    public function getClientId()
    {
        return $this->clientId;
    }

    /**
     * @return int
     */
    public function getExecutorId()
    {
        return $this->executorId;
    }

    /**
     * @return mixed
     */
    public function getCompletionDate()
    {
        return $this->completionDate;
    }

    /**
     * @return int
     */
    public function getCurrentStatus()
    {
        return $this->currentStatus;
    }

    /**
     * @param int $status
     * @return bool
     * @throws \Exception
     */
    public function setCurrentStatus(int $status): bool
    {
        if (key_exists($status, $this->getAllStatuses())) {
            $this->currentStatus = $status;

            return true;
        }

        throw new \Exception('Status doesn\'t exist.');
    }

    /**
     * @return array
     */
    public function getAllStatuses(): array
    {
        return [
            self::STATUS_NEW => 'Новое',
            self::STATUS_CANCEL => 'Отменено',
            self::STATUS_HAS_RESPONSES => 'Есть отклики',
            self::STATUS_IN_WORK => 'На исполнении',
            self::STATUS_DONE => 'Завершено',
            self::STATUS_FAILED => 'Провалено',
        ];
    }

    /**
     * @param string $action
     * @return string|null
     * @throws \Exception
     */
    public function getNextStatus(string $action)
    {
        switch ($action) {
            case CancelAction::class:
                return self::STATUS_CANCEL;
            case RespondAction::class:
                return self::STATUS_HAS_RESPONSES;
            case TakeInWorkAction::class:
                return self::STATUS_IN_WORK;
            case DoneAction::class:
                return self::STATUS_DONE;
            case RefuseAction::class:
                return self::STATUS_FAILED;
            default:
                throw new \Exception('Action does not exist');
        }
    }
}
