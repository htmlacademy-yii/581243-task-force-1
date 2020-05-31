<?php

namespace frontend\components;

use frontend\jobs\SendEmailJob;
use frontend\models\Event;
use frontend\models\Message;
use frontend\models\Opinion;
use frontend\models\Task;
use frontend\models\User;
use frontend\models\UserSettings;
use Yii;

class EventComponent
{
    /**
     * @param int $eventType
     * @param Task $task
     * @return Event|null
     */
    public function createTaskEvent(int $eventType, Task $task): ?Event
    {
        $user = $this->prepareUser($eventType, $task);

        return  $this->createEvent($user, $eventType, $task);
    }

    /**
     * @param int $eventType
     * @param Message $message
     * @return Event|null
     */
    public function createMessageEvent(int $eventType, Message $message): ?Event
    {
        if (!($task = Task::findOne($message->task_id))) {
            return null;
        }

        $user = User::findOne((int)$task->client_id === (int)$message->author_id ? $task->executor_id : $task->client_id);

        return  $this->createEvent($user, $eventType, $task);
    }

    /**
     * @param int $eventType
     * @param Opinion $opinion
     * @return Event|null
     */
    public function createOpinionEvent(int $eventType, Opinion $opinion): ?Event
    {
        if (!($task = Task::findOne($opinion->task_id))) {
            return null;
        }

        $user = User::findOne((int)$task->client_id === (int)$opinion->author_id ? $task->executor_id : $task->client_id);

        return  $this->createEvent($user, $eventType, $task);
    }

    /**
     * @param User $user
     * @param int $eventType
     * @param Task $task
     * @return Event|null
     */
    protected function createEvent(User $user, int $eventType, Task $task): ?Event
    {
        if (!$user) {
            return null;
        }

        $event = new Event([
            'user_id' => $user->id,
            'send_to_email' => $user->email,
            'type' => $eventType,
        ]);
        $event->message = $this->prepareMessage($eventType, $task);
        $event->subject = $this->prepareSubject($eventType);

        if ($event->validate() && $event->save()) {
            return $event;
        }

        return null;
    }

    /**
     * @param Event $event
     */
    public function send(Event $event): void
    {
        $userSettings = UserSettings::find()->where(['user_id' => $event->user_id])->one();

        $send = false;
        if ($userSettings && $event->send_to_email) {
            switch ($event->type) {
                case Event::NEW_REPLY:
                case Event::REFUSE:
                case Event::TAKE_IN_WORK:
                case Event::DONE:
                    if ($userSettings->task_action) {
                        $send = true;
                    }
                    break;
                case Event::NEW_MESSAGE:
                    if ($userSettings->new_messages) {
                        $send = true;
                    }
                    break;
                case Event::NEW_OPINION:
                    if ($userSettings->new_response) {
                        $send = true;
                    }
                    break;
            }
        }

        if ($send) {
            Yii::$app->queue->push(new SendEmailJob([
                'event' => $event,
            ]));
        }
    }

    /**
     * @param int $type
     * @param Task $task
     * @param Event|null $event
     * @return string|null
     */
    protected function prepareMessage(int $type, Task $task, Event $event = null): ?string
    {
        $taskLink = '<a href="' .
            Yii::$app->request->hostInfo . Yii::$app->params['task_view_url'] . $task->id .
            '">' . $task->name . '</a>';

        switch ($type) {
            case Event::NEW_REPLY:
                return "Новый отклик к заданию. \n $taskLink";
            case Event::NEW_MESSAGE:
                return "Новое сообщение в чате. \n $taskLink";
            case Event::REFUSE:
                return "Отказ от задания исполнителем. \n $taskLink";
            case Event::TAKE_IN_WORK:
                return "Старт задания. \n $taskLink";
            case Event::DONE:
                return "Завершение задания. \n $taskLink";
            case Event::NEW_OPINION:
                return "Новый отзыв. \n $taskLink";
        }

        return null;
    }

    /**
     * @param int $type
     * @param Task $task
     * @return User|null
     */
    protected function prepareUser(int $type, Task $task): ?User
    {
        switch ($type) {
            case Event::NEW_REPLY:
            case Event::REFUSE:
                return User::findOne($task->client_id);
            case Event::TAKE_IN_WORK:
            case Event::DONE:
                return User::findOne($task->executor_id);
        }

        return null;
    }

    /**
     * @param int $type
     * @return string|null
     */
    protected function prepareSubject(int $type): ?string
    {
        switch ($type) {
            case Event::NEW_REPLY:
                return 'Новый отклик к заданию';
            case Event::NEW_MESSAGE:
                return 'Новое сообщение в чате';
            case Event::REFUSE:
                return 'Отказ от задания исполнителем.';
            case Event::TAKE_IN_WORK:
                return 'Старт задания.';
            case Event::DONE:
                return 'Завершение задания.';
            case Event::NEW_OPINION:
                return 'Новый отзыв.';
        }

        return null;
    }
}
