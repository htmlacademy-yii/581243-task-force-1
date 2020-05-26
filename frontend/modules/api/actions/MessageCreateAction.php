<?php

namespace frontend\modules\api\actions;

use frontend\models\Event;
use Yii;
use yii\base\Model;
use yii\rest\Action;
use yii\web\Response;
use yii\web\ServerErrorHttpException;

class MessageCreateAction extends Action
{
    public $scenario = Model::SCENARIO_DEFAULT;

    public function run(int $id): array
    {
        $user = Yii::$app->user->identity;

        try {
            if (!$user) {
                throw new ServerErrorHttpException('Not authorize.');
            }

            if ($this->checkAccess) {
                call_user_func($this->checkAccess, $this->id);
            }

            /* @var $model \yii\db\ActiveRecord */
            $model = new $this->modelClass([
                'scenario' => $this->scenario,
            ]);

            $body = json_decode(Yii::$app->getRequest()->getRawBody(), true);

            $model->task_id = $id;
            $model->author_id = $user->id;
            $model->comment = $body['message'] ?? null;

            if ($model->save()) {
                $response = Yii::$app->getResponse();
                $response->format = Response::FORMAT_JSON;
                $response->setStatusCode(201);

                if ($event = Yii::$app->event->createMessageEvent(Event::NEW_MESSAGE, $model)) {
                    Yii::$app->event->send($event);
                }

                return [
                    'message' => $model->comment,
                    'published_at' => $model->created_at,
                    'is_mine' => (int)$model->author_id === $user->id,
                ];
            } elseif (!$model->hasErrors()) {
                throw new ServerErrorHttpException('Failed to create the object for unknown reason.');
            }
        } catch (ServerErrorHttpException $e) {
            Yii::error($e->getMessage(), 'api');
            return [];
        }
    }
}
