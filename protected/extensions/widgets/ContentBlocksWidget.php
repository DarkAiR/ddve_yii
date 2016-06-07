<?php

Yii::import('modules.contentBlock.models.ContentBlocks');

class ContentBlocksWidget extends ExtendedWidget
{
    public $showTitle = false;
    public $position = ContentBlocks::POS_NONE;

    public function run()
    {
        if ($this->position == ContentBlocks::POS_NONE)
            return;

        $contentBlock = ContentBlocks::model()->onSite()->byPosition($this->position)->find();
        if (empty($contentBlock))
            return;

        $this->render('contentBlock', array(
            'contentBlock' => $contentBlock,
            'showTitle' => $this->showTitle
        ));
    }
}
