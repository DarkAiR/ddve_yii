<?php

/**
 * Display image from url at edit page
 * Supports thumbnails and image editing (add, update, remove)
 *
 * Model must contains following attributes:
 * <code>
 * public $_image; // CUploadedFile
 * public $_removeImageFlag; // bool
 * </code>
 * And controller must implement following code:
 * <code>
 * public function beforeSave($model)
 * {
 *     if ($model->_removeImageFlag) {
 *         // removing file
 *         // set attribute to null
 *     }
 *     $model->_image = CUploadedFile::getInstance($model, '_image');
 *     if ($model->validate() && !empty($model->_image)) {
 *         // saving file from CUploadFile instance $model->_image
 *     }
 *
 *     parent::beforeSave($model);
 * }
 * </code>
 *
 * @see AdminController::getEditFormElements
 */
class ImageFileRowWidget extends CInputWidget
{
    /**
     * internal
     * @var CActiveRecord
     */
    public $model;

    /**
     * internal
     * @var string refers to fullsize image URL
     */
    public $attribute;

    /**
     * internal
     * @var TbActiveForm
     */
    public $form;

    /**
     * @var string refers to CUploadedFile instance
     */
    public $uploadedFileFieldName = '_image';

    /**
     * @var string refers to checkbox field
     */
    public $removeImageFieldName = '_removeImageFlag';

    /**
     * @var int
     */
    public $maxImageSize = 120;

    /**
     * @var null|string URL to thumbnail image. If empty, used fullsize image
     */
    public $thumbnailImageUrl = null;

    /**
     * @var string Hint will appended to file field
     */
    public $hint = '';

    public function run()
    {
        $model = $this->model;
        $attributeName = $this->attribute;
        $form = $this->form;

        $htmlOptions = array();
        if (!empty($this->hint)) {
            $this->hint = "{$this->hint}";
            $htmlOptions = array('hint' => $this->hint);
        }
        if (!empty($model->$attributeName)) {
            if (empty($this->thumbnailImageUrl)) {
                $this->thumbnailImageUrl = $model->$attributeName;
            }
            $htmlOptions = array(
                'hint' => $this->hint . "<br /><br />" . CHtml::link(
                    CHtml::image(
                        $this->thumbnailImageUrl,
                        '',
                        array('style' => "max-width:{$this->maxImageSize}px; max-height:{$this->maxImageSize}px")
                    ),
                    $model->$attributeName,
                    array('target' => '_blank')
                ),
            );
        }

        $rowOptions = [
            'onlyImages' => true,
            'allowExt' => ['jpeg', 'jpg', 'png', 'gif'],
            'allowMime' => ['image/jpg', 'image/jpeg', 'image/png', 'image/gif']
        ];

        echo '<div class="widget-box">';
        echo    '<div class="widget-body">';
        echo        '<div class="widget-main">';
        echo $form->fileFieldRow($model, $this->uploadedFileFieldName, $htmlOptions, $rowOptions);
        echo            '<div class="form-group">';
        echo                '<label class="col-sm-3"></label>';
        echo                '<div class="controls col-sm-9">';
        echo                    '<p>'.(isset($htmlOptions['hint']) ? $htmlOptions['hint'] : '').'</p>';
        echo                '</div>';
        if (!empty($model->$attributeName)) {
            echo $form->checkboxRow($model, $this->removeImageFieldName);
        }
        echo            '</div>';
        echo        '</div>';
        echo    '</div>';
        echo '</div>';
        echo '<div class="space"></div>';
    }
}
