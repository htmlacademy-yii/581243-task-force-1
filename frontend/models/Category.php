<?php

namespace frontend\models;

/**
 * This is the model class for table "categories".
 *
 * @property int $id
 * @property string|null $name
 * @property string|null $icon
 */
class Category extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return 'categories';
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['name'], 'string', 'max' => 255],
            [['icon'], 'string', 'max' => 45],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'icon' => 'Icon',
        ];
    }
}
