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
    public $required = false;


    public function imageLabels()
    {
        $imgF = $this->innerImageField;

        $arr = array(
            $this->imageField => Yii::t('app', 'Изображение'),
            $this->innerRemoveBtnField => Yii::t('app', 'Удалить')
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

    public function imageRules($on='', $except='')
    {
        $rules = array();

        $arr = array(
            $this->innerImageField,
            'ext.validators.EImageValidator',
            'types'         => $this->imageExt,
            'allowEmpty'    => true,
            'on'            => $on,
            'except'        => $except
        );
        if (!empty($this->imageWidth))      $arr['width'] = $this->imageWidth;
        if (!empty($this->imageHeight))     $arr['height'] = $this->imageHeight; 
        if (!empty($this->imageMaxWidth))   $arr['maxWidth'] = $this->imageMaxWidth;
        if (!empty($this->imageMaxHeight))  $arr['maxHeight'] = $this->imageMaxHeight; 
        $rules[] = $arr;

        if ($this->required) {
            $imageRequiredValidator = array(
                $this->innerImageField,
                'ext.validators.ImageRequiredValidator',
                'imageField'    => $this->imageField,
                'on'            => $on,
                'except'        => $except
            );
            $rules[] = $imageRequiredValidator;

            // Так делать нельзя, т.к. при повторном сохранении картинки innerImageField пустой
            // $rules[] = array($this->innerImageField, 'required');
        }

        // Принимаем флаг удаления картинки
        $rules[] = array($this->innerRemoveBtnField, 'safe');

        return $rules;
    }

    public function getStorePath()
    {
        return Yii::getPathOfAlias('webroot.store.'.$this->storagePath).'/';
    }

    public function getImageUrl()
    {
        if (empty($this->owner->{$this->imageField}))
            return '';
        return CHtml::normalizeUrl('/store/'.str_replace('.', '/', $this->storagePath).'/'.$this->owner->{$this->imageField});
    }

    public function getOriginalImageUrl()
    {
        if (empty($this->owner->{$this->imageField}))
            return '';
        return CHtml::normalizeUrl('/store/'.str_replace('.', '/', $this->storagePath).'/original/'.$this->owner->{$this->imageField});
    }

    public function afterDelete($event)
    {
        if ($this->owner->{$this->imageField}) {
            @unlink( $this->getStorePath().$this->owner->{$this->imageField} );
            @unlink( $this->getStorePath().'original/'.$this->owner->{$this->imageField} );
        }
        $this->owner->{$this->imageField} = '';
    }

    public function afterFind($event)
    {
        $this->owner->{$this->innerImageField} = $this->getImageUrl();
    }
}