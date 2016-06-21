<?php

class GalleryControllerBehavior extends CBehavior
{
    public $imagesField = 'images';
    public $innerImagesField = '_images';
    public $innerRemoveField = '_removeGalleryImageFlags';
    public $imageWidth = null;
    public $imageHeight = null;
    public $resize = true;

    public function galleryBeforeSave($model, $storagePath)
    {
        // path to original
        $storageOrig = $storagePath.'original/';

        // disable resize if 
        $isResize = (empty($this->imageWidth) && empty($this->imageHeight)) ? false : $this->resize;

        if (!empty($model->{$this->innerRemoveField})) {
            foreach ($model->{$this->innerRemoveField} as $imgName) {
                // removing file
                // set attribute to null
                @unlink( $storagePath.$imgName );
                @unlink( $storageOrig.$imgName );

                $imgArr = $model->{$this->imagesField};
                $key = array_search($imgName, $imgArr);
                if ($key !== false)
                    unset($imgArr[$key]);
                $model->{$this->imagesField} = $imgArr;
            }
        }

        $model->{$this->innerImagesField} = CUploadedFile::getInstances($model, $this->innerImagesField);

        if ($model->validate(array($this->innerImagesField)) && !empty($model->{$this->innerImagesField})) {
            // saving file from CUploadFile instance $model->{$this->innerImagesField}
            if (!is_dir($storagePath))
                mkdir($storagePath, 0755, true);
            if (!is_dir($storageOrig))
                mkdir($storageOrig, 0755, true);

            $images = [];
            foreach ($model->{$this->innerImagesField} as $img) {
                $imageName = basename($img->name);
                $ext = strrchr($imageName, '.');
                $imageName = md5(time().$imageName).($ext?$ext:'');

                $img->saveAs( $storageOrig.$imageName );
            
                $image = Yii::app()->image->load($storageOrig.$imageName);
                if ($isResize) {
                    if (empty($this->imageWidth)) {
                        // resize by height
                        $image->resize(null, $this->imageHeight);
                    } else
                    if (empty($this->imageHeight)) {
                        // resize by width
                        $image->resize($this->imageWidth, null);
                    } else {
                        // normal resize
                        $image->resize($this->imageWidth, $this->imageHeight);
                    }
                }
                // rewrite under other name
                $image->save($storagePath.$imageName);

                $images[] = $imageName;
            }
            if (!is_array($model->{$this->imagesField}))
                $model->{$this->imagesField} = array();
            $model->{$this->imagesField} = array_merge($model->{$this->imagesField}, $images);
        }
    }
}