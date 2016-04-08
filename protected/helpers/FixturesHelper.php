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
    public static function loadJson($filename)
    {
        $data = file_get_contents('../fixture/'.$filename.'.json');
        if ($data)
            $data = json_decode($data);
        if (!$data)
            return false;
        return $data;
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