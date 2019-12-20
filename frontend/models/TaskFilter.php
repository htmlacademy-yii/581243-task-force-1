<?php


namespace frontend\models;


use yii\base\Model;
use yii\db\ActiveQuery;

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

    public function filter(ActiveQuery $taskBuilder): ActiveQuery
    {
        if (!empty($ids = $this->categories)) {
            $taskBuilder = $taskBuilder->andWhere(['in', 'category_id', $ids]);
        } else {
            $this->categories = [];
        }

        if ($this->my_city) {
            // нужна аутентификация пользователя
        }

        if ($this->no_executor) {
            $taskBuilder->andWhere(['executor_id' => NULL]);
        }
        if ($this->no_address) {
            $taskBuilder->andWhere(['address' => NULL]);
        }

        switch ($this->date) {
            case 'day':
                $taskBuilder = $taskBuilder->andWhere(['>=', 'created_at', date('Y-m-d 00:00:00', strtotime('now - 24 hours'))]);
                break;
            case 'week':
                $taskBuilder = $taskBuilder->andWhere(['>=', 'created_at', date('Y-m-d 00:00:00', strtotime('now - 1 week'))]);
                break;
            case 'month':
                $taskBuilder = $taskBuilder->andWhere(['>=', 'created_at', date('Y-m-d 00:00:00', strtotime('now - 1 month'))]);
                break;
            case 'year':
                $taskBuilder = $taskBuilder->andWhere(['>=', 'created_at', date('Y-m-d 00:00:00', strtotime('now - 1 year'))]);
                break;
        }

        if (trim($this->title)) {
            $taskBuilder = $taskBuilder->andWhere(['like', 'name', $this->title]);
        }

        return $taskBuilder;
    }
}
