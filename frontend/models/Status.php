<?php

namespace frontend\models;

use Yii;
use yii\db\ActiveQuery;

/**
 * This is the model class for table "statuses".
 *
 * @property int $id
 * @property string|null $status
 */
class Status extends \yii\db\ActiveRecord
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

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'statuses';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['status'], 'string', 'max' => 255],
            [['status'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'status' => 'Status',
        ];
    }

    /**
     * @return ActiveQuery
     */
    public function getTasks() {
        return $this->hasMany(Task::class, ['task_status_id' => 'id'])
            ->inverseOf('status');
    }
}
