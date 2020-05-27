<?php

use frontend\components\AddressComponent;
use frontend\components\EventComponent;
use frontend\components\TaskComponent;
use frontend\models\User;
use frontend\modules\api\Module;
use yii\authclient\clients\VKontakte;
use yii\authclient\Collection;
use yii\log\FileTarget;
use yii\redis\Cache;
use yii\rest\UrlRule;
use yii\web\JsonParser;

$params = array_merge(
    require __DIR__ . '/../../common/config/params.php',
    require __DIR__ . '/../../common/config/params-local.php',
    require __DIR__ . '/params.php',
    require __DIR__ . '/params-local.php'
);

return [
    'language' => 'ru-RU',
    'timeZone' => 'UTC',
    'id' => 'app-frontend',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'controllerNamespace' => 'frontend\controllers',
    'modules' => [
        'api' => [
            'class' => Module::class,
        ],
    ],
    'components' => [
        'request' => [
            'csrfParam' => '_csrf-frontend',
            'parsers' => [
                'application/json' => JsonParser::class,
            ],
            'cookieValidationKey' => '',
        ],
        'address' => [
            'class' => AddressComponent::class,
        ],
        'task' => [
            'class' => TaskComponent::class,
        ],
        'event' => [
            'class' => EventComponent::class,
        ],
        'user' => [
            'identityClass' => User::class,
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
                    'class' => FileTarget::class,
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
                'users' => 'user/',
                'events' => 'event/',
                'users/view/<id:\d+>' => 'user/show',
                'users/favorite/<id:\d+>' => 'user/favorite',
                'task/view/<id:\d+>' => 'task/show',
                'task/mylist/<status:\d+>' => 'task/mylist',
                'file/download/<id:\d+>' => 'file/download',
                'reply/reject/<taskId:\d+>/<replyId:\d+>' => 'reply/reject',
                'reply/take-in-work/<taskId:\d+>/<replyId:\d+>' => 'reply/take-in-work',
                'address/<query:.+>' => 'address/', [
                    'class' => UrlRule::class,
                    'controller' => 'api/messages',
                    'patterns' => [
                        'GET,HEAD {id}' => 'view',
                        'POST {id}' => 'create',
                    ],
                ], [
                    'class' => UrlRule::class,
                    'controller' => 'api/tasks',
                ],
            ],
        ],
        'authClientCollection' => [
            'class' => Collection::class,
            'clients' => [
                'vkontakte' => [
                    'class' => VKontakte::class,
                    'clientId' => '7358390',
                    'clientSecret' => 'w04vA3yNRbtV5IuUoK1R',
                    'scope' => 'email',
                ],
            ],
        ],
        'cache' => [
            'class' => Cache::class,
            'redis' => [
                'hostname' => 'localhost',
                'port' => 6379,
                'database' => 1,
            ],
        ],
    ],
    'params' => $params,
];
