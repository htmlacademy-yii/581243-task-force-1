<?php

namespace frontend\models;

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
            [['send_to_email'], 'string', 'max' => 255],
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
        ];
    }

    /**
     * @return ActiveQuery
     */
    public function getUser(): ActiveQuery
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }
}
