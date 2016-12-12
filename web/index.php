<?php
$updates = json_decode(file_get_contents('php://input'), true);
file_get_contents("https://api.telegram.org/bot260016296:AAH6IbNRAcrhdjGffKxXQiy3fKyzPYUNSLg/sendMessage?chat_id={$updates['message']['chat']['id']}&text=test");
exit;
// comment out the following two lines when deployed to production
defined('YII_DEBUG') or define('YII_DEBUG', true);
defined('YII_ENV') or define('YII_ENV', 'dev');

require(__DIR__ . '/../vendor/autoload.php');
require(__DIR__ . '/../vendor/yiisoft/yii2/Yii.php');

$config = require(__DIR__ . '/../config/web.php');

(new yii\web\Application($config))->run();
