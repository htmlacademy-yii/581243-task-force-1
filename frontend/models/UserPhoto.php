<?php

namespace frontend\models;

use yii\db\ActiveRecord;

/**
 * This is the model class for table "user_photo".
 *
 * @property int $id
 * @property int $user_id
 * @property int $file_id
 */
class UserPhoto extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return 'user_photo';
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['user_id', 'file_id'], 'required'],
            [['user_id', 'file_id'], 'integer'],
            [['user_id', 'file_id'], 'unique', 'targetAttribute' => ['user_id', 'file_id']],
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
            'file_id' => 'File ID',
        ];
    }
}
