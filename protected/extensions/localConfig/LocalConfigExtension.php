<?php

Yii::import('application.models.LocalConfigItem');

class LocalConfigExtension extends CApplicationComponent
{
    public static $config;

    public function init()
    {
        parent::init();
        Yii::import('ext.localConfig.*');

        try {
            $items = @LocalConfigItem::model()->findAll();

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
    public function getConfig($path, $hasPhones=false)
    {
        if (!isset(self::$config[$path]))
            return null;
        
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
}
