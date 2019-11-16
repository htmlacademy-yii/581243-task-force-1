<?php


namespace Src\classes;


class Task
{
    /**
     * Статусы
     */
    CONST STATUS_NEW = 'Новое';
    CONST STATUS_CANCEL = 'Отменено';
    CONST STATUS_IN_WORK = 'На исполнении';
    CONST STATUS_DONE = 'Завершено';
    CONST STATUS_FAILED = 'Провалено';

    /**
     * Действия
     */
    CONST ACTION_NEW = 'Добавление задания';
    CONST ACTION_CANCEL = 'Отменить';
    CONST ACTION_RESPOND = 'Откликнуться';
    CONST ACTION_TAKE_IN_WORK = 'Принять';
    CONST ACTION_DONE = 'Завершение задания';
    CONST ACTION_REFUSE = 'Отказ от задания';

    /**
     * Роли
     */
    CONST ROLE_CLIENT = 'Заказчик';
    CONST ROLE_EXECUTOR = 'Исполнитель';

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
     * @return |null
     */
    public function __get(string $name)
    {
        if ($this->$name) {
            return $this->$name;
        }

        return null;
    }

    /**
     * @param string $name
     * @param $value
     * @return bool
     */
    public function __set(string $name, $value)
    {
        return false;
    }

    /**
     * @return array
     * @throws \ReflectionException
     */
    public static function getAllStatuses(): array
    {
        return static::getAllConstants('STATUS');
    }

    /**
     * @return array
     * @throws \ReflectionException
     */
    public static function getAllActions(): array
    {
        return static::getAllConstants('ACTION');
    }

    /**
     * @param bool $prefix
     * @return array
     * @throws \ReflectionException
     */
    public static function getAllConstants($prefix = false): array
    {
        $reflectionClass = new \ReflectionClass(static::class);

        if (!$prefix) {
            return $reflectionClass->getConstants();
        }

        $constants = [];
        foreach ($reflectionClass->getConstants() as $constantName => $constantValue) {
            if (strripos($constantName, $prefix) !== false) {
                $constants[$constantName] = $constantValue;
            }
        }

        return $constants;
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
                break;
            case self::ACTION_RESPOND:
                return null;
                break;
            case self::ACTION_TAKE_IN_WORK:
                return self::STATUS_IN_WORK;
                break;
            case self::ACTION_DONE:
                return self::STATUS_DONE;
                break;
            case self::ACTION_REFUSE:
                return self::STATUS_FAILED;
                break;
            default:
                throw new \Exception('action does not exist');
        }
    }
}
