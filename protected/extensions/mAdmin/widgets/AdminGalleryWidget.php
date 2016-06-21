<?php
/**
 * Галерея
 */
class AdminGalleryWidget extends ExtendedWidget
{
    public $form;
    public $model;
    public $attribute;
    public $innerImagesField;
    public $innerRemoveField;

    public function init()
    {
        parent::init();
        AdminComponent::getInstance()->assetsRegistry->registerPackage('colorbox');
        AdminComponent::getInstance()->assetsRegistry->registerPackage('bootbox');
    }

    public function run()
    {
        if (empty($this->model) || !is_object($this->model)) {
            echo 'Error: model incorrect';
            return;
        }
        $this->render('gallery', array(
            'form'              => $this->form,
            'model'             => $this->model,
            'attribute'         => $this->attribute,
            'modelName'         => get_class($this->model),
            'innerImagesField'  => $this->innerImagesField,
            'innerRemoveField'  => $this->innerRemoveField,
        ));
    }
}
