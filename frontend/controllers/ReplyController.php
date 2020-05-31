<?php

namespace frontend\controllers;

use frontend\models\Event;
use frontend\models\Reply;
use frontend\models\Task;
use TaskForce\actions\RejectAction;
use TaskForce\actions\RespondAction;
use TaskForce\actions\TakeInWorkAction;
use TaskForce\exceptions\ActionException;
use TaskForce\exceptions\StatusException;
use Yii;
use yii\helpers\Url;
use yii\web\Response;

class ReplyController extends SecuredController
{
    /**
     * @return Response
     * @throws ActionException
     * @throws StatusException
     */
    public function actionCreate(): Response
    {
        $user = Yii::$app->user->identity;

        if (Yii::$app->request->getIsPost()) {
            $replyForm = new Reply();
            $replyForm->load(Yii::$app->request->post());
            $replyForm->executor_id = $user->id;
            $task = Task::findOne($replyForm->task_id);

            if ($task &&
                RespondAction::checkRights($user, $task) &&
                $replyForm->validate() &&
                $replyForm->save()) {
                $nextStatus = $task->getNextStatus(RespondAction::getInnerName());
                $task->setCurrentStatus($nextStatus);
                $task->save();

                if ($event = Yii::$app->event->createTaskEvent(Event::NEW_REPLY, $task)) {
                    Yii::$app->event->send($event);
                }
            }
        }

        return $this->redirect(Yii::$app->request->referrer ?? Url::to(['/task/']));
    }

    /**
     * @param int $taskId
     * @param int $replyId
     * @return Response
     * @throws ActionException
     * @throws StatusException
     */
    public function actionReject(int $taskId, int $replyId): Response
    {
        $user = Yii::$app->user->identity;
        $task = Task::findOne($taskId);
        $reply = Reply::findOne($replyId);

        if ($task && $reply && RejectAction::checkRights($user, $task, $reply)) {
            $reply->rejected = true;
            $reply->save();
            $nextStatus = $task->getNextStatus(RejectAction::getInnerName());
            $task->setCurrentStatus($nextStatus);
            $task->save();
        }

        return $this->redirect(Yii::$app->request->referrer ?? Url::to(['/task/']));
    }

    /**
     * @param int $taskId
     * @param int $replyId
     * @return Response
     * @throws ActionException
     * @throws StatusException
     */
    public function actionTakeInWork(int $taskId, int $replyId): Response
    {
        $user = Yii::$app->user->identity;
        $task = Task::findOne($taskId);
        $reply = Reply::findOne($replyId);

        if ($task && $reply && TakeInWorkAction::checkRights($user, $task)) {
            $nextStatus = $task->getNextStatus(TakeInWorkAction::getInnerName());
            $task->setCurrentStatus($nextStatus);
            $task->executor_id = $reply->executor_id;
            $task->save();

            if ($event = Yii::$app->event->createTaskEvent(Event::TAKE_IN_WORK, $task)) {
                Yii::$app->event->send($event);
            }
        }

        return $this->redirect(Yii::$app->request->referrer ?? Url::to(['/task/']));
    }
}
