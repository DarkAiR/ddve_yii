<?php

class GalleryBehavior extends CActiveRecordBehavior
{
    public $storagePath = '';
    //public $imageWidth = 0;
    //public $imageHeight = 0;
    //public $imageMaxWidth = 0;
    //public $imageMaxHeight = 0;
    public $imagesField = '';
    //public $imageLabel = 'Изображение';
    public $innerImageField = '_images';
    public $innerRemoveField = '_removeGalleryImageFlags';


    public function galleryLabels()
    {
        $arr = array(
            $this->imagesField => Yii::t('app', 'Галерея'),
        );

//        $imgF = $this->imagesField;
//        if (!empty($this->imageWidth) && !empty($this->imageHeight))
//            $arr[$imgF] = $this->imageLabel.' '.$this->imageWidth.'x'.$this->imageHeight;
//        else
//        if (!empty($this->imageMaxWidth) && !empty($this->imageMaxHeight))
//            $arr[$imgF] = $this->imageLabel.' не больше '.$this->imageMaxWidth.'x'.$this->imageMaxHeight;
//        else
//            $arr[$imgF] = $this->imageLabel;
        return $arr;
    }

//                array('_images', 'safe'),

    public function galleryRules()
    {
        $rules = array();
        $rules[] = array($this->innerImageField, 'safe');
        $rules[] = array($this->innerRemoveField, 'safe');
        return $rules;
    }

    public function getStorePath()
    {
        return Yii::getPathOfAlias('webroot.store.'.$this->storagePath).'/';
    }

    public function getImageUrl($img)
    {
        if (array_search($img, $this->owner->{$this->imagesField}) === false)
            return;
        // Проверяем на абсолютные пути (которые возникают, например, при импорте внешних картинок)
        if (preg_match('/^(http|https):\/\//i', $img))
            return $img;
        return CHtml::normalizeUrl('/store/'.str_replace('.', '/', $this->storagePath).'/'.$img);
    }

    public function getOriginalImageUrl($img)
    {
        if (array_search($img, $this->owner->{$this->imagesField}) === false)
            return;
        return CHtml::normalizeUrl('/store/'.str_replace('.', '/', $this->storagePath).'/original/'.$img);
    }

    public function getAbsoluteImagesUrl()
    {
        return array_map(function($imageName) {
            return $this->getImageUrl($imageName);
        }, $this->owner->{$this->imagesField});
    }


    public function afterDelete($event)
    {
        if (!empty($this->owner->{$this->imagesField})) {
            foreach ($this->owner->{$this->imagesField} as $imgName) {
                @unlink( $this->getStorePath().$imgName );
                @unlink( $this->getStorePath().'original/'.$imgName );
            }
        }
        $this->owner->{$this->imagesField} = [];
    }

    public function afterFind($event)
    {
        $this->owner->{$this->imagesField} = json_decode($this->owner->{$this->imagesField}, true);
        if (!is_array($this->owner->{$this->imagesField}))
            $this->owner->{$this->imagesField} = array();
    }

    public function beforeSave($event)
    {
        $this->owner->{$this->imagesField} = json_encode($this->owner->{$this->imagesField});
    }
}