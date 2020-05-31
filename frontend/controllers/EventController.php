<?php

namespace frontend\controllers;

use Carbon\Carbon;
use frontend\models\Event;
use Yii;
use yii\db\Exception;
use yii\db\Query;

class EventController extends SecuredController
{
    /**
     * @throws Exception
     */
    public function actionIndex(): void
    {
        (new Query)
            ->createCommand()
            ->update(Event::tableName(), ['view_feed_at' => Carbon::now()], 'view_feed_at IS NUll AND user_id = :id')
            ->bindValue(':id', Yii::$app->user->id)
            ->execute();
    }
}
