<?php


namespace TaskForce\classes;


class Task
{
    /**
     * Статусы
     */
    CONST STATUS_NEW = 1;
    CONST STATUS_CANCEL = 2;
    CONST STATUS_IN_WORK = 3;
    CONST STATUS_DONE = 4;
    CONST STATUS_FAILED = 5;

    /**
     * Действия
     */
    CONST ACTION_NEW = 6;
    CONST ACTION_CANCEL = 7;
    CONST ACTION_RESPOND = 8;
    CONST ACTION_TAKE_IN_WORK = 9;
    CONST ACTION_DONE = 10;
    CONST ACTION_REFUSE = 11;

    /**
     * Роли
     */
    CONST ROLE_CLIENT = 12;
    CONST ROLE_EXECUTOR = 13;

    protected $clientId;
    protected $executorId = null;
    protected $completionDate = null;
    protected $currentStatus;

    /**
     * Task constructor.
     * @param $clientId
     * @param null $completionDate
     */
    public function __construct($clientId, $completionDate = null)
    {
        $this->clientId = $clientId;
        $this->completionDate = $completionDate;
        $this->currentStatus = self::STATUS_NEW;
    }

    /**
     * @param string $name
     * @return mixed
     * @throws \Exception
     */
    public function __get(string $name)
    {
        $getter = 'get' . ucfirst($name);
        if (method_exists($this, $getter)) {
            return $this->$getter();
        } elseif (method_exists($this, 'set' . ucfirst($name))) {
            throw new \Exception('Getting write-only property: ' . get_class($this) . '::'. $name);
        } else {
            throw new \Exception('Getting unknown property: ' . get_class($this) . '::'. $name);
        }
    }

    /**
     * @param string $name
     * @param $value
     * @return mixed
     * @throws \Exception
     */
    public function __set(string $name, $value)
    {
        $setter = 'set' . ucfirst($name);
        if (method_exists($this, $setter)) {
            return $this->$setter();

        } elseif (method_exists($this, 'get' . ucfirst($name))) {
            throw new \Exception('Setting read-only property: ' . get_class($this) . '::'. $name);
        } else {
            throw new \Exception('Setting unknown property: ' . get_class($this) . '::'. $name);
        }
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
     * @param int $currentStatus
     * @return bool
     */
    public function setCurrentStatus(int $currentStatus): bool
    {
        $this->currentStatus = $currentStatus;

        return true;
    }

    /**
     * @return array
     */
    public function getAllStatuses(): array
    {
        return [
            self::STATUS_NEW => 'Новое',
            self::STATUS_CANCEL => 'Отменено',
            self::STATUS_IN_WORK => 'На исполнении',
            self::STATUS_DONE => 'Завершено',
            self::STATUS_FAILED => 'Провалено',
        ];
    }

    /**
     * @return array
     */
    public static function getAllActions(): array
    {
        return [
            self::ACTION_NEW => 'Добавление задания',
            self::ACTION_CANCEL => 'Отменить',
            self::ACTION_RESPOND => 'Откликнуться',
            self::ACTION_TAKE_IN_WORK => 'Принять',
            self::ACTION_DONE => 'Завершение задания',
            self::ACTION_REFUSE => 'Отказ от задания',
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
            case self::ACTION_CANCEL:
                return self::STATUS_CANCEL;
            case self::ACTION_RESPOND:
                return null;
            case self::ACTION_TAKE_IN_WORK:
                return self::STATUS_IN_WORK;
            case self::ACTION_DONE:
                return self::STATUS_DONE;
            case self::ACTION_REFUSE:
                return self::STATUS_FAILED;
            default:
                throw new \Exception('Action does not exist');
        }
    }
}
