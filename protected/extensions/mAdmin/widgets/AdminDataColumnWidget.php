<?php

Yii::import('zii.widgets.grid.CDataColumn');


class AdminDataColumnWidget extends CDataColumn
{
    /**
     * @var array HTML options for filter input
     * @see AdminDataColumnWidget::renderFilterCellContent()
     */
    public $filterInputOptions;

    /**
     * Renders the header cell content.
     * This method will render a link that can trigger the sorting if the column is sortable.
     */
    protected function renderHeaderCellContent()
    {
        if ($this->grid->enableSorting && $this->sortable && $this->name !== null) {
            $sort = $this->grid->dataProvider->getSort();
            $label = isset($this->header)
                ? $this->header
                : $sort->resolveLabel($this->name);

            if ($sort->resolveAttribute($this->name) !== false) {
                if($sort->getDirection($this->name) === CSort::SORT_ASC){
                    $label .= '&nbsp;&nbsp;<span class="fa fa-sort-asc"></span>';
                } elseif($sort->getDirection($this->name) === CSort::SORT_DESC){
                    $label .= '&nbsp;&nbsp;<span class="fa fa-sort-desc"></span>';
                } else {
                    $label .= ' ';
                }
            }
            echo $sort->link($this->name, $label, array('class' => 'sort-link'));
        } else {
            if ($this->name !== null && $this->header === null) {
                if ($this->grid->dataProvider instanceof CActiveDataProvider)
                    echo CHtml::encode($this->grid->dataProvider->model->getAttributeLabel($this->name));
                else
                    echo CHtml::encode($this->name);
            } else {
                parent::renderHeaderCellContent();
            }
        }
    }

    /**
     * Renders the filter cell.
     */
    public function renderFilterCell()
    {
        echo CHtml::openTag('td', $this->filterHtmlOptions);
        echo '<div class="filter-container">';
        $this->renderFilterCellContent();
        echo '</div>';
        echo CHtml::closeTag('td');
    }

    /**
     * Renders the filter cell content.
     * On top of Yii's default, here we can provide HTML options for actual filter input
     */
    protected function renderFilterCellContent()
    {
        if (is_string($this->filter)) {
            echo $this->filter;
            return;
        }

        if ($this->filter !== false && $this->grid->filter !== null && $this->name !== null && strpos($this->name, '.') === false) {
            if ($this->filterInputOptions) {
                $filterInputOptions = $this->filterInputOptions;
                if (empty($filterInputOptions['id']))
                    $filterInputOptions['id'] = false;
            } else {
                $filterInputOptions = array();
            }

            if (is_array($this->filter)) {
                if (!isset($filterInputOptions['prompt']))
                    $filterInputOptions['prompt'] = '';
                echo CHtml::activeDropDownList($this->grid->filter, $this->name, $this->filter, $filterInputOptions);
            } else
            if ($this->filter === null) {
                echo CHtml::activeTextField($this->grid->filter, $this->name, $filterInputOptions);
            }
            return;
        }

        parent::renderFilterCellContent();
    }

    /**
     * Utility function for appending class names for a generic $htmlOptions array.
     *
     * @param array $htmlOptions
     * @param string $class
     */
    protected static function addCssClass(&$htmlOptions, $class)
    {
        if (empty($class))
            return;

        if (isset($htmlOptions['class']))
            $htmlOptions['class'] .= ' ' . $class;
        else
            $htmlOptions['class'] = $class;
    }
}
