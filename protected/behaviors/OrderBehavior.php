<?php

class OrderBehavior extends CActiveRecordBehavior
{
    public $orderField = 'orderNum';

    public function orderLabels()
    {
        return array(
            $this->orderField => 'Порядок сортировки',
        );
    }

    public function orderRules()
    {
        return array(
            array($this->orderField, 'numerical', 'integerOnly'=>true),
        );
    }

    public function orderBeforeSave()
    {
        if (empty($this->owner->{$this->orderField})) {
            // Автоматическое выставление orderNum
            $sql = 'SELECT MAX('.$this->orderField.')+1 as '.$this->orderField.' FROM '.$this->owner->tableName();
            $orderNum = Yii::app()->db->createCommand($sql)->queryScalar();
            $this->owner->{$this->orderField} = ($orderNum === null) ? 1 : $orderNum;
        } else {
            // Проверяем существующий orderNum
            $sql = 'SELECT id, count(*) as count FROM '.$this->owner->tableName().' WHERE '.$this->orderField.'='.$this->owner->{$this->orderField};
            $row = Yii::app()->db->createCommand($sql)->queryRow();
            if ($row['id'] != $this->owner->id  &&  $row['count'] > 0) {
                // Пересортируем все записи до конца
                $sql = 'UPDATE '.$this->owner->tableName().' SET '.$this->orderField.' = '.$this->orderField.'+1 WHERE '.$this->orderField.' >= '.$this->owner->{$this->orderField};
                Yii::app()->db->createCommand($sql)->execute();
            }
        }
    }
}