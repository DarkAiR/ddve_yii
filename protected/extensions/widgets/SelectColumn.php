<?php


Yii::import('zii.widgets.grid.CGridColumn');

class SelectColumn extends CGridColumn
{
    public $name;
    public $filter;
    public $data = array();
    public $sortable=true;
    public $value;
    public $type='text';

    public function init()
    {
        parent::init();
        if ($this->name===null)
            throw new CException(Yii::t('zii','"name" must be specified for SelectColumn.'));
    }


    protected function renderFilterCellContent()
    {
        return;
    }

    /**
     * Renders the header cell content.
     * This method will render a link that can trigger the sorting if the column is sortable.
     */
    protected function renderHeaderCellContent()
    {
        if ($this->grid->enableSorting && $this->sortable && $this->name!==null)
            echo $this->grid->dataProvider->getSort()->link($this->name,$this->header,array('class'=>'sort-link'));
        elseif($this->name!==null && $this->header===null)
        {
            if($this->grid->dataProvider instanceof CActiveDataProvider)
                echo CHtml::encode($this->grid->dataProvider->model->getAttributeLabel($this->name));
            else
                echo CHtml::encode($this->name);
        }
        else
            parent::renderHeaderCellContent();
    }

    /**
     * Renders the data cell content.
     * This method evaluates {@link value} or {@link name} and renders the result.
     * @param integer $row the row number (zero-based)
     * @param mixed $data the data associated with the row
     */
    protected function renderDataCellContent($row, $data)
    {
        if ($this->value!==null) {
            $value = $this->evaluateExpression($this->value, array('data'=>$data,'row'=>$row));
            if (!empty($this->data) && isset($this->data[$value]))
                $value = $this->data[$value];
        }
        elseif ($this->name!==null) {
            $value = CHtml::value($data,$this->name);
        }

        echo $value===null
            ? $this->grid->nullDisplay
            : $this->grid->getFormatter()->format($value, $this->type);
    }
}
