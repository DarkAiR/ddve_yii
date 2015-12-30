<?php

class TimeBehavior extends CActiveRecordBehavior
{
    public $createTimeField = '';
    public $_createTime = 0;        // Время создания в человекочитаемом виде


    public function timeLabels()
    {
        return array(
            '_createTime' => 'Время создания',
        );
    }

    public function timeRules()
    {
        return array(
            array($this->createTimeField, 'numerical', 'integerOnly'=>true),
        );
    }

    public function timeAfterFind()
    {
        $this->_createTime = date('d-m-Y H:i', $this->owner->{$this->createTimeField});
    }

    public function timeCreate()
    {
        if (!$this->owner->{$this->createTimeField})
            $this->owner->{$this->createTimeField} = time();
    }
}