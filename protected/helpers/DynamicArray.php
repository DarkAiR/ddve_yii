<?php
/**
 * Dynamic array helper class.
 *
 * @author     Dark AiR
 * @copyright  (c) 2016
 */

final class DynamicArray
{
    /**
     * Получить произвольный параметр
     * @return <value> | null
     */
    public static function getParam(&$arrParam, $path)
    {
        $arr = &$arrParam;
        $keys = explode('.', $path);
        foreach ($keys as $key) {
            if (!isset($arr[$key]))
                return null;

            $arr = &$arr[$key];
        }
        return $arr;
    }

    /**
     * Установить произвольный параметр
     * @return booolean
     */
    public static function setParam(&$arrParam, $path, $value)
    {
        $arr = &$arrParam;
        $keys = explode('.', $path);
        foreach ($keys as $key) {
            if (!isset($arr[$key]))
                return false;
            $arr = &$arr[$key];
        }
        $arr = $value;
        return true;
    }

    /**
     * Добавить произвольный параметр
     */
    public static function addParam(&$arrParam, $path, $value)
    {
        $arr = &$arrParam;
        $keys = explode('.', $path);
        foreach ($keys as $key) {
            if (!isset($arr[$key]))
                $arr[$key] = array();
            $arr = &$arr[$key];
        }
        $arr = $value;
    }

    /**
     * Удалить параметр
     * @return boolean
     */
    public static function removeParam(&$arrParam, $path, $level=0)
    {
        $arr = &$arrParam;
        $prevArr = null;

        // $keys always have at least one element - key or "", then we dont need check ($prevArr !== null) anywhere
        $keys = explode('.', $path);
        foreach ($keys as $key) {
            if (!isset($arr[$key]))
                return false;
            $prevArr = &$arr;
            $arr = &$arr[$key];
        }

        // Remove recursive
        if ($level == 0) {
            unset($prevArr[$key]);
        } else {
            if (count($prevArr[$key]) != 0)
                return true;
            unset($prevArr[$key]);
        }  

        array_pop($keys);

        // Check root
        if (count($keys) == 0)
            return true;

        return self::removeParam($arrParam, implode('.', $keys), $level+1);
    }

    /**
     * Проверить существование параметра
     */
    public static function hasParam(&$arrParam, $path)
    {
        $arr = &$arrParam;
        $keys = explode('.', $path);
        foreach ($keys as $key) {
            if (!isset($arr[$key]))
                return false;

            $arr = &$arr[$key];
        }
        return true;
    }
}