<?php

$params = require __DIR__ . '/params.php';
$configManager = require __DIR__ . '/configManager.php';
$db = require __DIR__ . '/db.php';

$config = [
    'id' => 'basic',
    'name' => 'Bob l\'assistant',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'language' => 'fr-FR',
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
        '@data' => '@app/../data',
        '@template' => '@app/../data/templates',
        '@imports' => '@app/../data/imports',
    ],

    //////////////////////////////////////////////////////////////////////////////////////////////////////
    // Application Parameters
    //    

    'params' => $params,
    
    //////////////////////////////////////////////////////////////////////////////////////////////////////
    // CONTAINUER 
    //
    // @see https://www.yiiframework.com/doc/guide/2.0/en/concept-configurations
    // @see https://www.yiiframework.com/doc/guide/2.0/en/concept-di-container#advanced-practical-usage
    //

    'container' => [
        'definitions' => [
            /**
             * Set default parameters to Data column
             */
            'yii\grid\DataColumn' => [
                'filterInputOptions' => [
                    'class' => 'form-control input-sm', 
                    'autocomplete'=> 'off',
                ],                
            ]
        ],
        'singletons' => [
            // Dependency Injection Container singletons configuration
        ]
    ],  

    //////////////////////////////////////////////////////////////////////////////////////////////////////
    // COMPONENTS 
    //

    'components' => [
        'configManager' => $configManager,
        'session' => [
            'class' => 'yii\web\DbSession',
            'timeout' => 3600, // session expire after 1 hour
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
            'parsers' => [ // required by REST API
                'application/json' => 'yii\web\JsonParser',
            ]
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
        ],
        'i18n' => [
            'translations' => [
                'app*' => [
                    'class' => 'yii\i18n\PhpMessageSource',
                    //'basePath' => '@app/messages',
                    //'sourceLanguage' => 'en-US',
                    'fileMap' => [
                        'app' => 'app.php',
                        'app/error' => 'error.php',
                    ],
                ],
            ],
        ],
    ],

    //////////////////////////////////////////////////////////////////////////////////////////////////////
    // MODULES
    //

    'modules' => [
        'webshell' => [
            'class' => 'samdark\webshell\Module',
            'allowedIPs' => ['127.0.0.1', '::1', '192.168.0.2'],
            // 'yiiScript' => Yii::getAlias('@root'). '/yii', // adjust path to point to your ./yii script
        ],
        'gymv' => [
            'class' => 'app\modules\gymv\Module',
            'defaultRoute' => 'home/index'
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
            'administrators' => ['admin', 'admin2']
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
            
            'as access' => [
                'class' => 'yii\filters\AccessControl',
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['admin'],
                    ],
                ],
            ],
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
