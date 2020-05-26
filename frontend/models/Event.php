<?php

namespace frontend\models;

use Carbon\Carbon;
use yii\db\ActiveQuery;

/**
 * This is the model class for table "events".
 *
 * @property int $id
 * @property int $user_id
 * @property string $message
 * @property string|null $send_to_email
 * @property string|null $send_email_at
 * @property string|null $view_feed_at
 */
class Event extends \yii\db\ActiveRecord
{
    const NEW_REPLY = 1;
    const NEW_MESSAGE = 2;
    const REFUSE = 3;
    const TAKE_IN_WORK = 4;
    const DONE = 5;
    const NEW_OPINION = 6;

    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return 'events';
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['user_id', 'message'], 'required'],
            [['user_id'], 'integer'],
            [['message'], 'string'],
            [['send_email_at', 'view_feed_at'], 'safe'],
            [['send_to_email', 'subject'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels():array
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'message' => 'Message',
            'send_to_email' => 'Send To Email',
            'send_email_at' => 'Send Email At',
            'view_feed_at' => 'View Feed At',
            'subject' => 'Subject',
        ];
    }

    /**
     * @return ActiveQuery
     */
    public function getUser(): ActiveQuery
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }

    /**
     * @return $this
     */
    public function send(): self
    {
        $this->send_email_at = Carbon::now();

        return $this;
    }

    /**
     * @return $this
     */
    public function view(): self
    {
        $this->view_feed_at = Carbon::now();

        return $this;
    }

    /**
     * @param int $type
     * @param Task|null $task
     * @return $this
     */
    protected function createMessage(int $type, Task $task = null): self
    {
        $this->message = $this->prepareMessage($type, $task);

        return $this;
    }

    /**
     * @param int $type
     * @return $this
     */
    protected function createSubject(int $type): self
    {
        $this->subject = $this->prepareMessage($type);

        return $this;
    }
}
