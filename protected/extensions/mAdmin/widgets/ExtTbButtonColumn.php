<?php

Yii::import('bootstrap.widgets.TbButtonColumn');

class ExtTbButtonColumn extends TbButtonColumn
{
    protected function renderButton($id, $button, $row, $data)
    {
        $options = isset($button['options']) ? $button['options'] : array();

        if (isset($options['visible']) && !$this->evaluateExpression($options['visible'], array('row'=>$row, 'data'=>$data)))
            return;

        return parent::renderButton($id, $button, $row, $data);
    }
}
