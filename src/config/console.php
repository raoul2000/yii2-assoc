<?php

$params = require __DIR__ . '/params.php';
$db = require __DIR__ . '/test_db.php';

$config = [
    'id' => 'basic-console',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'controllerNamespace' => 'app\commands',
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
        '@tests' => '@app/tests',
    ],
    'components' => [
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'log' => [
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
        'user' => [
            //'class' => 'Da\User\Model\User',
            'class' => 'yii\web\User',
            //'identityClass' => 'app\models\User',
            'identityClass' => 'Da\User\Model\User',
            //'enableAutoLogin' => true,
        ],


    ],
    'modules' => [
        'user' =>  Da\User\Module::class,
        'arhistory' => [
            'class' => 'bupy7\activerecord\history\Module',
        ],        
    ],    
    'params' => $params,
    /*
    'controllerMap' => [
        'fixture' => [ // Fixture generation command line.
            'class' => 'yii\faker\FixtureController',
        ],
    ],
    */
];
$config['bootstrap'][] = 'arhistory';
if (YII_ENV_DEV) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
    ];
}

return $config;
