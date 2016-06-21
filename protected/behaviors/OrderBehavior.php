<?php

class OrderBehavior extends CActiveRecordBehavior
{
    public $orderField = 'orderNum';
    private $moveToUp = true;

    public function orderLabels()
    {
        return array(
            $this->orderField => Yii::t('app', 'Порядок сортировки'),
        );
    }

    public function orderRules()
    {
        return array(
            array($this->orderField, 'numerical', 'integerOnly'=>true),
        );
    }

    public function orderSetAttribute($value)
    {
        // Выясняем, в какую сторону переместили запись (moveToUp - на возрастание)
        $this->moveToUp = $value < $this->owner->{$this->orderField}
            ? false
            : true;
    }

    public function beforeSave($event)
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
                $sql = $this->moveToUp
                    ? 'UPDATE '.$this->owner->tableName().' SET '.$this->orderField.' = '.$this->orderField.'-1 WHERE '.$this->orderField.' <= '.$this->owner->{$this->orderField}
                    : 'UPDATE '.$this->owner->tableName().' SET '.$this->orderField.' = '.$this->orderField.'+1 WHERE '.$this->orderField.' >= '.$this->owner->{$this->orderField};
                Yii::app()->db->createCommand($sql)->execute();
            }
        }
    }

    public function afterSave($event)
    {
        $sql = 'SELECT id, '.$this->orderField.' FROM '.$this->owner->tableName();
        $res = Yii::app()->db->createCommand($sql)->queryAll();
        
        // Переназначаем значение orderNum
        $index = 1;
        $valueStr = '';
        foreach ($res as $v) {
            if ($v[$this->orderField] != $index)
                $valueStr .= ' when id='.$v['id'].' then '.$index;
            $index++;
        }
        if (!empty($valueStr)) {
            $sql = 'UPDATE '.$this->owner->tableName().' SET '.$this->orderField.' = case '.$valueStr.' else '.$this->orderField.' end';
            Yii::app()->db->createCommand($sql)->execute();
        }
    }
}
