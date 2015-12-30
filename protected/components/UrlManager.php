<?php
class UrlManager extends CUrlManager
{
    public function createUrl($route, $params=array(), $ampersand='&')
    {
        // Формируем стандартный URL без языка
        $url = parent::createUrl($route, $params, $ampersand);
        $domains = explode('/', ltrim($url, '/'));

        if (in_array($domains[0], array_keys(Yii::app()->params['languages']))) {
            if ($domains[0] == Yii::app()->sourceLanguage) {
                // Вырезем из урла дефолтный язык
                array_shift($domains);
                $url = '/' . implode('/', $domains);
            } else {
                // В урле задан язык, отличный от дефолтного, ничего делать не надо
            }
        } else {
            if (Yii::app()->language != Yii::app()->sourceLanguage) {
                // Язык не задан в урле, добавляем его
                array_unshift($domains, Yii::app()->language);
                $url = '/' . implode('/', $domains);
            } else {
                // Язык не задан, но он дефолтный, ничего подставлять не надо
            }
        }

        return $url;
    }
}
