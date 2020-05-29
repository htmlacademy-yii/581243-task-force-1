<?php

namespace frontend\models;

use Yii;
use yii\base\Model;

class NewTaskForm extends Model
{
    public $name;
    public $description;
    public $category_id;
    public $files;
    public $address;
    public $budget;
    public $expire_at;
    public $lat;
    public $long;
    public $locality;
    public $city_id;

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            [['name', 'description', 'category_id'], 'required'],
            [['name', 'address'], 'string', 'max' => 255],
            [['description', 'lat', 'long', 'locality'], 'string'],
            [['files'], 'file', 'maxFiles' => 10],
            [['category_id', 'budget'], 'integer'],
            [['expire_at'], 'safe'],
            [['expire_at'], 'date', 'format' => 'php:Y-m-d'],
            ['address', 'validateCity'],
        ];
    }

    /**
     * @param string $attribute
     */
    public function validateCity(string $attribute): void
    {
        if (!$city = City::findOne((int)Yii::$app->user->identity->city_id)) {
            $this->addError('address', 'У вас не выбран город в настройках пользователя');

            return;
        }

        if (mb_strtolower($city->city, 'utf8') !== mb_strtolower($this->locality)) {
            $this->addError(
                'address',
                'Город в локации должен соответствовать вашему городу в настройках'
            );

            return;
        }

        $this->city_id = $city->id;
    }

    /**
     * @return array
     */
    public function attributeLabels(): array
    {
        return [
            'id' => 'ID',
            'name' => 'Мне нужно',
            'description' => 'Подробности задания',
            'category_id' => 'Категория',
            'files' => 'Файлы',
            'address' => 'Локация',
            'budget' => 'Бюджет',
            'expire_at' => 'Срок исполнения',
        ];
    }

    /**
     * @return array|null
     */
    public function upload(): ?array
    {
        if ($this->validate()) {
            $uploadFiles = File::uploadAttaches($this->files);
            if (count($this->files) === count($uploadFiles)) {
                return $uploadFiles;
            }
        }

        return null;
    }
}
