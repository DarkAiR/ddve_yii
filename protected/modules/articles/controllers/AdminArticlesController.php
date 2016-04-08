<?php

class AdminArticlesController extends MAdminController
{
    public $modelName = 'Articles';
    public $modelHumanTitle = array('статью', 'статьи', 'статей');
    public $allowedRoles = 'admin, manager';

    public function getEditFormElements($model)
    {
        return array_merge(
            array(
                'visible' => array(
                    'type' => 'checkBox',
                ),
                'type' => array(
                    'type' => 'dropdownlist',
                    'data' => Articles::getTypeNames(),
                    'htmlOptions' => array(
                        'data-placeholder' => Articles::TYPE_UNDER_CONSTRUCTION,
                    ),
                ),
            ),
            $this->getLangField(
                'title', array(
                    'type' => 'textField',
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
        $buttons['deleteButtonOptions']['visible'] = '!$data->visible;';
        $attributes = array(
            'id',
            'title',
            'type',
            $this->getVisibleColumn(),
            $buttons
        );

        return $attributes;
    }
}
