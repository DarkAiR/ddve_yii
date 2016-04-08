<?php

class AdminController extends MAdminController
{
    public $defaultAction = 'index';
    public $allowedActions = 'translate, error';

    const TRANSLATE_ERROR_BIG_TEXT          = 'big-text';
    const TRANSLATE_ERROR_API_NOT_RESPONSE  = 'api-not-response';
    const TRANSLATE_ERROR_API_ERROR         = 'api-error';
    const TRANSLATE_ERROR_CURL_ERROR        = 'curl-error';
    const TRANSLATE_ERROR_SAVE              = 'not-save';
    const TRANSLATE_ERROR_ALREADY           = 'already-translate';

    public function actionTranslate()
    {
        if (!Yii::app()->request->isPostRequest || !Yii::app()->request->isAjaxRequest)
            throw new CHttpException(403);

        $modelId = Yii::app()->request->getPost('id');
        $modelName = Yii::app()->request->getPost('model');
        $fieldName = Yii::app()->request->getPost('field');
        if (!$modelId || !$modelName || !$fieldName)
            throw new CHttpException(400);

        $model = $modelName::model()->findByPk($modelId);
        $text = $model->$fieldName;

        // Вырезаем непереводимые теги
        $cutTags = $this->translateCutTags($text);

        if (strlen($text) >= 10000) {
            Yii::log(self::TRANSLATE_ERROR_BIG_TEXT, CLogger::LEVEL_ERROR, 'admin.translate');
            echo CJSON::encode(array('errors'=>array(array('errMsg'=>self::TRANSLATE_ERROR_BIG_TEXT))));
            Yii::app()->end();
        }

        $errors = array();
        $someTranslate = false;
        foreach (Yii::app()->params['languages'] as $lang => $langName) {
            if ($lang == Yii::app()->sourceLanguage)
                continue;

            $fieldNameTmp = $fieldName.'_'.$lang;
            if (!empty($model->$fieldNameTmp)) {
                array_push($errors, array('errMsg'=>self::TRANSLATE_ERROR_ALREADY, 'lang'=>$lang));
                continue;
            }

            $dir = Yii::app()->sourceLanguage.'-'.$lang;

            $params = array(
                'key' => Yii::app()->params['translateKey'],
                'text' => $text,
                'lang' => $dir
            );
            $url = 'https://translate.yandex.net/api/v1.5/tr.json/translate';
            try{
                $res = CJSON::decode(CurlHelper::postUrl($url, $params));
            } catch (CurlException $ex) {
                array_push($errors, array('errMsg'=>self::TRANSLATE_ERROR_CURL_ERROR, 'lang'=>$lang, 'code'=>$ex->getCode()));
                continue;
            }
            if (!$res) {
                array_push($errors, array('errMsg'=>self::TRANSLATE_ERROR_API_NOT_RESPONSE, 'lang'=>$lang));
                continue;
            }
            if ($res['code'] != 200) {
                array_push($errors, array('errMsg'=>self::TRANSLATE_ERROR_API_ERROR, 'lang'=>$lang, 'code'=>$res['code']));
                continue;
            }

            // Запоминаем текст
            $model->$fieldNameTmp = $this->translateUncutText($res['text'][0], $cutTags);
            $someTranslate = true;
        }
        if ($someTranslate) {
            if (!$model->save()) {
                echo CJSON::encode(array('errors'=>array(array('errMsg'=>self::TRANSLATE_ERROR_SAVE))));
                Yii::app()->end();
            }
        }

        $res = array();

        // Если хоть что-то перевелось, то выставляем флаг "success"
        if ($someTranslate)
            $res['success'] = true;

        if (!empty($errors)) {
            foreach ($errors as $err) {
                Yii::log($err['errMsg'], CLogger::LEVEL_ERROR, 'admin.translate');
            }
            $res['errors'] = $errors;
        }

        echo CJSON::encode($res);
        Yii::app()->end();
    }

    private function translateCutTags(&$text)
    {
        $count = 0;
        $arr = array();
        $text = preg_replace_callback('/<img.*?\/>/imxsu', function($matches) use (&$count, &$arr) {
            $arr[$count] = $matches[0];
            return '{{'.($count++).'}}';
        }, $text);
        return $arr;
    }

    private function translateUncutText($text, $cutTags)
    {
        return preg_replace_callback('/{{(\d+)}}/imxus', function($matches) use (&$text, &$cutTags) {
            return $cutTags[$matches[1]];
        }, $text);
    }

    public function actionError()
    {
        $error = Yii::app()->errorHandler->error;
        $this->render('layouts/error', $error);
    }
}
