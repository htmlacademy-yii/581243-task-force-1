<?php
namespace frontend\models;

use Yii;
use yii\base\Model;
use yii\web\UploadedFile;

/**
 * Signup form
 */
class AccountForm extends Model
{
    public $avatar;

    public $name;
    public $email;
    public $city_id;
    public $birthday_at;
    public $about;

    public $categories = [];  // специализации

    public $new_password;
    public $confirm;

    public $images;

    public $phone;
    public $skype;
    public $messenger;

    const FILE_NAME = 'file';

    /**
     * Уведомления
     */
    public $new_messages;       // Новое сообщение
    public $task_action;        // действия по заданияю ??
    public $new_response;       // новый отзыв
    public $show_only_client;   // показывать мои контакты только заказчику
    public $hide_profile;       // не показывать мой профиль

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'avatar' => 'Сменить аватар',
            'id' => 'ID',
            'name' => 'Ваше имя',
            'email' => 'email',
            'city_id' => 'Город',
            'birthday_at' => 'День рождения',
            'about' => 'Информация о себе',
            'categories' => 'Categories',
            'new_password' => 'Новый пароль',
            'confirm' => 'Повтор пароля',
            'images' => 'Фото работ',
            'phone' => 'Телефон',
            'skype' => 'Skype',
            'messenger' => 'Другой мессенджер',
            'new_messages' => 'Новое сообщение',
            'task_action' => 'Действия по заданию',
            'new_response' => 'Новый отзыв',
            'show_only_client' => 'Показывать мои контакты только заказчику',
            'hide_profile' => 'Не показывать мой профиль',
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['avatar'], 'image', 'extensions' => ['png', 'jpg', 'gif'], 'maxWidth' => 1000, 'maxHeight' => 1000,],

            ['name', 'trim'],
            ['name', 'required'],
            ['name', 'string', 'min' => 2, 'max' => 255],

            ['email', 'trim'],
            ['email', 'required'],
            ['email', 'email'],
            ['email', 'string', 'max' => 255],
            ['email', 'validateEmail'],

            ['city_id', 'in', 'range' => City::find()->select('id')->asArray()->column()],
            [['birthday_at'], 'date', 'format' => 'php:Y-m-d'],
            [['about'], 'string'],

            [['categories'], 'safe'],

            ['new_password', 'string', 'min' => 6],
            [
                'confirm', 'compare', 'compareAttribute' => 'new_password', 'message'=>"Пароли не совпадают",
                'skipOnEmpty' => false,
                'when' => function ($model) {
                    return $model->new_password !== null && $model->new_password !== '';
                },
            ],

            [
                ['images'],
                'image',
                'extensions' => ['png', 'jpg', 'gif'],
                'maxWidth' => 100, 'maxHeight' => 100,
                'maxFiles' => 1
            ],

            ['phone', 'match', 'pattern' => '/^[\d]{11}/i',
                'message' => 'Номер телефона должен состоять из 11 цифр'],
            [['skype', 'messenger'], 'string', 'min' => 3, 'max' => 255],

            [['new_messages', 'task_action', 'new_response', 'show_only_client', 'hide_profile'], 'boolean'],
        ];
    }

    public function validateEmail($attribute, $params)
    {
        $user = Yii::$app->user->identity;

        if (User::find()->where(['email' => $this->$attribute])->andWhere(['!=', 'id', $user->id])->one()) {
            $this->addError($attribute, 'email уже существует.');
        }
    }

    public function validateCategories($attribute, $params)
    {
        if (Category::find()->where(['in', 'id', $this->$attribute])->count() != count($attribute)) {
            $this->addError($attribute, 'Ошибка при выборе категории.');
        }
    }

    /**
     * @param File|null $file
     * @return array|null
     */
    public function uploadAvatar(File $file = null): ?File
    {
        $file = $file ?? new File();
        if ($this->validate()) {
            $avatar = UploadedFile::getInstances($this, 'avatar');
            if (isset($avatar[0])) {
                return $file->upload($avatar[0], Yii::$app->params['web_uploads']);
            }
        }

        return null;
    }

    /**
     * @return mixed
     */
    public function uploadImages()
    {
        if (isset($_FILES[static::FILE_NAME])) {
            for ($i = 0; $i < count($_FILES[static::FILE_NAME]['name']); $i++) {
                $image = UploadedFile::getInstanceByName(static::FILE_NAME . '[' . $i . ']');
                $file = new File();
                $files[] = $file->upload($image, Yii::$app->params['web_uploads']);
            }
        }

        return $files ?? [];
    }
}
