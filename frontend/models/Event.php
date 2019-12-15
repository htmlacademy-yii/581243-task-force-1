<?php

namespace frontend\models;

use Yii;
use yii\db\ActiveQuery;

/**
 * This is the model class for table "events".
 *
 * @property int $id
 * @property int $user_id
 * @property string $mesage
 * @property string|null $send_to_email
 * @property string|null $send_email_at
 * @property string|null $view_feed_at
 */
class Event extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'events';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'mesage'], 'required'],
            [['user_id'], 'integer'],
            [['mesage'], 'string'],
            [['send_email_at', 'view_feed_at'], 'safe'],
            [['send_to_email'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'mesage' => 'Mesage',
            'send_to_email' => 'Send To Email',
            'send_email_at' => 'Send Email At',
            'view_feed_at' => 'View Feed At',
        ];
    }

    /**
     * @return ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }
}
