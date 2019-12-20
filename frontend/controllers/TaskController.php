<?php

namespace frontend\controllers;

use frontend\models\Category;
use frontend\models\Task;
use frontend\models\TaskFilter;
use yii\widgets\ActiveForm;

class TaskController extends \yii\web\Controller
{
    /**
     * @return string
     */
    public function actionIndex()
    {
        $taskFilter = new TaskFilter();
        $taskBuilder = Task::find()->where(['task_status_id' => 1]);
        $categories = Category::find()->all();

        if (\Yii::$app->request->getIsPost()) {
            $taskFilter->load(\Yii::$app->request->post());
            $taskBuilder = $taskFilter->filter($taskBuilder);
        } else {
            // если пользователь просто открыл страницу,
            // то применим начальные фильтры
            $taskFilter->date = 'year';
            $taskBuilder = $taskFilter->filter($taskBuilder);
        }

        return $this->render('index', [
            'tasks' => $taskBuilder->all(),
            'taskFilter' => $taskFilter,
            'categories' => $categories,
        ]);
    }

    /**
     * @param $id
     * @return string
     */
    public function actionShow($id)
    {
        $task = Task::findOne($id);
        $client = $task->client;
        $replies = $task->replies;

        return $this->render('view', [
            'task' => $task,
            'client' => $client,
            'replies' => $replies,
        ]);
    }
}
