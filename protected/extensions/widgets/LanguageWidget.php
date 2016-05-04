<?php

class LanguageWidget extends ExtendedWidget
{
    public $model;
    public $attribute;

    public function run()
    {
        $currentLang = Yii::app()->language;
        $languages = Yii::app()->params->languages;

        $langArr = array();
        foreach ($languages as $lang=>$name) {
            $langArr[$lang] = Yii::app()->controller->createUrl(Yii::app()->controller->action->id, array_merge($_GET, array('language'=>$lang)));
        }

        $this->render('language', array(
            'currentLang' => $currentLang,
            'languages' => $langArr
        ));
    }
}
