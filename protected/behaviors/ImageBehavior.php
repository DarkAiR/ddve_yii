<?php

class ImageBehavior extends CActiveRecordBehavior
{
    public $storagePath = '';
    public $imageWidth = 0;
    public $imageHeight = 0;
    public $imageMaxWidth = 0;
    public $imageMaxHeight = 0;
    public $imageExt = 'jpeg, jpg, png';
    public $imageField = '';

    public $imageLabel = 'Изображение';
    public $innerImageField = '_image';
    public $innerRemoveBtnField = '_removeImageFlag';


    public function imageLabels()
    {
        $imgF = $this->innerImageField;

        $arr = array(
            $this->imageField => 'Изображение',
            $this->innerRemoveBtnField => 'Удалить'
        );

        if (!empty($this->imageWidth) && !empty($this->imageHeight))
            $arr[$imgF] = $this->imageLabel.' '.$this->imageWidth.'x'.$this->imageHeight;
        else
        if (!empty($this->imageMaxWidth) && !empty($this->imageMaxHeight))
            $arr[$imgF] = $this->imageLabel.' не больше '.$this->imageMaxWidth.'x'.$this->imageMaxHeight;
        else
            $arr[$imgF] = $this->imageLabel;
        return $arr;
    }

    public function imageRules()
    {
        $arr = array($this->innerImageField, 'ext.validators.EImageValidator', 'types'=>$this->imageExt, 'allowEmpty'=>true);
        if (!empty($this->imageWidth))      $arr['width'] = $this->imageWidth;
        if (!empty($this->imageHeight))     $arr['height'] = $this->imageHeight; 
        if (!empty($this->imageMaxWidth))   $arr['maxWidth'] = $this->imageMaxWidth;
        if (!empty($this->imageMaxHeight))  $arr['maxHeight'] = $this->imageMaxHeight; 

        return array($arr);
    }

    public function getStorePath()
    {
        return Yii::getPathOfAlias('webroot.store.'.$this->storagePath).'/';
    }

    public function getImageUrl()
    {
        if (empty($this->owner->{$this->imageField}))
            return '';
        return CHtml::normalizeUrl('/store/'.$this->storagePath.'/'.$this->owner->{$this->imageField});
    }

    public function getOriginalImageUrl()
    {
        if (empty($this->owner->{$this->imageField}))
            return '';
        return CHtml::normalizeUrl('/store/'.$this->storagePath.'/original/'.$this->owner->{$this->imageField});
    }

    public function imageAfterDelete()
    {
        if ($this->owner->{$this->imageField}) {
            @unlink( $this->getStorePath().$this->owner->{$this->imageField} );
            @unlink( $this->getStorePath().'original/'.$this->owner->{$this->imageField} );
        }
    }

    public function imageAfterFind()
    {
        $this->owner->{$this->innerImageField} = $this->getImageUrl();
    }
}