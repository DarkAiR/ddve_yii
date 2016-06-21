<?php
class UrlManager extends CUrlManager
{
    public $useArticlesUrl = false;

    public function init()
    {
        Yii::import('modules.articles.models.Article');

        if ($this->useArticlesUrl) {
            $rules = $this->createArticlesUrl();
            $this->rules = array_merge($rules, $this->rules);
        }
        parent::init();
    }

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

    private function createArticlesUrl()
    {
        // нельзя Article::model(), т.к. это убивает переключение языков
        $article = new Article();

        $command = Yii::app()->db->createCommand();
        $criteria = $article->onlyLinks()->getDbCriteria();
        $count = $article->count();
        $offs = 0;

        $langArr = array_keys(Yii::app()->params['languages']);
        $langPrefix = '<language:('. implode('|', $langArr) .')>';
        
        $rules = array();
        do {
            $articles = $command->reset()
                ->select($criteria->select)
                ->from($article->tableName())
                ->limit(20, $offs)
                ->queryAll();
            foreach ($articles as $article) {
                $link = trim($article['link'], '/');        // Используем link, т.к. даже если имя поменяется, здесь все упадет
                $rules[$langPrefix.'/'.$link.'/'] = 'articles/articles/show';
                $rules[$link.'/'] = 'articles/articles/show';
            }
            $offs += 20;
        } while($offs < $count);
        return $rules;
    }
}
