<?php

class YandexMapWidget extends ExtendedWidget
{
    public $lng = 0;
    public $lat = 0;
    public $zoom = 0;

    public function init()
    {
        parent::init();
        $mode = YII_DEBUG ? 'debug' : 'release';
        Yii::app()->clientScript->registerScriptFile('http://api-maps.yandex.ru/2.0/?load=package.full&lang=ru-RU&ns=YMaps&mode='.$mode);
    }

    public function run()
    {
        $centerLng = $this->lng;
        $centerLat = $this->lat;
        $showPlacemark = true;
        if (!$this->lng || !$this->lat) {
            $centerLng = Yii::app()->params['defaultLongitude'];
            $centerLat = Yii::app()->params['defaultLatitude'];
            $showPlacemark = false;
        }
        $zoom = $this->zoom;
        if (!$this->zoom)
            $zoom = 16;

        $this->render('yandexMap', array(
            'centerLat' => $centerLat,
            'centerLng' => $centerLng,
            'zoom' => $zoom,
            'showPlacemark' => $showPlacemark
        ));
    }
}
