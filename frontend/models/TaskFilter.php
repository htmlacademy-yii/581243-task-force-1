<?php

namespace frontend\models;

use yii\base\Model;

class TaskFilter extends Model
{
    public $categories = [];
    public $my_city;
    public $no_executor;
    public $no_address;
    public $date ;
    public $title;

    public function attributeLabels()
    {
        return [
            'categories' => 'Категории',
            'my_city' => 'Мой город',
            'no_executor' => 'Без исполнителя',
            'no_address' => 'Удаленная работа',
            'date' => 'Дата',
            'title' => 'Поиск по названию',
        ];
    }

    public function rules()
    {
        return [
            [['categories', 'my_city', 'no_executor', 'no_address', 'date', 'title'], 'safe']
        ];
    }
}
