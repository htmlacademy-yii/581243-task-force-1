<?php

namespace frontend\components;

use frontend\models\DoneTaskForm;
use frontend\models\Event;
use frontend\models\NewTaskForm;
use frontend\models\Opinion;
use frontend\models\Status;
use frontend\models\Task;
use frontend\models\User;
use TaskForce\actions\DoneAction;
use TaskForce\actions\GetProblemAction;
use Yii;
use yii\db\ActiveQuery;
use yii\web\UploadedFile;

class TaskComponent
{
    public function create(NewTaskForm $taskForm, User $user): bool
    {
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

            return true;
        }

        return false;
    }

    public function done(DoneTaskForm $doneTaskForm, Task $task, User $user): void
    {
        $opinion = new Opinion();
        $opinion->attributes = $doneTaskForm->attributes;
        $opinion->author_id = $user->id;
        $opinion->evaluated_user_id = $task->executor_id;

        if ($opinion->validate() &&
            DoneAction::checkRights($user, $task) &&
            GetProblemAction::checkRights($user, $task)) {
            $opinion->save();

            if ($event = Yii::$app->event->createOpinionEvent(Event::NEW_OPINION, $opinion)) {
                Yii::$app->event->send($event);
            }
            if ($event = Yii::$app->event->createTaskEvent(Event::DONE, $task)) {
                Yii::$app->event->send($event);
            }

            $action = $doneTaskForm->done === 'yes' ? DoneAction::getInnerName() : GetProblemAction::getInnerName();
            $nextStatus = $task->getNextStatus($action);
            $task->setCurrentStatus($nextStatus);
            $task->save();
        }
    }

    /**
     * @param User $user
     * @param int $status
     * @return ActiveQuery
     */
    public function userList(User $user, int $status = null): ActiveQuery
    {
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

        return $taskBuilder;
    }
}
