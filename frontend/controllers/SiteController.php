<?php
namespace frontend\controllers;

use frontend\models\Task;
use yii\helpers\Url;
use yii\filters\AccessControl;

/**
 * Site controller
 */
class SiteController extends SecuredController
{
    /**
     * {@inheritdoc}
     */
    public function behaviors(): array
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['index'],
                'rules' => [
                    [
                        'actions' => ['index'],
                        'allow' => true,
                        'roles' => ['?'],
                    ],
                    [
                        'actions' => ['index'],
                        'allow' => false,
                        'roles' => ['@'],
                        'denyCallback' => function($rule, $action) {
                            return $this->redirect(Url::to(['/task/']));
                        },
                    ]
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function actions(): array
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return mixed
     */
    public function actionIndex(): string
    {
        $tasks = Task::find()->orderBy([
            'created_at' => SORT_DESC,
        ])->limit(4)->all();
        return $this->render('index', ['tasks' => $tasks]);
    }
}
