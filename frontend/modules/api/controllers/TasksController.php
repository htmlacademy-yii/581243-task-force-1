<?php

namespace frontend\modules\api\controllers;

use frontend\models\Message;
use frontend\modules\api\actions\TaskIndexAction;
use yii\rest\ActiveController;

class TasksController extends ActiveController
{
    public $modelClass = Message::class;

    public function actions(): array
    {
        $actions = [
            'index' => [
                'class' => TaskIndexAction::class,
                'modelClass' => $this->modelClass,
                'checkAccess' => [$this, 'checkAccess'],
            ],
        ];

        return array_merge(parent::actions(), $actions);
    }
}
