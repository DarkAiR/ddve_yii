<?php

Yii::import('modules.articles.models.*');

class AdminArticlesController extends MAdminController
{
    public $modelName = 'Article';
    public $modelHumanTitle = array('статью', 'статьи', 'статей');
    public $allowedRoles = 'admin, moderator';

    public function behaviors()
    {
        return array(
            'galleryBehavior' => array(
                'class' => 'application.behaviors.GalleryControllerBehavior',
                'imageWidth' => Article::IMAGE_W,
                'imageHeight' => Article::IMAGE_H,
            ),
        );
    }

    public function getEditFormElements($model)
    {
        return array_merge(
            array(
                'visible'   => ['type' => 'checkBox'],
                'link'      => ['type' => 'textField'],
            ),
            $this->getLangField('title',    ['type' => 'textArea']),
            $this->getLangField('text',     ['type' => 'ckEditor']),
            array(
                'coords'    => ['type' => 'yandexMap'],
                'images'    => [
                    'type' => 'gallery',
                    'htmlOptions' => [
                        'innerImagesField' => '_images',
                        'innerRemoveField' => '_removeGalleryImageFlags'
                    ],
                ],
            )
        );
    }

    public function getTableColumns()
    {
        $buttons = $this->getButtonsColumn();
        $buttons['deleteButtonOptions'] = array(
            'visible' => '!$data->visible;'
        );
        $attributes = array(
            'id',
            'link',
            'title',
            $this->getVisibleColumn(),
            $buttons
        );
        return $attributes;
    }

    public function beforeSave($model)
    {
        $this->galleryBehavior->galleryBeforeSave($model, $model->getGalleryStorePath());
        parent::beforeSave($model);
    }
}
