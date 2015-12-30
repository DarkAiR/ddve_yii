<?php

class EditDocumentsWidget extends CWidget
{
    public $model;
    public $attribute;
    public $attributeRemove = '';
    public $innerField = '';
    public $form;

    public $maxRows = 0;        // Максимальное количество строк (0-бесконечно)


    public function run()
    {
        $model      = $this->model;
        $attribute  = $this->attribute;
        $array      = $model->$attribute;
        if (!is_array($array))
            $array = array();

        for ($i=0; $i<$this->maxRows; $i++) {
            if (isset($array[$i]))
                continue;
            $array[$i] = '';
        }
        ksort($array);

        $this->render('editDocuments',array(
            'form'                  => $this->form,
            'model'                 => $model,
            'modelClass'            => get_class($model),
            'attributeName'         => $attribute,
            'attributeRemoveName'   => $this->attributeRemove,
            'innerField'            => $this->innerField,
            'array'                 => $array,
            'maxRows'               => $this->maxRows,
            'fixed'                 => $this->maxRows > 0
        ));
    }
}
