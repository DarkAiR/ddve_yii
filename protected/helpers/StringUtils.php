<?php

class StringUtils
{
    /**
     * перекодирование строки из 1251-кодировки в UTF-8
     * @param $inStr string Строка в win-1251 кодировке для перекодирования в UTF-8
     * @return string
     */
    public static function convertWinToUTF8($inStr)
    {
        return @iconv('windows-1251','utf-8',$inStr);
    }

    public static function getWordEndByAmount( $amount, $ends = array("","","") )
    {
        if ($amount > 10 && $amount < 20)
            return $ends[0];

        $res = $amount % 10;
        if ($res == 1)
            return $ends[1];
        if ($res > 1 && $res < 5)
            return $ends[2];
        return $ends[0];
    }

    /**
    * Вырезает все непечатные UNICODE-символы
    *
    * Аналог html_entity_decode с параметром ENT_QUOTES + дополнительные
    * обработки строки, используется для гибкости
    *
    * @param string $str строка
    * @return string
    */
    public static function normalize($str, $xml = false)
    {
        $res = html_entity_decode( strval($str), ENT_QUOTES );
        if ($xml) {
            $pattern = '/[^\x{0009}\x{000a}\x{000d}\x{0020}-\x{D7FF}\x{E000}-\x{FFFD}]+/u';
            $res = preg_replace($pattern, '', $res);
        }

        return $res;
    }

    /**
    * Аналог htmlspecialchars с параметром ENT_QUOTES + дополнительные
    * обработки строки, используется для гибкости
    *
    * @param string $str строка
    * @return string
    */
    public static function safe($str, $normalize = true)
    {
        $str = str_replace( '\\', '\\\\', $str);

        if( $normalize )
            $str = self::normalize( $str );

        $str = htmlspecialchars( $str, ENT_QUOTES);
        $str = preg_replace('@\&amp;#\d+;@', '&mdash;', $str);

        return $str;
    }

    public static function txt($str)
    {
        $str = strval( $str );
        $str = nl2br( $str );
        $str = preg_replace( '#&amp;\#[0-9]+;#', '•', $str );

        return $str;
    }

    public static function stripTags($str)
    {
        return strip_tags($str);
    }

    /**
     * Конвертирует текст из html в txt, пригодный для отправки в текстовых письмах
     */ 
    public static function html2txt($str)
    {
        if (empty($str))
            return '';

        $str = preg_replace('#\<a.*?\>\s*\<img.*?\>\s*\</a\>#is', '', $str); // Потому что гладиолус! Во! @dukhanin
        $str = preg_replace('#\"mailto:(.*?)\"#is', '"${1}"', $str);
        $str = preg_replace('#\<a.*?href=([\'"])(.*?)\\1.*?\>(.*?)</a.*?\>#is', '${3} ( ссылка: ${2} )', $str);

        // <br> -> \n
        $str = preg_replace('#\<br\s*(/)?\>#i', "\n", $str);
        
        // <p> -> \n
        $str = preg_replace('#\<p\.*?\>#i', "\n", $str);

        // &nbsp; -> ' ' because html_entity_decode decode &nbsp; to \xA0
        $str = str_replace('&nbsp;', ' ', $str);

        // remove all tags and decode html
        $str = html_entity_decode(strip_tags($str), ENT_QUOTES);

        $temp = explode("\n", $str);

        $prevEmptyStr = false;
        $out = array();
        foreach ($temp as $_str) {
            $_str = trim($_str);

            if ($_str) {
                $out[] = $_str;
                $prevEmptyStr = false;
            } elseif (!$prevEmptyStr) {
                $out[] = '';
                $prevEmptyStr = true;
            }
        }

        return implode("\n", $out);
    }

    /*
     * форматирование имени сайта для отображения на сайте (убирает некрасивый http:// и т.п.)
     */
    public static function formatSiteName($str)
    {
        return str_replace('http://', '', $str);
    }

    public static function translit($str)
    {
        $result = mb_strtolower($str);
        $result = strtr($result,
            array(
                'а' => 'a', 'б' => 'b', 'в' => 'v', 'г' => 'g', 'д' => 'd', 'е' => 'e',
                'ё'=>'jo', 'ж'=>'zh', 'з'=>'z', 'и'=>'i', 'й'=>'jj', 'к'=>'k',
                'л'=>'l', 'м'=>'m', 'н'=>'n', 'о'=>'o', 'п'=>'p', 'р'=>'r',
                'с'=>'s', 'т'=>'t', 'у'=>'u', 'ф'=> 'f', 'х'=>'kh', 'ц'=>'c',
                'ч'=>'ch', 'ш'=>'sh', 'щ'=>'shh', 'ъ'=>'', 'ы'=>'y', 'ь'=>'',
                'э'=>'eh', 'ю'=>'ju', 'я'=>'ja', ' '=>'_', '-'=>'-', '_'=>'_'
            )
        );
        $result = preg_replace('/[^a-z0-9\-\_\.]/u', '', $result);

        return $result;
    }

    public static function detectUTF8($string)
    {
        return (bool) preg_match('/(?:
            [\xC2-\xDF][\x80-\xBF]           # non-overlong 2-byte
            |\xE0[\xA0-\xBF][\x80-\xBF]     # excluding overlongs
            |[\xE1-\xEC\xEE\xEF][\x80-\xBF]{2} # straight 3-byte
            |\xED[\x80-\x9F][\x80-\xBF]     # excluding surrogates
            |\xF0[\x90-\xBF][\x80-\xBF]{2}   # planes 1-3
            |[\xF1-\xF3][\x80-\xBF]{3}       # planes 4-15
            |\xF4[\x80-\x8F][\x80-\xBF]{2}   # plane 16
            )+/xs', $string);
    }

    public static function removeBOM($string)
    {
        return str_replace("\xef\xbb\xbf", '', $string);
    }

    public static function filterShrinkPhone($phone, $mask = false, $tail = '…')
    {
        $mask = $mask ? $mask : '\+\d\s+?\(\d+\)\s+?(\d{3}\-\d{1})\d\-\d{2}';
        if (preg_match('#'.$mask.'#', $phone, $matches))
            $phone = str_replace($matches[1], $tail, $phone);

        return $phone;
    }

    public static function filterShrink($text, $length, $tail = '…')
    {
        if ( mb_strlen($text) > $length ) {
            $whiteSpacePosition = mb_strpos($text, ' ', $length) - 1;

            if ($whiteSpacePosition > 0) {
                $chars = count_chars(mb_substr($text, 0, ($whiteSpacePosition + 1)), 1);
                if ( isset($chars[ord('<')]) && isset($chars[ord('>')]) && ($chars[ord('<')] > $chars[ord('>')]) ) {
                    $whiteSpacePosition = mb_strpos($text, '>', $whiteSpacePosition) - 1;
                }
                $text = mb_substr($text, 0, ($whiteSpacePosition + 1));
            } else {
                $text = mb_substr($text, 0, $length);
            }

            // close unclosed html tags
            if ( preg_match_all('|<([a-zA-Z]+)|', $text, $aBuffer) ) {
                if ( !empty($aBuffer[1]) ) {
                    preg_match_all('|</([a-zA-Z]+)>|', $text, $aBuffer2);

                    if ( count($aBuffer[1]) != count($aBuffer2[1]) ) {
                        foreach ($aBuffer[1] as $index => $tag) {
                            if ( empty($aBuffer2[1][$index]) || $aBuffer2[1][$index] != $tag) {
                                $text .= '</'.$tag.'>';
                            }
                        }
                    }
                }
            }

            $text .= $tail;
        }

        return $text;
    }

    public static function sumFormat($sum = 0, $currency = false, $format = array('before'=>'<span style="white-space:nowrap">', 'after'=>'</span>', 'thousandsDelimiter'=>' '))
    {
        $res = '';
        $formatOptions = array (
            'before' => '',
            'after' => '',
            'empty' => '',
            'thousandsDelimiter' => '',
        );
        $format = array_replace_recursive($formatOptions, $format);

        if (empty($sum) | !is_numeric($sum))
            return $format['empty'];

        $res = number_format($sum, 0, ',', $format['thousandsDelimiter']);

        if ($currency)
            $res .= ' '.self::plural($sum, $currency[0], $currency[1], $currency[2]);

        $res = $format['before'].$res.$format['after'];

        return $res;
    }

    public static function floatFormat($number, $format = array())
    {
        $tmp = explode('.', $number);
        $dec = (int) $tmp[1];

        return (!$dec)
            ? floor($number)
            : str_replace ('.', ',', $number);
    }

    public static function plural($n, $c1, $c2, $c3 = false)
    {
        if($c3 === false)
            $c3 = $c2;

        return $n % 10 == 1 && $n % 100 !=11
            ? $c1
            : ($n % 10 >= 2 && $n % 10 <=4 && ($n % 100 < 10 || $n % 100 >= 20)
                ? $c2
                : $c3);
    }

    public static function getPrintableRepresentation($value)
    {
        $res = $value;
        if (is_array($value)) {
            if (isset($value[0])) {
                $res = '(' . implode(', ', $value) . ')';
            } else {
                $res = array();
                foreach ($value as $id => $val)
                    $res[] = $id.' => '.$val;
                $res = '(' . implode(', ', $res) . ')';
            }
        }

        if($res === false)
            $res = '0';

        return $res;
    }

    public static function utf8_wordwrap($string, $width=75, $break="\n", $cut=false)
    {
        if ($cut) {
            // Match anything 1 to $width chars long followed by whitespace or EOS,
            // otherwise match anything $width chars long
            $pattern = '/(.{1,'.$width.'})(?:\s|$)|(.{'.$width.'})/uS';
            $replace = '$1$2'.$break;
        } else {
            // Anchor the beginning of the pattern with a lookahead
            // to avoid crazy backtracking when words are longer than $width
            $pattern = '/(?=\s)(.{1,'.$width.'})(?:\s|$)/uS';
            $replace = '$1'.$break;
        }

        return preg_replace($pattern, $replace, $string);
    }
}
