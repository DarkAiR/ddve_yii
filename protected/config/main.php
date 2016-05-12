<?php

Yii::setPathOfAlias('lib', realpath(__DIR__ . '/../../lib'));

if (is_file('../protected/config/params.php')) {
    $params = include '../protected/config/params.php';
} else {
    $params = include '../protected/config/params-global.php';
}

$res = array(
    'basePath' => dirname(__FILE__) . DIRECTORY_SEPARATOR . '..',
    'name' => $params['appName'],
    'preload' => array('log'),
    'import' => array(
        'application.models.*',
        'application.models.forms.*',
        'application.components.*',
        'application.helpers.*',
        'lib.CurlHelper.*',
        'lib.ImageHelper.*',
        'ext.mAdmin.*',
        'ext.YiiMailer.YiiMailer',
        'ext.localConfig.*',
    ),
    'modules' => array(
        'admin',
        'sitemenu',
        'contentBlocks',
    ),
    'components' => array(
        'user' => array(
            // enable cookie-based authentication
            'allowAutoLogin' => true,
            'loginUrl' => array('site/login'),
        ),
        'request' => array(
            'class' => 'application.components.HttpRequest',
            'baseUrl' => $params['baseUrl'],
            'enableCsrfValidation' => true,     // Включаем CSRF защиту для форм
        ),
        'urlManager' => array(
            'class' => 'application.components.UrlManager',
            'urlFormat' => 'path',
            'urlSuffix' => '/',
            'showScriptName' => false,
            'rules' => array(),
            'useStrictParsing' => true,         // Запрещаем обращаться к контроллерам напрямую, только через route
        ),
        'db' => array(
            'connectionString' => 'mysql:host=' . $params['dbHost'] . ';dbname=' . $params['dbName'],
            'emulatePrepare' => true,
            'username' => $params['dbLogin'],
            'password' => $params['dbPassword'],
            'charset' => 'utf8',
        ),
        'authManager' => array(
            'class' => 'CDbAuthManager',
            'connectionID' => 'db',
        ),
        'fs' => array(
            'class' => 'FileSystem',
            'nestedFolders' => 1,
        ),
        'session' => array(
            'class' => 'CDbHttpSession',
            'autoCreateSessionTable' => true,
            'connectionID' => 'db',
            'timeout' => 3600,       // 60 minutes
            'gcProbability' => 100,
        ),
        'viewRenderer' => array(
            'class' => 'lib.twig-renderer.ETwigViewRenderer',
            'twigPathAlias' => 'lib.twig.lib.Twig',
            'options' => array(
                'autoescape' => true,
            ),
            'functions' => array(
                'widget'    => array(
                    0 => 'TwigFunctions::widget',
                    1 => array('is_safe' => array('html')),
                ),
                'const'                     => 'TwigFunctions::constGet',
                'static'                    => 'TwigFunctions::staticCall',
                'call'                      => 'TwigFunctions::call',
                'import'                    => 'TwigFunctions::importResource',
                'importLink'                => 'TwigFunctions::importLink',
                'absLink'                   => 'TwigFunctions::absLink',
                'plural'                    => 'TwigFunctions::plural',
                'dump'                      => 'TwigFunctions::dump',
                'isDebug'                   => 'TwigFunctions::isDebug',
                'link'                      => 'CHtml::normalizeUrl',
                '_t'                        => 'Yii::t',                                        // см. messages/config.php
            ),
            'filters' => array(
                'unset'                     => 'TwigFunctions::filterUnset',
                'translit'                  => 'TwigFunctions::filterTranslit',
                'externalLink'              => 'TwigFunctions::filterExternalLink',
                'fixSkype'                  => 'TwigFunctions::filterFixSkype',

                'date'                      => 'TwigFunctions::filterDate',                     // date from timestamp
                'formatDateTime'            => 'TwigFunctions::filterFormatDateTime',
                'formatMonthYear'           => 'TwigFunctions::filterFormatMonthYear',
            ),
        ),
        'adminComponent' => array(
            'class' => 'ext.mAdmin.components.AdminComponent'
        ),
        'errorHandler' => array(
            'errorAction' => 'site/error',
        ),
        'image' => array(
            'class' => 'ext.image.CImageComponent',
            'driver' => $params['imageDriver'],
        ),
        'log' => array(
            'class' => 'CLogRouter',
            'routes' => array(
                array(
                    'class' => 'CFileLogRoute',
                    'levels' => 'error, warning',
                    'categories' => 'application'
                ),
                array(
                    'class' => 'CFileLogRoute',
                    'levels' => 'error, warning',
                    'categories' => 'admin.*',
                    'logFile' => 'admin.log'
                ),
            ),
        ),
        'localConfig' => array(
            'class' => 'ext.localConfig.LocalConfigExtension'
        ),
        'cache'=>array(
            'class'=>'system.caching.CDbCache',
            'connectionID'=>'db',
        ),
    ),
    'controllerMap' => array(
        'barcodegenerator' => array(
            'class' => 'ext.barcodegenerator.BarcodeGeneratorController',
        ),
    ),
    'params' => array_merge(
        $params,
        array(
            'md5Salt' => 'ThisIsMymd5Salt(*&^%$#',
            'languages' => array(
                'ru' => 'Russian',
                'en' => 'English',
                'de' => 'Deutsch'
            )
        )
    ),
);


$langArr = array_keys($res['params']['languages']);
$langPrefix = '<language:('. implode('|', $langArr) .')>';

$res['sourceLanguage'] = 'ru';
$res['language'] = $langArr[0];

// NOTE: в правилах сперва должен идти роут без action, чтобы преобразования <module>/<contoller>/index давали простые урлы

$res['components']['urlManager']['rules'] = array(
    $langPrefix.'/'                             => 'site/index',
    '/'                                         => 'site/index',

    $langPrefix.'/login/'                       => 'site/login',
    'login/'                                    => 'site/login',
    $langPrefix.'/logout/'                      => 'site/logout',
    'logout/'                                   => 'site/logout',

    // Admin
    'admin/<action:\w+>/'                       => 'admin/admin/<action>',      // Обработка ошибок
    'admin/'                                    => 'admin',
    'admin/<module:\w+>/<controller:\w+>/'      => '<module>/admin<controller>',
    'admin/<module:\w+>/<controller:\w+>/<action:\w+>/' => '<module>/admin<controller>/<action>',
);

// Роуты для правильного преобразования ссылок в меню
$res['params']['routes'] = array(
    //'news'      => 'news/news/blog',
);

return $res;