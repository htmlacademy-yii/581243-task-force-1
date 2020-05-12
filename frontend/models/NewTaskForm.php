<?php

namespace frontend\models;

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

    public function rules(): array
    {
        return [
            [['name', 'description', 'category_id'], 'required'],
            [['name', 'address'], 'string', 'max' => 255],
            [['description', 'lat', 'long'], 'string'],
            [['files'], 'file', 'maxFiles' => 10],
            [['category_id', 'budget'], 'integer'],
            [['expire_at'], 'safe'],
            [['expire_at'], 'date', 'format' => 'php:Y-m-d'],
        ];
    }

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
