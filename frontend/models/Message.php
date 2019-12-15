<?php

namespace frontend\models;

use Yii;
use yii\db\ActiveQuery;

/**
 * This is the model class for table "messages".
 *
 * @property int $id
 * @property int $task_id
 * @property int $author_id
 * @property string $comment
 * @property string $created_at
 * @property string|null $updated_at
 */
class Message extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'messages';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['task_id', 'author_id', 'comment', 'created_at'], 'required'],
            [['task_id', 'author_id'], 'integer'],
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
            'author_id' => 'Author ID',
            'comment' => 'Comment',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
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
    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'author_id']);
    }
}
