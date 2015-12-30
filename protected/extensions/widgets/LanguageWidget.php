<?php

class LanguageWidget extends ExtendedWidget
{
    public $model;
    public $attribute;

    public function run()
    {
        $currentLang = Yii::app()->language;
        $languages = Yii::app()->params->languages;

        $this->render('language', array(
            'currentLang' => $currentLang,
            'languages' => $languages
        ));
    }
}
