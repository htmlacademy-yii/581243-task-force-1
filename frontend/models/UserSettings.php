<?php

namespace frontend\models;

use Yii;
use yii\db\ActiveQuery;

/**
 * This is the model class for table "user_settings".
 *
 * @property int $id
 * @property int|null $user_id
 * @property int|null $new_messages
 * @property int|null $task_action
 * @property int|null $new_response
 * @property int|null $profile_access
 * @property string $created_at
 * @property string $updated_at
 */
class UserSettings extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'user_settings';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'new_messages', 'task_action', 'new_response', 'profile_access'], 'integer'],
            [['created_at', 'updated_at'], 'required'],
            [['created_at', 'updated_at'], 'safe'],
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
            'new_messages' => 'New Messages',
            'task_action' => 'Task Action',
            'new_response' => 'New Response',
            'profile_access' => 'Profile Access',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
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
