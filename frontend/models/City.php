<?php

namespace frontend\models;

use yii\db\ActiveQuery;

/**
 * This is the model class for table "cities".
 *
 * @property int $id
 * @property string $city
 * @property string $lat
 * @property string $long
 */
class City extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return 'cities';
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['city', 'lat', 'long'], 'required'],
            [['city', 'lat', 'long'], 'string', 'max' => 255],
            [['city', 'long', 'lat'], 'unique', 'targetAttribute' => ['city', 'long', 'lat']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array
    {
        return [
            'id' => 'ID',
            'city' => 'City',
            'lat' => 'Lat',
            'long' => 'Long',
        ];
    }

    /**
     * @return ActiveQuery
     */
    public function getUsers(): ActiveQuery
    {
        return $this->hasMany(User::class, ['city_id' => 'id'])
            ->inverseOf('city');
    }
}
