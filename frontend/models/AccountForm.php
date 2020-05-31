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
    public $categories = [];
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
    public $new_messages;
    public $task_action;
    public $new_response;
    public $show_only_client;
    public $hide_profile;

    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array
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
    public function rules(): array
    {
        return [
            [['avatar'], 'image', 'extensions' => ['png', 'jpg', 'gif'], 'maxWidth' => 1000, 'maxHeight' => 1000,],

            [['name', 'email'], 'required'],
            [['name', 'email'], 'trim'],
            ['name', 'string', 'min' => 2, 'max' => 255],

            ['email', 'email'],
            ['email', 'string', 'max' => 255],
            ['email', 'unique', 'targetAttribute' => 'email', 'targetClass' => User::className(),
                'filter' => ['!=', 'id', Yii::$app->user->id], 'message' =>  'Такой логин уже зарегистрирован'],

            ['city_id', 'validateCity'],
            [['birthday_at'], 'date', 'format' => 'php:Y-m-d'],
            [['about'], 'string'],

            [['categories'], 'safe'],

            ['new_password', 'string', 'min' => 6],
            [
                'confirm', 'compare', 'compareAttribute' => 'new_password', 'message'=>"Пароли не совпадают",
                'skipOnEmpty' => false,
                'when' => function ($model) {
                    return !empty($model->new_password);
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

    /**
     * @param string $attribute
     */
    public function validateCity(string $attribute): void
    {
        if (is_null(City::findOne($this->$attribute))) {
            $this->addError($attribute, 'Указанный город не доступен.');
        }
    }

    /**
     * @param File|null $file
     * @return File|null
     * @throws \Exception
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
     * @return array
     * @throws \Exception
     */
    public function uploadImages(): array
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
