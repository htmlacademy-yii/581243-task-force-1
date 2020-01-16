<?php

namespace frontend\controllers;

use frontend\models\Category;
use frontend\models\NewTaskForm;
use frontend\models\Status;
use frontend\models\Task;
use frontend\models\TaskFilter;
use frontend\models\User;
use Yii;
use yii\web\Response;
use yii\web\UploadedFile;
use yii\widgets\ActiveForm;

class TaskController extends SecuredController
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

    public function actionCreate()
    {
        $user = User::findOne(\Yii::$app->user->getId()) ?? new User();
        if ($user->user_status !== User::author) {
            return $this->redirect('/task/');
        }

        $taskForm = new NewTaskForm();
        $categories = Category::find()->select('name')->indexBy('id')->column();
        $errors = [];

        if (Yii::$app->request->getIsPost()) {
            $taskForm->load(Yii::$app->request->post());
            //$taskForm->expire_at = date('Y-m-d H:i:s', strtotime($taskForm->expire_at));

            if ($taskForm->validate()) {
                $taskForm->files = UploadedFile::getInstances($taskForm, 'files');

                $task = new Task();
                $task->attributes = $taskForm->attributes;
                $task->client_id = $user->id;
                $task->task_status_id = Status::STATUS_NEW;

                $files = $taskForm->upload();

                if ($task->validate() && is_array($files) && $task->save()) {

                    foreach ($files as $file) {
                        $task->link('files', $file);
                    }

                    return $this->redirect('/task/');
                }
            } else {
                $errors = $taskForm->getErrors();
            }
        }

        return $this->render('create', [
            'taskForm' => $taskForm,
            'categories' => $categories,
            'errors' => $errors,
        ]);
    }
}
