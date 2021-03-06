<?php

// comment out the following two lines when deployed to production
defined('YII_DEBUG') or define('YII_DEBUG', true);
defined('YII_ENV') or define('YII_ENV', 'dev');
defined('APP_VERSION') or define('APP_VERSION', '%%VERSION%%');
defined('APP_BUILD_NUMBER') or define('APP_BUILD_NUMBER', '%%BUILD%%');

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../vendor/yiisoft/yii2/Yii.php';

// global event handlers ////////////////
require __DIR__ . '/../config/events.php';  

$config = require __DIR__ . '/../config/web.php';   

$application = new yii\web\Application($config);
$application->run();
