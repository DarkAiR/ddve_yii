<?php

/**
 * Image column for AdminGridViewWidget
 *
 * @see AdminController::getTableColumns
 */
class AdminImageColumnWidget extends AdminDataColumnWidget
{
    public $type = 'raw';

    public $htmlOptions = array('style' => 'width:120px');

    /**
     * @var string additional image css
     */
    public $imageStyle = 'max-width:120px';

    /**
     * @var null|string URL to thumbnail image. If empty, used fullsize image
     */
    public $thumbnailUrl = null;

    public function init()
    {
        if (empty($this->thumbnailUrl))
            $this->thumbnailUrl = '$data->' . $this->name;

        $this->value = '
            CHtml::link(
                CHtml::image(' . $this->thumbnailUrl . ',"", array("style"=>"' . addslashes($this->imageStyle) . '")),
                $data->' . $this->name . ',
                array("target" => "_blank")
            );';

        parent::init();
    }
}
