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
    'modules' => [
        'api' => [
            'class' => 'frontend\modules\api\Module',
        ],
    ],
    'components' => [
        'request' => [
            'csrfParam' => '_csrf-frontend',
            'parsers' => [
                'application/json' => 'yii\web\JsonParser',
            ],
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
                'file/download/<id:\d+>' => 'file/download',
                'file/download/<id:\d+>' => 'file/download',
                'reply/reject/<taskId:\d+>/<replyId:\d+>' => 'reply/reject',
                'reply/take-in-work/<taskId:\d+>/<replyId:\d+>' => 'reply/take-in-work',
                'address/<query:.+>' => 'address/', [
                    'class' => 'yii\rest\UrlRule',
                    'controller' => 'api/messages',
                    'patterns' => [
                        'GET,HEAD {id}' => 'view',
                        'POST {id}' => 'create',
                    ],
                ], [
                    'class' => 'yii\rest\UrlRule',
                    'controller' => 'api/tasks',
                ],
            ],
        ],
        'authClientCollection' => [
            'class' => 'yii\authclient\Collection',
            'clients' => [
                'vkontakte' => [
                    'class' => 'yii\authclient\clients\VKontakte',
                    'clientId' => '7358390',
                    'clientSecret' => 'w04vA3yNRbtV5IuUoK1R',
                    'scope' => 'email',
                ],
            ],
        ],
    ],
    'params' => $params,
];
