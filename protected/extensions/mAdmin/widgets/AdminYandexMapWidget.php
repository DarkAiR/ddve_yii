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

        $mode = YII_DEBUG ? 'debug' : 'release';
        Yii::app()->clientScript->registerScriptFile('http://api-maps.yandex.ru/2.0/?load=package.full&lang=ru-RU&ns=YMaps&mode='.$mode);
    }

    public function run()
    {
        if (empty($this->model) || !is_object($this->model)) {
            echo 'Error: model incorrect';
            return;
        }

        $defaultZoom = $this->defaultZoom !== null ? $this->defaultZoom : Yii::app()->params['defaultZoom'];

        $arr = explode(';', $this->model->{$this->attribute});
        switch (count($arr)) {
            case 2:
                $arr[2] = $defaultZoom;
                break;
            case 3:
                break;
            default:
                $arr = [0, 0, $defaultZoom];
                break;
        }
        list($lat, $lng, $zoom) = $arr;

        $centerLat = $lat;
        $centerLng = $lng;
        if (!$lat || !$lng) {
            $centerLat = Yii::app()->params['defaultLatitude'];
            $centerLng = Yii::app()->params['defaultLongitude'];
        }

        $this->render('yandexMap', array(
            'form'              => $this->form,
            'model'             => $this->model,
            'attribute'         => $this->attribute,
            'modelName'         => get_class($this->model),
            'defaultLatitude'   => Yii::app()->params['defaultLatitude'],
            'defaultLongitude'  => Yii::app()->params['defaultLongitude'],
            'defaultZoom'       => $defaultZoom,

            'centerLat'         => $centerLat,
            'centerLng'         => $centerLng,
            'lat'               => $lat,
            'lng'               => $lng,
            'zoom'              => $zoom,
            'readonly'          => $this->readonly ? 1 : 0,
        ));
    }
}
