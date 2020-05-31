<?php

namespace frontend\models;

use yii\base\Model;

class TaskFilter extends Model
{
    public $categories = [];
    public $my_city;
    public $no_executor;
    public $no_address;
    public $date = Task::ALL;
    public $title;

    /**
     * @return array
     */
    public function attributeLabels(): array
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

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            [['categories', 'my_city', 'no_executor', 'no_address', 'date', 'title'], 'safe']
        ];
    }
}
