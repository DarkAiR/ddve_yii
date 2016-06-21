<?php

class FlashBehavior extends CActiveRecordBehavior
{
    public $storagePath = '';
    public $flashWidth = 0;
    public $flashHeight = 0;
    public $flashField = '';

    public $flashLabel = 'Флеш';
    public $innerField = '_flash';
    public $innerRemoveBtnField = '_removeFlash';


    public function flashLabels()
    {
        $field = $this->innerField;

        $arr = array(
            $this->flashField => $this->flashLabel,
            $this->innerRemoveBtnField => 'Удалить'
        );

        if (!empty($this->flashWidth) && !empty($this->flashHeight))
            $arr[$field] = $this->flashLabel.' '.$this->flashWidth.'x'.$this->flashHeight;
        else
            $arr[$field] = $this->flashLabel;
        return $arr;
    }

    public function flashRules()
    {
        return array(array($this->innerField, 'safe'));
    }

    public function getStorePath()
    {
        return Yii::getPathOfAlias('webroot.store.'.$this->storagePath).'/';
    }

    public function getFlashUrl()
    {
        if (empty($this->owner->{$this->flashField}))
            return '';
        return CHtml::normalizeUrl('/store/'.$this->storagePath.'/'.$this->owner->{$this->flashField});
    }

    public function afterDelete($event)
    {
        if ($this->owner->{$this->flashField}) {
            @unlink( $this->getStorePath().$this->owner->{$this->flashField} );
        }
    }

    public function afterFind($event)
    {
        $this->owner->{$this->innerField} = $this->getFlashUrl();
    }
}