<?php

namespace frontend\controllers;


use frontend\models\City;
use frontend\models\LoginForm;
use frontend\models\User;
use Yii;
use yii\base\Exception;
use yii\helpers\Url;
use yii\web\Response;
use yii\widgets\ActiveForm;

class UserController extends SecuredController
{
    public function actions()
    {
        return [
            'auth' => [
                'class' => 'yii\authclient\AuthAction',
                'successCallback' => [$this, 'onAuthSuccess'],
            ]
        ];
    }

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
     * @param $client
     * @return Response
     * @throws Exception]
     */
    public function onAuthSuccess($client)
    {
        $attributes = $client->getUserAttributes();
        $user = User::find()->where(['vk_id' => $attributes['id']])->one();

        if (!$user) {
            $user = new User();
            $user->vk_id = $attributes['id'];
            $user->email = $attributes['email'];
            $user->name = $attributes['first_name'];
            $user->last_name = $attributes['last_name'] ?? null;
            $user->password = Yii::$app->getSecurity()
                ->generatePasswordHash(Yii::$app->security->generateRandomString(8));
            if (isset($attributes['city']['title'])) {
                $user->city_id = City::find()->where(['city' => $attributes['city']['title']])->one()->id ?? null;
            }

            if (!($user->validate() && $user->save())) {
                return $this->redirect(Url::to(['/']));
            }
        }

        Yii::$app->user->login($user);
        return $this->redirect(Url::to(['/task/']));
    }
}
