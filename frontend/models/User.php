<?php

namespace frontend\models;

use Yii;
use yii\base\InvalidConfigException;
use yii\db\ActiveQuery;

/**
 * This is the model class for table "users".
 *
 * @property int $id
 * @property string|null $last_name
 * @property string $name
 * @property string $email
 * @property string $password
 * @property int|null $age
 * @property int|null $city_id
 * @property int|null $user_status
 * @property string|null $birthday_at
 * @property string|null $phone
 * @property string|null $skype
 * @property string|null $messenger
 * @property string|null $address
 * @property string|null $last_activity_at
 * @property string|null $about
 * @property int|null $avatar_id
 * @property int|null $views
 * @property string $created_at
 * @property string|null $updated_at
 * @property int|null $settings_id
 */
class User extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'users';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'email', 'password', 'created_at'], 'required'],
            [['age', 'city_id', 'user_status', 'avatar_id', 'views', 'settings_id'], 'integer'],
            [['birthday_at', 'last_activity_at', 'created_at', 'updated_at'], 'safe'],
            [['about'], 'string'],
            [['last_name', 'name', 'email'], 'string', 'max' => 45],
            [['password', 'skype', 'messenger', 'address'], 'string', 'max' => 255],
            [['phone'], 'string', 'max' => 11],
            [['email', 'password'], 'unique', 'targetAttribute' => ['email', 'password']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'last_name' => 'Last Name',
            'name' => 'Name',
            'email' => 'Email',
            'password' => 'Password',
            'age' => 'Age',
            'city_id' => 'City ID',
            'user_status' => 'User Status',
            'birthday_at' => 'Birthday At',
            'phone' => 'Phone',
            'skype' => 'Skype',
            'messenger' => 'Messenger',
            'address' => 'Address',
            'last_activity_at' => 'Last Activity At',
            'about' => 'About',
            'avatar_id' => 'Avatar ID',
            'views' => 'Views',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'settings_id' => 'Settings ID',
        ];
    }

    /**
     * Задания заказчика
     * @return ActiveQuery
     */
    public function getClientTasks() {
        return $this->hasMany(Task::class, ['client_id' => 'id'])
            ->inverseOf('client');
    }

    /**
     * Задания исполнителя
     * @return ActiveQuery
     */
    public function getExecutorTasks() {
        return $this->hasMany(Task::class, ['client_id' => 'id'])
            ->inverseOf('executor');
    }

    /**
     * @return ActiveQuery
     */
    public function getEvents() {
        return $this->hasMany(Event::class, ['user_id' => 'id'])
            ->inverseOf('user');
    }

    /**
     * @return ActiveQuery
     */
    public function getOpinion() {
        return $this->hasMany(Opinion::class, ['user_id' => 'id'])
            ->inverseOf('user');
    }

    /**
     * @return ActiveQuery
     */
    public function getReplies() {
        return $this->hasMany(Reply::class, ['executor_id' => 'id'])
            ->inverseOf('user');
    }

    /**
     * @return ActiveQuery
     */
    public function getMessages() {
        return $this->hasMany(Message::class, ['author_id' => 'id'])
            ->inverseOf('user');
    }

    /**
     * Выбранные пользователи
     * @return ActiveQuery
     * @throws InvalidConfigException
     */
    public function getFavoriteUsers() {
        return $this->hasMany(User::class, ['id' => 'favorite_user_id'])
            ->viaTable('favorites', ['user_id' => 'id']);
    }

    /**
     * @return ActiveQuery
     * @throws InvalidConfigException
     */
    public function getCategories() {
        return $this->hasMany(Category::class, ['id' => 'category_id'])
            ->viaTable('user_category', ['user_id' => 'id']);
    }

    /**
     * Фото работ
     * @return ActiveQuery
     * @throws InvalidConfigException
     */
    public function getFotos() {
        return $this->hasMany(File::class, ['id' => 'file_id'])
            ->viaTable('user_foto', ['user_id' => 'id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getCity()
    {
        return $this->hasOne(City::class, ['id' => 'city_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getAvatar()
    {
        return $this->hasOne(File::class, ['id' => 'avatar_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getUserSettings()
    {
        return $this->hasOne(UserSettings::class, ['id' => 'settings_id']);
    }
}
