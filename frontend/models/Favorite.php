<?php

namespace frontend\models;

use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\db\BaseActiveRecord;

/**
 * This is the model class for table "favorites".
 *
 * @property int $id
 * @property int $user_id
 * @property int $favorite_user_id
 * @property string $created_at
 * @property string|null $updated_at
 */
class Favorite extends ActiveRecord
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
                    BaseActiveRecord::EVENT_BEFORE_INSERT => ['created_at'],
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
        return 'favorites';
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['user_id', 'favorite_user_id'], 'required'],
            [['user_id', 'favorite_user_id'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['favorite_user_id', 'user_id'], 'unique', 'targetAttribute' => ['favorite_user_id', 'user_id']],
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
            'favorite_user_id' => 'Favorite User ID',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }
}
