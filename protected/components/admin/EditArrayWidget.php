<?php

class EditArrayWidget extends CWidget
{
    public $model;
    public $attribute;

    /** @var bool Массив фиксированной длины */
    public $fixed = false;
    public $numeric = false;

    /** @var array Массив - пример. По нему вычисляется длина фиксированного массива и его тип [ид=>значение] или просто [значение,значение] */
    public $example = array();

    public function run()
    {
        $model = $this->model;
        $attribute = $this->attribute;
        $array = $model->$attribute;
        if (!is_array($array))
            throw new CException('passed attribute is not an array!');

        if (!is_array($this->example))
            throw new CException('passed example is not an array!');

        $numeric = $this->numeric;
        if (!$numeric && isset($this->example[0]))
            $numeric = true;
        if ($this->fixed && sizeof($array) != sizeof($this->example)) {
            $array = array_fill(0, sizeof($this->example), '');
        }

        $this->render('editArray',array(
            'numeric' => $numeric,
            'fixed' => $this->fixed,

            'array'=> $array,
            'model' => $model,
            'modelClass' => get_class($model),
            'attributeName' => $attribute,
        ));
    }
}
