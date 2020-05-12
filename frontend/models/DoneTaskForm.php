<?php

namespace frontend\models;

use yii\base\Model;

class DoneTaskForm extends Model
{
    public $done = '';
    public $comment;
    public $rate;
    public $task_id;

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            [['done', 'task_id'], 'required'],
            [['comment', 'done'], 'string'],
            [['rate', 'task_id'], 'integer'],
            [['done', 'comment', 'rate', 'task_id'], 'safe'],
        ];
    }

    /**
     * @return array
     */
    public function attributeLabels(): array
    {
        return [
            'done' => 'Статус задания',
            'comment' => 'Комментарий',
            'rate' => 'Оценка',
        ];
    }
}
