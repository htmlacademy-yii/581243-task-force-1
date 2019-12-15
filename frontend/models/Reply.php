<?php

namespace frontend\models;

use Yii;
use yii\db\ActiveQuery;

/**
 * This is the model class for table "replies".
 *
 * @property int $id
 * @property int $task_id
 * @property int|null $price
 * @property string|null $comment
 * @property string $created_at
 * @property string|null $updated_at
 * @property int $executor_id
 */
class Reply extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'replies';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['task_id', 'created_at', 'executor_id'], 'required'],
            [['task_id', 'price', 'executor_id'], 'integer'],
            [['comment'], 'string'],
            [['created_at', 'updated_at'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'task_id' => 'Task ID',
            'price' => 'Price',
            'comment' => 'Comment',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'executor_id' => 'Executor ID',
        ];
    }

    /**
     * @return ActiveQuery
     */
    public function getTask()
    {
        return $this->hasOne(Task::class, ['id' => 'task_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getExecutor()
    {
        return $this->hasOne(User::class, ['id' => 'executor_id']);
    }
}
