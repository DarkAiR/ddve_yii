<?php

class AdminBaseInputWidget extends CInputWidget
{
    public function init()
    {
        $this->setDefaultPlaceholder();
    }
    
    protected function setDefaultPlaceholder()
    {
        if (!$this->model)
            return;
    
        if (!isset($this->htmlOptions['placeholder']))
            $this->htmlOptions['placeholder'] = $this->model->getAttributeLabel($this->attribute);
    }
}