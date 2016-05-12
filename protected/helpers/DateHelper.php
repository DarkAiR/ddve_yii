<?php

class DateHelper
{
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
