<?php

namespace frontend\modules\api\actions;

use Yii;
use yii\rest\Action;

class MessageViewAction extends Action
{
    public function run($id)
    {
        $user = Yii::$app->user->identity;

        $model = $this->findModel($id);
        if ($this->checkAccess) {
            call_user_func($this->checkAccess, $this->id, $model);
        }

        $messages = [];
        foreach ($model->messages as $message) {
            $messages[] = [
                'message' => $message->comment,
                'published_at' => $message->created_at,
                'is_mine' => (int)$message->author_id === $user->id,
            ];
        }

        return $messages;
    }
}
