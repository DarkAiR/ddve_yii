<?php

class ExtendedWidget extends CWidget
{
    public function render( $view, $data=null, $return=false )
    {
        Yii::trace('Twig: starting render widget `'.$view.'`', 'trace');

        $viewFile = $this->getViewFile($view);
        if ($viewFile === false)
            throw new CException(Yii::t('yii', '{widget} cannot find the view "{view}".', array('{widget}'=>get_class($this), '{view}'=>$view)));

        $data['_uniqId'] = 'w'.rand();

        $output = $this->renderFile($viewFile, $data, $return);
        Yii::trace('Twig: finished render widget `'.$view.'`', 'trace');
        return $output;
    }


    public function renderPartial( $view, $data=null, $return=false, $processOutput=false )
    {
        $viewFile = $this->getViewFile($view);
        if ($viewFile === false)
            throw new CException(Yii::t('yii', '{controller} cannot find the requested view "{view}".', array('{controller}'=>get_class($this), '{view}'=>$view)));

        $data['_uniqId'] = 'w'.rand();

        $output = $this->renderFile($viewFile, $data, true);

        if ($processOutput)
            $output = $this->processOutput($output);

        if ($return)
            return $output;

        echo $output;
    }


    /**
     * Перегружаем кеш-методы, чтобы в режиме отладки кеш не работал
     */
    public function beginCache($id,$properties=array())
    {
        if (YII_DEBUG)
            return true;
        return parent::beginCache($id, $properties);
    }
    public function endCache()
    {
        if (YII_DEBUG)
            return;
        parent::endCache();
    }
}