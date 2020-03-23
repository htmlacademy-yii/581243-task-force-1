<?php

namespace frontend\controllers;

use frontend\models\Reply;
use frontend\models\Task;
use frontend\models\User;
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
    public function actionCreate()
    {
        $user = Yii::$app->user->identity;
        $replyForm = new Reply();

        if (Yii::$app->request->getIsPost()) {
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
            }
        }

        return $this->redirect(Yii::$app->request->referrer ?? Url::to(['/task/']));
    }

    public function actionReject($taskId, $replyId)
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
     * @param $taskId
     * @param $replyId
     * @return Response
     * @throws ActionException
     * @throws StatusException
     */
    public function actionTakeInWork($taskId, $replyId)
    {
        $user = Yii::$app->user->identity;
        $task = Task::findOne($taskId);
        $reply = Reply::findOne($replyId);

        if ($task && $reply && TakeInWorkAction::checkRights($user, $task)) {
            $nextStatus = $task->getNextStatus(TakeInWorkAction::getInnerName());
            $task->setCurrentStatus($nextStatus);
            $task->executor_id = $reply->executor_id;
            $task->save();
        }

        return $this->redirect(Yii::$app->request->referrer ?? Url::to(['/task/']));
    }
}
