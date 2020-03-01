<?php
$params = array_merge(
    require __DIR__ . '/../../common/config/params.php',
    require __DIR__ . '/../../common/config/params-local.php',
    require __DIR__ . '/params.php',
    require __DIR__ . '/params-local.php'
);

return [
    'id' => 'app-frontend',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'controllerNamespace' => 'frontend\controllers',
    'components' => [
        'request' => [
            'csrfParam' => '_csrf-frontend',
        ],
        'user' => [
            'identityClass' => 'frontend\models\User',
            'enableAutoLogin' => true,
            'identityCookie' => ['name' => '_identity-frontend', 'httpOnly' => true],
            'loginUrl' => ['/'],
        ],
        'session' => [
            // this is the name of the session cookie used for login on the frontend
            'name' => 'advanced-frontend',
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
                'signup' => 'user/signup',
                'task/view/<id:\d+>' => 'task/show',
                'task/mylist/<status:\d+>' => 'task/mylist',
                'file/download/<id:\d+>' => 'file/download',
                'file/download/<id:\d+>' => 'file/download',
                'reply/reject/<taskId:\d+>/<replyId:\d+>' => 'reply/reject',
                'reply/take-in-work/<taskId:\d+>/<replyId:\d+>' => 'reply/take-in-work',
                'address/<query:.+>' => 'address/',
            ],
        ],

    ],
    'params' => $params,
];
