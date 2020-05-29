<?php

namespace frontend\modules\api\controllers;

use frontend\models\Message;
use frontend\models\Task;
use frontend\modules\api\actions\MessageCreateAction;
use frontend\modules\api\actions\MessageViewAction;
use yii\rest\ActiveController;

class MessagesController extends ActiveController
{
    public $modelClass = Message::class;

    /**
     * @return array
     */
    public function actions(): array
    {
        $actions = [
            'view' => [
                'class' => MessageViewAction::class,
                'modelClass' => Task::class,
                'checkAccess' => [$this, 'checkAccess'],
            ],
            'create' => [
                'class' => MessageCreateAction::class,
                'modelClass' => $this->modelClass,
                'checkAccess' => [$this, 'checkAccess'],
            ],
        ];

        return array_merge(parent::actions(), $actions);
    }
}
