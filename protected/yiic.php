<?php

// change the following paths if necessary
//$yiic=dirname(__FILE__).'/../lib/yii/framework/yiic.php';
$config=dirname(__FILE__).'/config/console.php';

date_default_timezone_set('Asia/Yekaterinburg');

//require_once($yiic);

// fix for fcgi
defined('STDIN') or define('STDIN', fopen('php://stdin', 'r'));

defined('YII_DEBUG') or define('YII_DEBUG',true);

require_once(dirname(__FILE__).'/../lib/yii/framework/yii.php');

if(isset($config))
{
    $app=Yii::createConsoleApplication($config);
    $app->commandRunner->addCommands(YII_PATH.'/cli/commands');
}
else
    $app=Yii::createConsoleApplication(array('basePath'=>dirname(__FILE__).'/../lib/yii/framework/cli'));

$env=@getenv('YII_CONSOLE_COMMANDS');
if(!empty($env))
    $app->commandRunner->addCommands($env);

Yii::setPathOfAlias('webroot', dirname($_SERVER['SCRIPT_FILENAME']).'/../www');

$app->run();
