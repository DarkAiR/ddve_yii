<?php

class ArticlesController extends Controller
{
    /**
     * Отображение статьи
     */
    public function actionShow()
    {
        $article = Article::model()->byLink(Yii::app()->request->url)->onSite()->find();
        if (!$article)
            throw new CHttpException(404);

        $this->render('/article', [
            'article' => $article
        ]);        
    }
}