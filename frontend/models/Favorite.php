<?php

namespace frontend\models;

use Yii;

/**
 * This is the model class for table "favorirites".
 *
 * @property int $id
 * @property int $user_id
 * @property int $favorite_user_id
 * @property string $created_at
 * @property string|null $updated_at
 */
class Favorite extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'favorites';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'favorite_user_id', 'created_at'], 'required'],
            [['user_id', 'favorite_user_id'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['favorite_user_id', 'user_id'], 'unique', 'targetAttribute' => ['favorite_user_id', 'user_id']],
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
            'favorite_user_id' => 'Favorite User ID',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }
}
