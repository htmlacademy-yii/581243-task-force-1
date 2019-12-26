<?php

namespace frontend\models;

use Yii;
use yii\base\InvalidConfigException;
use yii\db\ActiveQuery;

/**
 * This is the model class for table "tasks".
 *
 * @property int $id
 * @property string $name
 * @property string $description
 * @property int $category_id
 * @property string|null $address
 * @property string|null $lat
 * @property string|null $long
 * @property int|null $budget
 * @property string|null $expire_at
 * @property int $client_id
 * @property int|null $executor_id
 * @property int $task_status_id
 * @property string $created_at
 * @property string|null $updated_at
 */
class Task extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'tasks';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'description', 'category_id', 'client_id', 'task_status_id', 'created_at'], 'required'],
            [['description'], 'string'],
            [['category_id', 'budget', 'client_id', 'executor_id', 'task_status_id'], 'integer'],
            [['expire_at', 'created_at', 'updated_at'], 'safe'],
            [['name', 'address', 'lat', 'long'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'description' => 'Description',
            'category_id' => 'Category ID',
            'address' => 'Address',
            'lat' => 'Lat',
            'long' => 'Long',
            'budget' => 'Budget',
            'expire_at' => 'Expire At',
            'client_id' => 'Client ID',
            'executor_id' => 'Executor ID',
            'task_status_id' => 'Task Status ID',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * Заказчик
     * @return ActiveQuery
     */
    public function getClient()
    {
        return $this->hasOne(User::class, ['id' => 'client_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getCategory()
    {
        return $this->hasOne(Category::class, ['id' => 'category_id']);
    }

    /**
     * Исполнитель
     * @return ActiveQuery
     */
    public function getExecutor()
    {
        return $this->hasOne(User::class, ['id' => 'executor_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getStatus()
    {
        return $this->hasOne(Status::class, ['id' => 'task_status_id']);
    }

    /**
     * Отклики
     * @return ActiveQuery
     */
    public function getReplies() {
        return $this->hasMany(Reply::class, ['task_id' => 'id'])
            ->inverseOf('task');
    }

    /**
     * Отзывы (заказчика и исполнителя)
     * @return ActiveQuery
     */
    public function getOpinions() {
        return $this->hasMany(Opinion::class, ['task_id' => 'id'])
            ->inverseOf('task');
    }

    /**
     * @return ActiveQuery
     */
    public function getMessages() {
        return $this->hasMany(Message::class, ['task_id' => 'id'])
            ->inverseOf('task');
    }

    /**
     * @return ActiveQuery
     * @throws InvalidConfigException
     */
    public function getFiles() {
        return $this->hasMany(File::class, ['id' => 'file_id'])
            ->viaTable('task_file', ['task_id' => 'id']);
    }

    public static function filter(ActiveQuery $taskBuilder, TaskFilter $taskFilter): ActiveQuery
    {
        if (!empty($ids = $taskFilter->categories)) {
            $taskBuilder = $taskBuilder->andWhere(['in', 'category_id', $ids]);
        }

        if ($taskFilter->my_city) {
            // нужна аутентификация пользователя
        }

        if ($taskFilter->no_executor) {
            $taskBuilder->andWhere(['executor_id' => NULL]);
        }
        if ($taskFilter->no_address) {
            $taskBuilder->andWhere(['address' => NULL]);
        }

        $date = null;
        switch ($taskFilter->date) {
            case 'day':
                $date = date('Y-m-d 00:00:00', strtotime('now - 24 hours'));
                break;
            case 'week':
                $date = date('Y-m-d 00:00:00', strtotime('now - 1 week'));
                break;
            case 'month':
                $date = date('Y-m-d 00:00:00', strtotime('now - 1 month'));
                break;
            case 'year':
                $date = date('Y-m-d 00:00:00', strtotime('now - 1 year'));
                break;
        }

        if ($date) {
            $taskBuilder = $taskBuilder->andWhere(['>=', 'created_at', $date]);
        }

        if (trim($taskFilter->title)) {
            $taskBuilder = $taskBuilder->andWhere(['like', 'name', $taskFilter->title]);
        }

        return $taskBuilder;
    }
}
