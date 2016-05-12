<?php

class LocalConfigHelper
{
    /**
     * Replace shortcuts to local config params
     */
    public static function parseText($text)
    {
        $search = array(
        );
        $replace = array(
        );
        return str_replace($search, $replace, $text);
    }

    public static function fixSkype($v)
    {
        return implode( str_split($v, 5), '<span style="display:none;">_</span>');
    }
}
