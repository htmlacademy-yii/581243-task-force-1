<?php

namespace frontend\models;

use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\db\BaseActiveRecord;

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
class UserSettings extends ActiveRecord
{
    /**
     * @return array
     */
    public function behaviors(): array
    {
        return [
            'timestamp' => [
                'class' => TimestampBehavior::className(),
                'attributes' => [
                    BaseActiveRecord::EVENT_BEFORE_INSERT => ['created_at', 'updated_at'],
                    BaseActiveRecord::EVENT_BEFORE_UPDATE => ['updated_at'],
                ],
                'value' => function(){
                    return gmdate("Y-m-d H:i:s");
                },
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return 'user_settings';
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['new_messages', 'task_action', 'new_response', 'show_only_client', 'hide_profile'], 'boolean'],
            [['user_id'], 'integer'],
            [['user_id'], 'required'],
            [['created_at', 'updated_at'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'new_messages' => 'New Messages',
            'task_action' => 'Task Action',
            'new_response' => 'New Response',
            'show_only_client' => 'Show Only Client',
            'hide_profile' => 'Hide Profile',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
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
     * @param User $user
     * @return UserSettings
     * @throws \Exception
     */
    public static function firstOrCreate(User $user): self
    {
        if ($model = $user->userSettings) {
            return $model;
        }

        $model = new static(['user_id' => $user->id]);

        if ($model->save()) {
            $user->link('userSettings', $model);

            return $model;
        }

        throw new \Exception('Failed to get settings.');
    }
}
