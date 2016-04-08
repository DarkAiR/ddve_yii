<?php

class AdminModule extends CWebModule
{
    public $defaultController='admin';

    public function init()
    {
        // Принудительно подключаем все модели в админке, чтобы работали методы в AdminController
        foreach (Yii::app()->getModules() as $moduleName=>$moduleClass) {
            Yii::import('application.modules.'.$moduleName.'.models.*');
        }
    }
}
