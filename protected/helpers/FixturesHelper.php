<?php

class FixturesHelper
{
    /**
     * Загрузка фикстур в формате сериализованного объекта
     */
    public static function loadSerializedModel($model, $filename)
    {
        $res = array();
        $data = self::unserialize($filename);
        if (is_array($data)) {
            foreach ($data as $d) {
                $res[] = new $model($d);
            }
        } else {
            $res = new $model($data);
        }
        return $res;
    }

    /**
     * Загрузка данных в формате JSON
     */
    public static function loadJson($filename, $asArray=false)
    {
        $data = file_get_contents('../fixture/'.$filename.'.json');
        if ($data)
            $data = json_decode($data, $asArray);
        if (!$data)
            return false;
        return $data;
    }

    /**
     * Сохранение данных из сериализованных объектов
     */
    public static function serializeModel($filename, $data)
    {
        $pos = strrpos($filename, '/');
        if ($pos !== false) {
            $path = substr($filename, 0, $pos);
            $path = '../fixture/'.$path.'/';
            if (!is_dir($path))
                mkdir($path, 0755, true);
        }
        self::serialize($filename, $data);
    }

    /**
     * Сериализация массива
     */
    private static function serialize($filename, $data)
    {
        $s = serialize($data);
        file_put_contents('../fixture/'.$filename.'.php', $s);
    }

    /**
     * Сериализация массива
     */
    private static function unserialize($filename)
    {
        $s = file_get_contents('../fixture/'.$filename.'.php');
        return unserialize($s);
    }
}