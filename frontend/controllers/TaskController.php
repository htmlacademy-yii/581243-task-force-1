<?php

namespace frontend\controllers;

use frontend\models\Category;
use frontend\models\Task;
use frontend\models\TaskFilter;
use yii\widgets\ActiveForm;

class TaskController extends \yii\web\Controller
{
    public function actionIndex()
    {
        $taskFilter = new TaskFilter();
        $taskBuilder = Task::find()->where(['task_status_id' => 1]);
        $categories = Category::find()->all();

        if (\Yii::$app->request->getIsPost()) {
            $taskFilter->load(\Yii::$app->request->post());
        } else {
            // если пользователь просто открыл страницу,
            // то применим начальные фильтры
            $taskFilter->date = 'year';
        }

        if (!is_array($taskFilter->categories)) {
            $taskFilter->categories = [];
        }

        $taskBuilder = Task::filter($taskBuilder, $taskFilter);

        return $this->render('index', [
            'tasks' => $taskBuilder->all(),
            'taskFilter' => $taskFilter,
            'categories' => $categories,
        ]);
    }
}
