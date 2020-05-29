<?php

namespace frontend\controllers;

use Exception;
use frontend\models\Category;
use frontend\models\DoneTaskForm;
use frontend\models\Event;
use frontend\models\NewTaskForm;
use frontend\models\RefuseTaskForm;
use frontend\models\Reply;
use frontend\models\Status;
use frontend\models\Task;
use frontend\models\TaskFilter;
use frontend\models\User;
use TaskForce\actions\AvailableActions;
use TaskForce\actions\RefuseAction;
use TaskForce\exceptions\ActionException;
use TaskForce\exceptions\StatusException;
use Yii;
use yii\data\ActiveDataProvider;
use yii\data\ArrayDataProvider;
use yii\helpers\Url;
use yii\web\Response;

class TaskController extends SecuredController
{
    /**
     * @return string
     */
    public function actionIndex(): string
    {
        $taskFilter = new TaskFilter();
        $taskBuilder = Task::find()->where(['task_status_id' => [1, 3]]);

        $taskFilter->load(Yii::$app->request->get());

        if (!is_array($taskFilter->categories)) {
            $taskFilter->categories = [];
        }

        $taskBuilder = Yii::$app->task->filter($taskBuilder, $taskFilter);

        $taskProvider = new ActiveDataProvider([
            'query' => $taskBuilder,
            'pagination' => [
                'pageSize' => 5,
            ],
            'sort' => [
                'defaultOrder' => [
                    'created_at' => SORT_DESC,
                ],
            ],
        ]);

        return $this->render('index', [
            'taskFilter' => $taskFilter,
            'categoriesProvider' => new ActiveDataProvider([
                'query' => Category::find(),
            ]),
            'taskProvider' => $taskProvider,
        ]);
    }

    /**
     * @param $id
     * @return string
     * @throws Exception
     */
    public function actionShow(int $id): string
    {
        $task = Task::findOne($id);
        $user = Yii::$app->user->identity;

        /**
         * Если для задания выбран исполнитель и страницу просматривает автор этого задания,
         * то карточка показывает исполнителя.
         */
        if (($task->task_status_id === Status::STATUS_IN_WORK) &&
            ($task->client_id === $user->id)) {
            $profile = $task->executor;
            $viewer = User::ROLE_EXECUTOR;
        } else {
            $profile = $task->client;
            $viewer = User::ROLE_CLIENT;
        }

        return $this->render('view', [
            'task' => $task,
            'profile' => $profile,
            'viewer' => $viewer,
            'replies' => $task->replies,
            'user' => $user,
            'actions' => AvailableActions::getNextAction($user, $task),
            'replyForm' => new Reply(),
            'doneTaskForm' => new DoneTaskForm(),
            'refuseTaskForm' => new RefuseTaskForm(),
        ]);
    }

    /**
     * @return string|Response
     */
    public function actionCreate()
    {
        $user = Yii::$app->user->identity;
        if ($user->user_status !== User::ROLE_CLIENT) {
            return $this->redirect(Url::to(['/task/']));
        }

        $taskForm = new NewTaskForm();
        $errors = [];

        if (Yii::$app->request->getIsPost()) {
            $taskForm->load(Yii::$app->request->post());

            if ($taskForm->validate() && Yii::$app->task->create($taskForm, $user)) {
                return $this->redirect(Url::to(['/task/']));
            } else {
                $errors = $taskForm->getErrors();
            }
        }

        return $this->render('create', [
            'taskForm' => $taskForm,
            'categories' => new ArrayDataProvider([
                'allModels' => Category::find()->select('name')->indexBy('id')->column(),
            ]),
            'errors' => $errors,
        ]);
    }

    /**
     * @return Response
     */
    public function actionDone(): Response
    {
        $user = Yii::$app->user->identity;

        if (Yii::$app->request->getIsPost()) {
            $doneTaskForm = new DoneTaskForm();
            $doneTaskForm->load(Yii::$app->request->post());
            $task = Task::findOne($doneTaskForm->task_id);

            if ($task && $doneTaskForm->validate()) {
                Yii::$app->task->done($doneTaskForm, $task, $user);
            }
        }

        return $this->redirect(Yii::$app->request->referrer ?? Url::to(['/task/']));
    }

    /**
     * @return Response
     * @throws ActionException
     * @throws StatusException
     */
    public function actionRefuse(): Response
    {
        $user = Yii::$app->user->identity;

        if (Yii::$app->request->getIsPost()) {
            $refuseTaskForm = new RefuseTaskForm();
            $refuseTaskForm->load(Yii::$app->request->post());
            $task = Task::findOne($refuseTaskForm->task_id);

            if ($task && $refuseTaskForm->validate() && RefuseAction::checkRights($user, $task)) {
                $nextStatus = $task->getNextStatus(RefuseAction::getInnerName());
                $task->setCurrentStatus($nextStatus);
                $task->save();

                if ($event = Yii::$app->event->createTaskEvent(Event::REFUSE, $task)) {
                    Yii::$app->event->send($event);
                }
            }
        }

        return $this->redirect(Yii::$app->request->referrer ?? Url::to(['/task/']));
    }

    /**
     * @param int|null $status
     * @return string
     */
    public function actionMylist(int $status = null): string
    {
        $user = Yii::$app->user->identity;

        $dataProvider = new ActiveDataProvider([
            'query' => Yii::$app->task->userList($user, $status),
            'sort' => [
                'defaultOrder' => [
                    'created_at' => SORT_DESC,
                ],
            ],
        ]);

        return $this->render('mylist', [
            'dataProvider' => $dataProvider,
            'status' => $status,
        ]);
    }
}
