<?php

Yii::import('ext.localConfig.*');

class LocalConfigExtension extends CApplicationComponent
{
    public static $config;

    public function init()
    {
        parent::init();
        try {
            $items = LocalConfigItem::model()->findAll();

            /** @var $item LocalConfigItem */
            foreach ($items as $item) {
                $key = '';
                if (!empty($item->module))
                    $key = $item->module.'.';
                $key .= $item->name;
                self::$config[$key] = $item->value;
            }
        } catch (Exception $e) {

        }
    }

    /**
     * @param  string     $path
     * @param  boolean     $hasPhones вставляет невидимый тег в телефон
     * @return mixed|null
     */
    public function getParam($path, $hasPhones=false)
    {
        if (!isset(self::$config[$path])) {
            Yii::log('Попытка получить из конфига несуществующий параметр '.$path, CLogger::LEVEL_ERROR);
            return null;
        }
        
        $res = self::$config[$path];
        if ($hasPhones) {
            $arr = is_array($res) ? $res : array($res);
            foreach ($arr as &$v) {
                if (!is_string($v))
                    continue;
                $v = LocalConfigHelper::fixSkype($v);
            }
            $res = is_array($res) ? $arr : $arr[0];
        }
        return $res;
    }

    public function setParam($path, $value)
    {
        if (!isset(self::$config[$path])) {
            Yii::log('Попытка установить несуществующий параметр конфига  '.$path, CLogger::LEVEL_ERROR);
            return false;
        }

        $arr = explode('.', $path);
        $obj = null;
        if (count($arr) == 1) {
            $obj = LocalConfigItem::model()->byName($arr[0])->find();
        } else
        if (count($arr) == 2) {
            $obj = LocalConfigItem::model()->byModule($arr[0])->byName($arr[1])->find();
        } else {
            Yii::log('Попытка получить параметр конфига по слишком длинному пути '.$path, CLogger::LEVEL_ERROR);
            return false;
        }
        if (empty($obj)) {
            Yii::log('Не найден необходимый параметр конфига '.$path, CLogger::LEVEL_ERROR);
            throw new CHttpException(500, 'Не найден необходимый параметр настроек');
        }

        $obj->value = $value;
        if (!$obj->save()) {
            Yii::log('Не удалось сохранить параметр конфига '.$path, CLogger::LEVEL_ERROR);
            throw new CHttpException(500, 'Не получилось изменить параметр настроек');
        }
        return true;
    }
}
