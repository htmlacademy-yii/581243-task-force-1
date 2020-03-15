<?php

namespace frontend\controllers;


use frontend\models\City;
use frontend\models\User;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Response;

class SecuredController extends \yii\web\Controller
{
    /**
     * @param $action
     * @return bool
     * @throws \yii\web\BadRequestHttpException
     */
    public function beforeAction($action)
    {
        $user = Yii::$app->user->identity;
        if ($user) {
            $user->last_activity_at = date('Y-m-d H:i:s');
            $user->save();
        }

        return parent::beforeAction($action);
    }

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['login', 'signup'],
                        'allow' => true,
                        'roles' => ['?'],
                    ],
                    [
                        'actions' => ['login', 'signup'],
                        'allow' => false,
                        'roles' => ['@'],
                        'denyCallback' => function($rule, $action) {
                            return $this->redirect('/task/');
                        },
                    ],
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                    [
                        'allow' => false,
                        'roles' => ['?'],
                        'denyCallback' => function($rule, $action) {
                            return $this->redirect('/');
                        },
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'login' => ['post'],
                ],
            ],
        ];
    }
}
