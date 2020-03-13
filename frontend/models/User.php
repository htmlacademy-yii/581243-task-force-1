<?php

namespace frontend\models;

use Yii;
use yii\base\InvalidConfigException;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\BaseActiveRecord;
use yii\db\Exception;
use yii\db\Query;
use yii\web\IdentityInterface;

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
class User extends \yii\db\ActiveRecord implements IdentityInterface
{
    const CLIENT = 0;
    const EXECUTOR = 1;

    /**
     * @return array
     */
    public function behaviors()
    {
        return [
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
            ['phone', 'match', 'pattern' => '/^[\d]{11}/i',
                'message' => 'Номер телефона должен состоять из 11 цифр'],
            [['password'], 'string', 'min' => 8],
            [['email'], 'unique'],
        ];
    }

    public function validateCity($attribute, $params)
    {
        if (is_null(City::findOne($this->$attribute))) {
            $this->addError($attribute, 'Указанный город не доступен.');
        }
    }


    /**
     * Finds an identity by the given ID.
     * @param string|int $id the ID to be looked for
     * @return IdentityInterface|null the identity object that matches the given ID.
     * Null should be returned if such an identity cannot be found
     * or the identity is not in an active state (disabled, deleted, etc.)
     */
    public static function findIdentity($id)
    {
        return self::findOne($id);
    }

    /**
     * Finds an identity by the given token.
     * @param mixed $token the token to be looked for
     * @param mixed $type the type of the token. The value of this parameter depends on the implementation.
     * For example, [[\yii\filters\auth\HttpBearerAuth]] will set this parameter to be `yii\filters\auth\HttpBearerAuth`.
     * @return IdentityInterface|null the identity object that matches the given token.
     * Null should be returned if such an identity cannot be found
     * or the identity is not in an active state (disabled, deleted, etc.)
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        // TODO: Implement findIdentityByAccessToken() method.
    }

    /**
     * Returns an ID that can uniquely identify a user identity.
     * @return string|int an ID that uniquely identifies a user identity.
     */
    public function getId()
    {
        return $this->getPrimaryKey();
    }

    /**
     * Returns a key that can be used to check the validity of a given identity ID.
     *
     * The key should be unique for each individual user, and should be persistent
     * so that it can be used to check the validity of the user identity.
     *
     * The space of such keys should be big enough to defeat potential identity attacks.
     *
     * This is required if [[User::enableAutoLogin]] is enabled. The returned key will be stored on the
     * client side as a cookie and will be used to authenticate user even if PHP session has been expired.
     *
     * Make sure to invalidate earlier issued authKeys when you implement force user logout, password change and
     * other scenarios, that require forceful access revocation for old sessions.
     *
     * @return string a key that is used to check the validity of a given identity ID.
     * @see validateAuthKey()
     */
    public function getAuthKey()
    {
        // TODO: Implement getAuthKey() method.
    }

    /**
     * Validates the given auth key.
     *
     * This is required if [[User::enableAutoLogin]] is enabled.
     * @param string $authKey the given auth key
     * @return bool whether the given auth key is valid.
     * @see getAuthKey()
     */
    public function validateAuthKey($authKey)
    {
        // TODO: Implement validateAuthKey() method.
    }

    public function validatePassword($password)
    {
        return Yii::$app->security->validatePassword($password, $this->password);
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
     * Отклики пользователя
     * @return ActiveQuery
     */
    public function getReplies() {
        return $this->hasMany(Reply::class, ['executor_id' => 'id'])
            ->inverseOf('executor');
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
        $opinions = $this->getOpinions()->where(['not', ['rate' => null]])->all();

        if (empty($opinions)) {
            return 0;
        }

        foreach ($opinions as $opinion) {
            $rating += $opinion->rate;
        }

        $rating = round($rating/count($opinions));

        return $rating;
    }

    /**
     * @param array $ids
     * @return array
     * @throws Exception
     */
    public function syncCategories(array $ids): array
    {
        (new Query())
            ->createCommand()
            ->delete('user_category', ['user_id' => $this->id])
            ->execute();
        foreach (Category::find()->where(['in', 'id',  $ids])->all() as $category) {
            $this->link('categories', $category);
        }

        return $this->categories;
    }

    /**
     * @param array $images
     * @throws Exception
     * @throws InvalidConfigException
     */
    public function syncImages(array $images): void
    {
        /**
         * Удаляем все старые фотографии пользователя
         */
        $oldImages = $this->getFotos()->select('id')->column();
        (new Query)
            ->createCommand()
            ->delete('user_foto', ['user_id' => $this->id])
            ->execute();

        (new Query)
            ->createCommand()
            ->delete('files', ['in', 'id', $oldImages])
            ->execute();

        /**
         * Добавляем новые фотографии (6 штук)
         */
        foreach (array_slice($images, 0, 6) as $image) {
            $this->link('fotos', $image);
        }
    }
}
