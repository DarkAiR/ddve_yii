<?php

class AdminContentBlocksController extends MAdminController
{
    public $modelName = 'ContentBlocks';
    public $modelHumanTitle = array('контентный блок', 'контентных блоков', 'контентных блоков');
    public $allowedRoles = 'admin, manager';

    public function getEditFormElements($model)
    {
        return array_merge(
            array(
                'visible' => array(
                    'type' => 'checkBox',
                ),
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
            ),
            array(
                'position' => array(
                    'type' => 'dropdownlist',
                    'data' => ContentBlocks::getPosNames(),
                    'htmlOptions' => array(
                        'data-placeholder' => 3,
                    ),
                ),
            )
        );
    }

    public function getTableColumns()
    {
        $attributes = array(
            'id',
            'title',
            $this->getSelectColumn('position', ContentBlocks::getPosNames()),
            $this->getVisibleColumn(),
            $this->getButtonsColumn(),
        );

        return $attributes;
    }
}
