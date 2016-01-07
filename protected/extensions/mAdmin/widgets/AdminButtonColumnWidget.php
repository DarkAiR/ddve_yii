<?php

Yii::import('zii.widgets.grid.CButtonColumn');

class AdminButtonColumnWidget extends CButtonColumn
{
    /**
     * @var string the view button icon (defaults to 'eye-open').
     */
    public $viewButtonIcon = 'eye';

    /**
     * @var string the update button icon (defaults to 'pencil').
     */
    public $updateButtonIcon = 'pencil';

    /**
     * @var string the delete button icon (defaults to 'trash').
     */
    public $deleteButtonIcon = 'trash';

    /**
     * @var mixed the HTML code representing a filter input (eg a text field, a dropdown list)
     * that is used for this data column. This property is effective only when
     * {@link CGridView::filter} is set.
     * If this property is not set, a text field will be generated as the filter input;
     * If this property is an array, a dropdown list will be generated that uses this property value as
     * the list options.
     * If you don't want a filter for this data column, set this value to false.
     * @since 1.1.1
     */
    public $filter;

    /**
     * Initializes the default buttons (view, update and delete).
     */
    protected function initDefaultButtons()
    {
        parent::initDefaultButtons();

        if ($this->viewButtonIcon !== false && !isset($this->buttons['view']['icon']))
            $this->buttons['view']['icon'] = $this->viewButtonIcon;

        if ($this->updateButtonIcon !== false && !isset($this->buttons['update']['icon']))
            $this->buttons['update']['icon'] = $this->updateButtonIcon;

        if ($this->deleteButtonIcon !== false && !isset($this->buttons['delete']['icon']))
            $this->buttons['delete']['icon'] = $this->deleteButtonIcon;
    }

    /**
     * Renders a link button.
     *
     * @param string $id the ID of the button
     * @param array $button the button configuration which may contain 'label', 'url', 'imageUrl' and 'options' elements.
     * @param integer $row the row number (zero-based)
     * @param mixed $data the data object associated with the row
     */
    protected function renderButton($id, $button, $row, $data)
    {
        $options = isset($button['options']) ? $button['options'] : array();
        if (isset($options['visible']) && !$this->evaluateExpression($options['visible'], array('row'=>$row, 'data'=>$data)))
            return;

        if (isset($button['visible'])  && !$this->evaluateExpression( $button['visible'], array('row'=>$row, 'data'=>$data)))
            return;

        $label = isset($button['label']) ? $button['label'] : $id;
        $url = isset($button['url'])
            ? $this->evaluateExpression($button['url'], array('data' => $data, 'row' => $row))
            : '#';
        $options = isset($button['options'])
            ? $button['options']
            : array();

        if (!isset($options['title']))
            $options['title'] = $label;

        if (!isset($options['data-toggle']))
            $options['data-toggle'] = 'tooltip';

        unset($options['visible']);

        if (isset($button['icon']) && $button['icon']) {
            if (strpos($button['icon'], 'icon') === false && strpos($button['icon'], 'fa') === false)
                $button['icon'] = 'fa fa-' . implode(' fa-', explode(' ', $button['icon']));
            echo CHtml::link('<i class="' . $button['icon'] . '"></i>', $url, $options);
            return;
        }
        if (isset($button['imageUrl']) && is_string($button['imageUrl'])) {
            echo CHtml::link(CHtml::image($button['imageUrl'], $label), $url, $options);
            return;
        }
        echo CHtml::link($label, $url, $options);
    }
}
