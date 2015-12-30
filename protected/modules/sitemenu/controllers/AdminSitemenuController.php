<?php

class AdminSitemenuController extends MAdminController
{
    public $modelName = 'MenuItem';
    public $modelHumanTitle = array('пункт', 'пункта', 'пунктов');
    public $allowedRoles = 'admin';

    protected $templateList = '/list';


    public function behaviors()
    {
        return array(
            'imageBehavior' => array(
                'class' => 'application.behaviors.ImageControllerBehavior',
                'imageField' => 'image',
            ),
        );
    }

    /**
     * @param User $model
     * @return array
     */
    public function getEditFormElements($model)
    {
        $menu = Menu::model()->findAll();
        $menus = array();
        foreach ($menu as $m)
        {
            $menus[$m->id] = $m->name;
        }

        $parents = array('0'=>'[корневой элемент]');
        $parentRecords = CActiveRecord::model($this->modelName)->byParent(0)->orderDefault()->findAll();
        foreach ($parentRecords as $record)
        {
            $menuName = $menus[$record->menuId];
            $parents[$menuName][$record->id] = $record->name;
            foreach ($record->children as $childRecord)
                $parents[$childRecord->id] = '- '.$childRecord->name;
        }
        $res = array_merge(
            array(
                'menuId' => array(
                    'type' => 'dropdownlist',
                    'data' => $menus,
                    'htmlOptions' => array(
                        'data-placeholder' => 0,
                    ),
                )
            ),
            $this->getLangField(
                'name', array(
                    'type' => 'textField',
                )
            ),
            array(
                'link' => array(
                    'type' => 'textField',
                ),
                '_image' => array(
                    'class' => 'ext.ImageFileRowWidget',
                    'uploadedFileFieldName' => '_image',
                    'removeImageFieldName' => '_removeImageFlag',
                ),
                'active' => array(
                    'type' => 'checkBox',
                ),
                'visible' => array(
                    'type' => 'checkBox',
                ),
                'parentItemId'=>array(
                    'type' => 'dropdownlist',
                    'data' => $parents,
                    'options' => array($model->id => array('disabled' => 'disabled')),
                    'htmlOptions' => array(
                        'data-placeholder' => 0,
                    ),
                ),
            )
        );
        return $res;
    }

    public function getTableColumns()
    {
        $attributes = array(
            $this->getOrderColumn(),
            'parentItemId',
            'menuId',
            $this->getImageColumn('image', 'getIconUrl()'),
            'name',
            $this->getBooleanColumn('active'),
            $this->getVisibleColumn(),
            $this->getButtonsColumn(),
        );

        return $attributes;
    }

    public function beforeSave($model)
    {
        $this->imageBehavior->imageBeforeSave($model, $model->imageBehavior->getStorePath());
        parent::beforeSave($model);
    }
}
