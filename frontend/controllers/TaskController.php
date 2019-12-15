<?php

namespace frontend\controllers;

use frontend\models\Task;

class TaskController extends \yii\web\Controller
{
    public function actionIndex()
    {
        $tasks = Task::find()->all();
        return $this->render('index', ['tasks' => $tasks]);
    }

}
