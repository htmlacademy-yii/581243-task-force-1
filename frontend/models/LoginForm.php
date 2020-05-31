<?php

namespace frontend\models;

use yii\base\Model;

class LoginForm extends Model
{
    public $email;
    public $password;
    private $user;

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            [['email', 'password'], 'required'],
            ['email', 'email'],
            ['password', 'validatePassword'],
        ];
    }

    /**
     * @param string $attribute
     */
    public function validatePassword(string $attribute): void
    {
        if (!$this->hasErrors()) {
            $user = $this->getUser();
            if (!$user || !$user->validatePassword($this->password)) {
                $this->addError($attribute, 'Неправильный email или пароль');
            }
        }
    }

    /**
     * @return User|null
     */
    public function getUser(): ?User
    {
        if (is_null($this->user)) {
            $this->user = User::findOne(['email' => $this->email]);
        }

        return $this->user;
    }
}
