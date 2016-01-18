<?php

Yii::import('ext.mAdmin.widgets.AdminEditableFieldWidget');
Yii::import('ext.mAdmin.widgets.AdminDataColumnWidget');

class AdminEditableColumnWidget extends AdminDataColumnWidget
{
    /**
     * @var array editable config options.
     * @see EditableField config
     */
    public $editable = array();

    // array holds selectors for the registered scripts
    private static $scripts = array();

    /**
     * Widget initialization
     */
    public function init()
    {
        if (!$this->name)
            throw new CException('You should provide name for EditableColumn');
        parent::init();        
        $this->registerScripts();

        //need to attach ajaxUpdate handler to refresh editables on pagination and sort
        //should be here, before render of grid js
        $this->attachAjaxUpdateEvent();
    }
    
    /**
     * try to register editable scripts before any render, this is used especially for empty data providers
     * works only for CActiveDataProvider; reason is that we have to know model name
     */
    protected function registerScripts()
    {
        if (!$this->grid->dataProvider instanceOf CActiveDataProvider)
            return;
        
        /* dummy data */
        $data = new $this->grid->dataProvider->modelClass();
        $options = CMap::mergeArray(
            $this->editable,
            array(
                'model' => $data,
                'attribute' => $this->name,
                'parentid' => $this->grid->id,
            )
        );
        
        /* dummy widget */
        $widget = $this->grid->controller->createWidget('AdminEditableFieldWidget', $options);
        
        $widget->buildHtmlOptions();
        $widget->buildJsOptions();
        $widget->registerAssets();
        
        $selector = $widget->getSelector(true);

        if (!$this->isScriptRendered($selector)) {
            $script = $widget->registerClientScript(false);
            //use parent() as grid is totally replaced by new content
            Yii::app()->getClientScript()->registerScript(__CLASS__ . '#' . $this->grid->id.'-'.$this->name.'-event', '
                $("#' . $this->grid->id . '").parent().on("ajaxUpdate.yiiGridView", "#' . $this->grid->id . '", function() {' . $script . '});
            ');
        }
    }
    
    private function isScriptRendered($script)
    {
        if(in_array($script, self::$scripts))
            return true;
        self::$scripts[] = $script;
        return false;
    }

    protected function renderDataCellContent($row, $data)
    {
        if(!$data instanceOf CModel)
            throw new CException('EditableColumn can be applied only to CModel based objects');
        
        $options = CMap::mergeArray(
            $this->editable,
            array(
                'model' => $data,
                'attribute' => $this->name,
                'parentid' => $this->grid->id,
            )
        );

        //if value defined for column --> use it as element text
        if (strlen($this->value)) {
            ob_start();
            parent::renderDataCellContent($row, $data);
            $text = ob_get_clean();
            $options['text'] = $text;
            $options['encode'] = false;
        }
        
        /** @var $widget AdminEditableFieldWidget */
        $widget = $this->grid->controller->createWidget('AdminEditableFieldWidget', $options);
        
        //if editable not applied --> render original text
        if (!$widget->apply) {
            if (isset($text))
                echo $text;
            else
                parent::renderDataCellContent($row, $data);
            return;
        }
        
        // just add one editable call for all column cells
        $widget->buildHtmlOptions();
        $widget->buildJsOptions();
        $widget->registerAssets();
        
        // can't call run() as it registers clientScript
        $widget->renderLink();
        
        // manually render client script (one for all cells in column)
        $selector = $widget->getSelector(true);

        if (!$this->isScriptRendered($selector)) {
            $script = $widget->registerClientScript(false);
            //use parent() as grid is totally replaced by new content
            Yii::app()->getClientScript()->registerScript(__CLASS__ . '#' . $this->grid->id.'-'.$this->name.'-event', '
                $("#' . $this->grid->id . '").parent().on("ajaxUpdate.yiiGridView", "#' . $this->grid->id . '", function() {' . $script . '});
            ');
        }                               
    }

    /**
     *### .attachAjaxUpdateEvent()
     *
     * Yii yet does not support custom js events in widgets.
     * So we need to invoke it manually to ensure update of editables on grid ajax update.
     *
     * issue in Yii github: https://github.com/yiisoft/yii/issues/1313
     *
     */
    protected function attachAjaxUpdateEvent()
    {   
        $trigger = '$("#"+id).trigger("ajaxUpdate.yiiGridView");';

        //check if trigger already inserted by another column
        if (strpos($this->grid->afterAjaxUpdate, $trigger) !== false) {
            return;
        }

        //inserting trigger
        if (strlen($this->grid->afterAjaxUpdate)) {
            $orig = $this->grid->afterAjaxUpdate;
            if (strpos($orig, 'js:') === 0) {
                $orig = substr($orig, 3);
            }
            $orig = "\n($orig).apply(this, arguments);";
        } else {
            $orig = '';
        }
        $this->grid->afterAjaxUpdate = "js: function(id, data) {
            $trigger $orig
        }";
    }
}
