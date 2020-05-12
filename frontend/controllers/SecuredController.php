<?php

namespace frontend\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\Url;
use yii\web\BadRequestHttpException;

class SecuredController extends \yii\web\Controller
{
    /**
     * @param $action
     * @return bool
     * @throws BadRequestHttpException
     */
    public function beforeAction($action): bool
    {
        $user = Yii::$app->user->identity;
        if ($user) {
            $user->last_activity_at = date('Y-m-d H:i:s');
            $user->save();
        }

        return parent::beforeAction($action);
    }

    public function behaviors(): array
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['login', 'signup', 'auth'],
                        'allow' => true,
                        'roles' => ['?'],
                    ],
                    [
                        'actions' => ['login', 'signup', 'auth'],
                        'allow' => false,
                        'roles' => ['@'],
                        'denyCallback' => function($rule, $action) {
                            return $this->redirect(Url::to(['/task/']));
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
                            return $this->redirect(Url::to(['/']));
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
