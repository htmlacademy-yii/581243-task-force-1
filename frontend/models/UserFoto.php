<?php

namespace frontend\models;

use Yii;

/**
 * This is the model class for table "user_foto".
 *
 * @property int $id
 * @property int $user_id
 * @property int $file_id
 */
class UserFoto extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'user_foto';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
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
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'file_id' => 'File ID',
        ];
    }
}
