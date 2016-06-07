<?php
/**
 * Виджет выбора координат на Яндекс-картах
  *
 * Возможно использовать виджет для работы с GPS
 * Для этого нужно включить recalcButton и унаследоваться модель от IGpsInterface
 */
class AdminYandexMapWidget extends ExtendedWidget
{
    public $form;
    public $model;
    public $attribute;
    public $defaultZoom = null;
    public $readonly = false;

    public function init()
    {
        parent::init();
        AdminComponent::getInstance()->assetsRegistry->registerPackage('bootbox');
    }

    public function run()
    {
        if (empty($this->model) || !is_object($this->model)) {
            echo 'Error: model incorrect';
            return;
        }

        $defaultZoom = $this->defaultZoom !== null ? $this->defaultZoom : Yii::app()->params['defaultZoom'];

        $arr = explode(';', $this->model->{$this->attribute});
        if (count($arr) != 2) {
            $arr = [0,0];
        }
        list($lat, $lng) = $arr;

        $this->render('yandexMap', array(
            'model'             => $this->model,
            'attribute'         => $this->attribute,
            'modelName'         => get_class($this->model),
            'defaultLatitude'   => Yii::app()->params['defaultLatitude'],
            'defaultLongitude'  => Yii::app()->params['defaultLongitude'],
            'defaultZoom'       => $defaultZoom,
            'lat'               => $lat,
            'lng'               => $lng,
            'readonly'          => $this->readonly
        ));
    }
}
