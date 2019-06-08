<?php

$params = require __DIR__ . '/params.php';
$configManager = require __DIR__ . '/configManager.php';
$db = require __DIR__ . '/db.php';

$config = [
    'id' => 'basic',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'language' => 'fr-FR',
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
        '@data' => '@app/../data',
    ],
    'components' => [
        'configManager' => $configManager,
        'session' => [
            'class' => 'yii\web\DbSession',
            'writeCallback' => function ($session) {
                return [
                   'user_id' => Yii::$app->user->id,
                   'last_write' => time(),
                ];
            },
        ],
        'formatter' => [
            'class' => '\app\components\Formatter'
        ],
        'request' => [
            // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
            'cookieValidationKey' => 'APUtllkiioCHmBBdYlOZSamqTuboHn219lt_J8',
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        /*
        'user' => [
            'identityClass' => 'app\models\User',
            'enableAutoLogin' => true,
        ],*/
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            // send all mails to a file by default. You have to set
            // 'useFileTransport' to false and configure a transport
            // for the mailer to send real emails.
            'useFileTransport' => true,
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
        'db' => $db,
        'authManager' => [
            'class' => 'yii\rbac\DbManager',
        ],
        'attachmentStorageManager' => [
            'class' => 'app\components\AttachmentStorageManager',
            'storePath' => '@data/uploads/store',
            'tempPath' => '@data/uploads/temp'
        ]
    ],
    'params' => $params,
    'modules' => [
        'webshell' => [
            'class' => 'samdark\webshell\Module',
            // 'yiiScript' => Yii::getAlias('@root'). '/yii', // adjust path to point to your ./yii script
        ],        
        'gymv' => [
            'class' => 'app\modules\gymv\Module',
        ],
        'stat' => [
            'class' => 'app\modules\stat\Module',
        ],
        'quality' => [
            'class' => 'app\modules\quality\Module',
        ],
        'user' => [
            'class' => Da\User\Module::class,
            'enableRegistration' => true,
            'enableEmailConfirmation' => false,
            'administratorPermissionName' => 'manageUser',
            'administrators' => ['admin']
        ],
        'arhistory' => [
            'class' => 'bupy7\activerecord\history\Module',
        ],
        'db-manager' => [
            'class' => 'bs\dbManager\Module',
            // path to directory for the dumps
            'path' => '@data/backups',
            // list of registerd db-components
            'dbList' => ['db'],
            /*
            'as access' => [
                'class' => 'yii\filters\AccessControl',
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['admin'],
                    ],
                ],
            ],*/
        ],
    ]
];

$config['bootstrap'][] = 'arhistory';
//$config['bootstrap'][] = 'configManager';


if (YII_ENV_DEV) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',
        // uncomment the following to add your IP if you are not connecting from localhost.
        //'allowedIPs' => ['127.0.0.1', '::1'],
    ];

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
        // uncomment the following to add your IP if you are not connecting from localhost.
        //'allowedIPs' => ['127.0.0.1', '::1'],
    ];
}

return $config;
