<?php

class ImageRequiredValidator extends CValidator
{
    public $imageField = '';

    protected function validateAttribute($object, $attribute)
    {
        $value = $object->$attribute;
        $isEmpty = ($value===null || $value===array() || $value==='');

        $value = $object->{$this->imageField};
        $isEmptyImage = ($value===null || $value===array() || $value==='');

        if ($isEmpty && $isEmptyImage) {
            $message = Yii::t('yii','{attribute} cannot be blank.');
            $this->addError($object, $attribute, $message);
        }
    }
}