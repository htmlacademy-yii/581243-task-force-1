<?php

namespace frontend\controllers;


use frontend\models\City;
use frontend\models\User;
use Yii;
use yii\web\Response;

class UserController extends \yii\web\Controller
{
    /**
     * @return string|Response
     */
    public function actionSignup()
    {
        $user = new User();
        $cities = City::find()->select('city')->indexBy('id')->column();

        if (Yii::$app->request->getIsPost()) {
            $user->load(Yii::$app->request->post());

            if ($user->validate()) {
                $user->save();
                return $this->redirect('/task/');
            } else {
                $user->getErrors();
            }
        }

        return $this->render('signup', [
            'user' => $user,
            'cities' => $cities,
        ]);
    }

}
