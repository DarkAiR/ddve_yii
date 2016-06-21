<?php

class CoordsBehavior extends CActiveRecordBehavior
{
    public $coordsField = 'coords';
    public $defaultLat = 0;
    public $defaultLng = 0;
    public $defaultZoom = 1;
    private $coordsArr = [0,0,0];

    public function coordsLabels()
    {
        return array(
            $this->coordsField => Yii::t('app', 'Координаты'),
        );
    }

    public function coordsRules()
    {
        return array(
            array($this->coordsField, 'safe'),
        );
    }

    public function afterFind($event)
    {
        $this->coordsArr = explode(';', $this->owner->{$this->coordsField});
        switch (count($this->coordsArr)) {
            case 2:
                $this->coordsArr[2] = $this->defaultZoom;
                break;
            case 3:
                break;
            default:
                $this->coordsArr = [$this->defaultLat, $this->defaultLng, $this->defaultZoom];
                break;
        }
    }

    public function getLat()
    {
        return $this->coordsArr[0];
    }

    public function getLng()
    {
        return $this->coordsArr[1];
    }

    public function getZoom()
    {
        return $this->coordsArr[2];
    }
}
