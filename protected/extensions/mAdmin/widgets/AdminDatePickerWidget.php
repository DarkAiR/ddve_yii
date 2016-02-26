<?php

Yii::import('ext.mAdmin.widgets.AdminBaseInputWidget');

class AdminDatePickerWidget extends AdminBaseInputWidget
{
    public $form;
    public $options = array();
    public $events = array();

    /**
     * Initializes the widget.
     */
    public function init()
    {
        $this->htmlOptions['type'] = 'text';
        $this->htmlOptions['autocomplete'] = 'off';

        if (!isset($this->options['language']))
            $this->options['language'] = substr(Yii::app()->getLanguage(), 0, 2);

        parent::init();
    }

    /**
     * Runs the widget.
     */
    public function run()
    {
        list($name, $id) = $this->resolveNameID();

        if ($this->hasModel()) {
            if ($this->form) {
                echo $this->form->textField($this->model, $this->attribute, $this->htmlOptions);
            } else {
                echo CHtml::activeTextField($this->model, $this->attribute, $this->htmlOptions);
            }

        } else {
            echo CHtml::textField($name, $this->value, $this->htmlOptions);
        }

        $this->registerClientScript();
        $this->registerLanguageScript();
        $options = !empty($this->options) ? CJavaScript::encode($this->options) : '';

        ob_start();
        echo "jQuery('#{$id}').datepicker({$options})";
        foreach ($this->events as $event => $handler) {
            echo ".on('{$event}', " . CJavaScript::encode($handler) . ")";
        }

        Yii::app()->getClientScript()->registerScript(__CLASS__ . '#' . $this->getId(), ob_get_clean() . ';');

    }

    /**
     * Registers required client script for bootstrap datepicker. It is not used through bootstrap->registerPlugin
     * in order to attach events if any
     */
    public function registerClientScript()
    {
        $booster = AdminComponent::getInstance();
        $booster->registerPackage('datepicker');
    }

    /**
     * FIXME: this method delves too deeply into the internals of Bootstrap component
     */
    public function registerLanguageScript()
    {
        $booster = AdminComponent::getInstance();

        if (isset($this->options['language']) && $this->options['language'] != 'en') {
            $filename = '/bootstrap-datepicker/js/locales/bootstrap-datepicker.' . $this->options['language'] . '.js';
            if (file_exists(Yii::getPathOfAlias('bootstrap.assets') . $filename)) {
                $booster->assetsRegistry->registerScriptFile($booster->getAssetsUrl() . $filename, CClientScript::POS_HEAD);
            }
        }
    }
}
