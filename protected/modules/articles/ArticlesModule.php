<?php

/**
 * Модуль статей
 * Статья отличается от контентных блоков тем, что у нее нет позиции отображения, зато есть route, по которому она доступна и свой шаблон отображения
 */

class ArticlesModule extends CWebModule
{
    public $defaultController='admin';

    public function init()
    {
        parent::init();
        Yii::import('modules.articles.models.*');
    }
}
