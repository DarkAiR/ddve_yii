<?php
$params = @include dirname(__FILE__) . '/../protected/config/params.php';
if (!$params)
    $params = require dirname(__FILE__) . '/../protected/config/params-global.php';

defined('YII_DEBUG') or define('YII_DEBUG', $params['yiiDebug']);
defined('YII_TRACE_LEVEL') or define('YII_TRACE_LEVEL', 3);
//defined('YII_ENABLE_EXCEPTION_HANDLER') or define('YII_ENABLE_EXCEPTION_HANDLER',true);
//defined('YII_ENABLE_ERROR_HANDLER') or define('YII_ENABLE_ERROR_HANDLER',true);
define('PUBLISH_BOOTSTRAP', false);

date_default_timezone_set('Asia/Yekaterinburg');

$yii = dirname(__FILE__) . '/../lib/yii/framework/' . (YII_DEBUG ? 'yii.php' : 'yiilite.php');
$config = dirname(__FILE__) . '/../protected/config/main.php';

require_once($yii);
//Yii::createWebApplication($config)->run();
require(dirname(__FILE__) . '/../protected/components/ExtendedWebApplication.php');
Yii::createApplication('ExtendedWebApplication', $config)->run();
