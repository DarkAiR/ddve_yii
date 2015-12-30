<?php

class FlashControllerBehavior extends CBehavior
{
    public $flashField = '';
    public $innerField = '_flash';
    public $innerRemoveBtnField = '_removeFlash';

    public function flashBeforeSave($model, $storagePath)
    {
        if ($model->{$this->innerRemoveBtnField})
        {
            // removing file
            // set attribute to null
            @unlink( $storagePath.$model->{$this->flashField} );
            $model->{$this->flashField} = null;
        }

        $model->{$this->innerField} = CUploadedFile::getInstance($model, $this->innerField);

        if ($model->validate(array($this->innerField)) && !empty($model->{$this->innerField}))
        {
            if ($model->{$this->flashField})
            {
                @unlink( $storagePath.$model->{$this->flashField} );
                $model->{$this->flashField} = null;
            }
            
            // saving file from CUploadFile instance $model->{$this->innerField}
            if (!is_dir($storagePath))
                mkdir($storagePath, 755, true);

            $flashName = basename($model->{$this->innerField}->name);
            $ext = strrchr($flashName, '.');
            $flashName = md5(time().$flashName).($ext?$ext:'');

            $model->{$this->innerField}->saveAs( $storagePath.$flashName );
            
            $model->{$this->flashField} = $flashName;
        }
    }
}