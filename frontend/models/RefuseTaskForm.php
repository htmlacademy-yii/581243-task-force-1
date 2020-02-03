<?php

namespace frontend\models;

use yii\base\Model;

class RefuseTaskForm extends Model
{
    public $task_id;

    /**
     * @return array
     */
    public function rules()
    {
        return [
            [['task_id'], 'required'],
            [['task_id'], 'integer'],
        ];
    }
}
