<?php

class DateHelper
{
    /**
     * @param $date - timestamp / string
     */
    public static function formatDate($date, $format='dd.LL.yyyy')
    {
        $d = (is_numeric($date))
            ? $date
            : strtotime($date);
        if (!$d)
            return '';
        return Yii::app()->dateFormatter->format($format, $d);
    }

    /**
     * @param $time - timestamp / string / minutes from start day
     */
    public static function formatTime($time)
    {
        if (is_string($time) && $time != (string)intval($time)) {
            // Пришла строка
            $time = strtotime($time);
        }
        if ($time > 60*24) {
            // Если пришло большое число, то это timestamp в секундах
            $t = strtotime(date('d.m.Y', $time).' 00:00');
            $time -= $t;
            $time = ceil($time/60);
        }
        $m = $time % 60;
        $h = ($time - $m) / 60;
        $m = ($m < 10 ? '0' : '') . $m;
        $h = ($h < 10 ? '0' : '') . $h;
        return $h.':'.$m;
    }

    public static function formatDateTime($date)
    {
        $d = (is_numeric($date))
            ? $date
            : strtotime($date);
        if (!$d)
            return '';
        return date('d.m.Y G:i', $d);
    }

    public static function formatMonthYear($string)
    {
        return Yii::app()->dateFormatter->format('LLL y', $string);
    }
}
