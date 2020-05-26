<?php

namespace frontend\jobs;

use Exception;
use frontend\models\Event;
use Yii;
use yii\base\BaseObject;
use yii\queue\JobInterface;
use yii\queue\Queue;

class SendEmailJob  extends BaseObject implements JobInterface
{
    public $event;

    /**
     * @param Queue $queue
     * @return mixed|void
     */
    public function execute($queue): void
    {
        try {
            if (!($this->event instanceof Event)) {
                throw new Exception('Event must be instance of ' . Event::class);
            }

            if (Yii::$app
                ->mailer
                ->compose()
                ->setFrom([Yii::$app->params['supportEmail'] => Yii::$app->name . ' robot'])
                ->setTo($this->event->send_to_email)
                ->setSubject($this->event->subject)
                ->setTextBody($this->event->message)
                ->send()) {
                $this->event->send()->save();
            }
        } catch (Exception $e) {
            Yii::error($e->getMessage(), 'job');
        }
    }
}
