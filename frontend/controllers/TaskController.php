<?php

namespace frontend\controllers;

use Exception;
use frontend\models\Category;
use frontend\models\DoneTaskForm;
use frontend\models\NewTaskForm;
use frontend\models\Opinion;
use frontend\models\RefuseTaskForm;
use frontend\models\Reply;
use frontend\models\Status;
use frontend\models\Task;
use frontend\models\TaskFilter;
use frontend\models\User;
use TaskForce\actions\AvailableActions;
use TaskForce\actions\DoneAction;
use TaskForce\actions\GetProblemAction;
use TaskForce\actions\RefuseAction;
use TaskForce\exceptions\ActionException;
use TaskForce\exceptions\StatusException;
use Yii;
use yii\web\Response;
use yii\web\UploadedFile;

class TaskController extends SecuredController
{
    /**
     * @return string
     */
    public function actionIndex()
    {
        $taskFilter = new TaskFilter();
        $taskBuilder = Task::find()->where(['task_status_id' => [1, 3]]);
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
     * @throws Exception
     */
    public function actionShow($id)
    {
        $task = Task::findOne($id);
        $client = $task->client;
        $replies = $task->replies;
        $user = Yii::$app->user->identity;
        $actions = AvailableActions::getNextAction($user, $task);
        $replyForm = new Reply();
        $doneTaskForm = new DoneTaskForm();
        $refuseTaskForm = new RefuseTaskForm();

        return $this->render('view', [
            'task' => $task,
            'client' => $client,
            'replies' => $replies,
            'user' => $user,
            'actions' => $actions,
            'replyForm' => $replyForm,
            'doneTaskForm' => $doneTaskForm,
            'refuseTaskForm' => $refuseTaskForm,
        ]);
    }

    /**
     * @return string|Response
     * @throws StatusException
     */
    public function actionCreate()
    {
        $user = Yii::$app->user->identity;
        if ($user->user_status !== User::CLIENT) {
            return $this->redirect('/task/');
        }

        $taskForm = new NewTaskForm();
        $categories = Category::find()->select('name')->indexBy('id')->column();
        $errors = [];

        if (Yii::$app->request->getIsPost()) {
            $taskForm->load(Yii::$app->request->post());

            if ($taskForm->validate()) {
                $taskForm->files = UploadedFile::getInstances($taskForm, 'files');

                $task = new Task();
                $task->attributes = $taskForm->attributes;
                $task->client_id = $user->id;
                $task->setCurrentStatus(Status::STATUS_NEW);

                $files = $taskForm->upload();

                $task->expire_at = $task->expire_at ? date('Y-m-d H:i:s', strtotime($task->expire_at)) : null;

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

    /**
     * @return Response
     * @throws StatusException
     * @throws ActionException
     */
    public function actionDone()
    {
        $user = Yii::$app->user->identity;

        $doneTaskForm = new DoneTaskForm();

        if (Yii::$app->request->getIsPost()) {
            $doneTaskForm->load(Yii::$app->request->post());
            $task = Task::findOne($doneTaskForm->task_id);

            if ($task && $doneTaskForm->validate()) {
                $opinion = new Opinion();
                $opinion->attributes = $doneTaskForm->attributes;
                $opinion->author_id = $user->id;
                $opinion->evaluated_user_id = $task->executor_id;

                if ($opinion->validate() &&
                    DoneAction::checkRights($user, $task) &&
                    GetProblemAction::checkRights($user, $task)) {
                    $opinion->save();

                    $action = $doneTaskForm->done === 'yes' ? DoneAction::getInnerName() : GetProblemAction::getInnerName();
                    $nextStatus = $task->getNextStatus($action);
                    $task->setCurrentStatus($nextStatus);
                    $task->save();
                }
            }
        }

        return $this->redirect(Yii::$app->request->referrer ?? '/task/');
    }

    /**
     * @return Response
     * @throws ActionException
     * @throws StatusException
     */
    public function actionRefuse()
    {
        $user = Yii::$app->user->identity;

        $refuseTaskForm = new RefuseTaskForm();

        if (Yii::$app->request->getIsPost()) {
            $refuseTaskForm->load(Yii::$app->request->post());
            $task = Task::findOne($refuseTaskForm->task_id);

            if ($task && $refuseTaskForm->validate() && RefuseAction::checkRights($user, $task)) {
                $nextStatus = $task->getNextStatus(RefuseAction::getInnerName());
                $task->setCurrentStatus($nextStatus);
                $task->save();
            }
        }

        return $this->redirect(Yii::$app->request->referrer ?? '/task/');
    }

    public function actionMylist($status = null)
    {
        $user = Yii::$app->user->identity;

        $taskBuilder = Task::find()->where(['client_id' => $user->id])
            ->orWhere(['executor_id' => $user->id]);

        switch ($status) {
            case Status::STATUS_DONE:
                $taskBuilder = $taskBuilder->andWhere(['task_status_id' => Status::STATUS_DONE]);
                break;
            case Status::STATUS_NEW:
                $taskBuilder = $taskBuilder->andWhere(['task_status_id' => Status::STATUS_NEW])
                    ->orWhere(['task_status_id' => Status::STATUS_HAS_RESPONSES]);
                break;
            case Status::STATUS_IN_WORK:
                $taskBuilder = $taskBuilder->andWhere(['task_status_id' => Status::STATUS_IN_WORK]);
                break;
            case Status::STATUS_CANCEL:
                $taskBuilder = $taskBuilder->andWhere(['task_status_id' => Status::STATUS_CANCEL])
                    ->orWhere(['task_status_id' => Status::STATUS_FAILED]);
                break;
            case Status::STATUS_EXPIRED:
                $taskBuilder = $taskBuilder->andWhere(['task_status_id' => Status::STATUS_IN_WORK])
                ->andWhere(['<', 'expire_at', date('Y-m-d 00:00:00')]);
                break;
        }

        return $this->render('mylist', [
            'tasks' => $taskBuilder->all(),
            'status' => $status,
        ]);
    }
}
