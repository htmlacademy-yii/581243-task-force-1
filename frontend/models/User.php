<?php

namespace frontend\models;

use Yii;
use yii\base\InvalidConfigException;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\BaseActiveRecord;
use yii\db\Query;

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
     * @return array
     */
    public function behaviors()
    {
        return [
            //Использование поведения TimestampBehavior ActiveRecord
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
            [['name', 'email', 'password', 'city_id'], 'required'],
            [['age', 'city_id', 'user_status', 'avatar_id', 'views', 'settings_id'], 'integer'],
            [['birthday_at', 'last_activity_at', 'created_at', 'updated_at'], 'safe'],
            [['about'], 'string'],
            ['email', 'email'],
            ['city_id', 'validateCity'],
            [['last_name', 'name', 'email'], 'string', 'max' => 45],
            [['skype', 'messenger', 'address'], 'string', 'max' => 255],
            [['phone'], 'string', 'max' => 11],
            [['password'], 'string', 'min' => 8],
            [['email', 'password'], 'unique', 'targetAttribute' => ['email', 'password']],
        ];
    }

    public function validateCity($attribute, $params)
    {
        if (is_null(City::findOne($this->$attribute))) {
            $this->addError($attribute, 'Указанный город не доступен.');
        }
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
            'email' => 'Электронная почта',
            'password' => 'Пароль',
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
     * Свои отзывы
     * @return ActiveQuery
     */
    public function getSelfOpinions() {
        return $this->hasMany(Opinion::class, ['author_id' => 'id'])
            ->inverseOf('author');
    }

    /**
     * Отзывы на данного пользователя
     * @return ActiveQuery
     */
    public function getOpinions() {
        return $this->hasMany(Opinion::class, ['evaluated_user_id' => 'id'])
            ->inverseOf('author');
    }

    /**
     * @return ActiveQuery
     */
    public function getReplies() {
        return $this->hasMany(Reply::class, ['executor_id' => 'id'])
            ->inverseOf('evaluatedUser');
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

    /**
     * @return float|int|string
     */
    public function getRating(): int
    {
        $rating = 0;
        $opinions = $this->opinions;

        if (empty($opinions)) {
            return 0;
        }

        foreach ($opinions as $opinion) {
            $rating += $opinion->rate;
        }

        $rating = round($rating/count($opinions));

        return $rating;
    }
}
