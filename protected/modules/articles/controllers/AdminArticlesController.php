<?php

Yii::import('modules.articles.models.*');

class AdminArticlesController extends MAdminController
{
    public $modelName = 'Article';
    public $modelHumanTitle = array('статью', 'статьи', 'статей');
    public $allowedRoles = 'admin, moderator';

    public function getEditFormElements($model)
    {
        return array_merge(
            array(
                'visible' => array(
                    'type' => 'checkBox',
                ),
                'link' => array(
                    'type' => 'textField',
                )
            ),
            $this->getLangField(
                'title', array(
                    'type' => 'textArea',
                )
            ),
            $this->getLangField(
                'text', array(
                    'type' => 'ckEditor',
                )
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
}
