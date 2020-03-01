<?php

namespace frontend\controllers;


use frontend\models\AccountForm;
use frontend\models\Category;
use frontend\models\City;
use frontend\models\LoginForm;
use frontend\models\User;
use frontend\models\UserSettings;
use Yii;
use yii\base\Exception;
use yii\web\Response;
use yii\widgets\ActiveForm;

class UserController extends SecuredController
{
    /**
     * @return string|Response
     * @throws Exception
     */
    public function actionSignup()
    {
        $user = new User();
        $cities = City::find()->select('city')->indexBy('id')->column();

        if (Yii::$app->request->getIsPost()) {
            $user->load(Yii::$app->request->post());

            if ($user->validate()) {
                $user->password = Yii::$app->getSecurity()
                    ->generatePasswordHash($user->password);
                $user->save();
                UserSettings::firstOrCreate($user);

                return $this->redirect('/task/');
            } else {
                $errors = $user->getErrors();
            }
        }

        return $this->render('signup', [
            'user' => $user,
            'cities' => $cities,
            'errors' => $errors ?? [],
        ]);
    }

    /**
     * @return array|Response
     */
    public function actionLogin()
    {
        $loginForm = new LoginForm();
        if (Yii::$app->request->getIsPost()) {
            $loginForm->load(Yii::$app->request->post());
            if (Yii::$app->request->isAjax) {
                Yii::$app->response->format = Response::FORMAT_JSON;

                return ActiveForm::validate($loginForm);
            }
            if ($loginForm->validate()) {
                $user = $loginForm->getUser();
                Yii::$app->user->login($user);
                return $this->redirect('/task/');
            }
        }
    }

    /**
     * @return Response
     */
    public function actionLogout() {
        Yii::$app->user->logout();

        return $this->redirect('/');
    }

    /**
     * @return string
     * @throws \Exception
     */
    public function actionAccount()
    {
        $user = Yii::$app->user->identity;
        $categories = Category::find()->all();
        $cities = City::find()->select('city')->indexBy('id')->column();

        $accountForm = new AccountForm();
        $userSettings = UserSettings::firstOrCreate($user);

        if (Yii::$app->request->getIsPost()) {
            $accountForm->load(Yii::$app->request->post());

            if ($accountForm->validate()) {
                $user->attributes = $accountForm->attributes;
                $user->save();

                $userSettings->attributes = $accountForm->attributes;
                $userSettings->save();

                $user->syncCategories($accountForm->categories);

                $avatar = $accountForm->upload();
                if ($avatar) {
                    $user->link('avatar', $avatar);
                    $accountForm->avatar = null;
                }
            }
        }

        $accountForm->attributes = $user->attributes;
        $accountForm->attributes = $userSettings->attributes;

        return $this->render('account', [
            'accountForm' => $accountForm,
            'categories' => $categories,
            'cities' => $cities,
            'user' => $user,
        ]);
    }
}
