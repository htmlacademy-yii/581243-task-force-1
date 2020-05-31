<?php

use yii\queue\redis\Queue;
use yii\redis\Connection;
use yii\swiftmailer\Mailer;

return [
    'name' => 'Task force',
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
    ],
    'vendorPath' => dirname(dirname(__DIR__)) . '/vendor',
    'components' => [
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'redis' => [
            'class' => Connection::class,
            'retries' => 1,
        ],
        'queue' => [
            'class' => Queue::class,
            'redis' => 'redis',
            'channel' => 'queue',
        ],
        'mailer' => [
            'class' => Mailer::class,
            'useFileTransport' => true,
        ],
    ],
    'bootstrap' => [
        'queue',
    ],
];
