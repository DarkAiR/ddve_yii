<?php

class LeafletMapWidget extends ExtendedWidget
{
    public $markers = [];       // [lat, lng, title, link]
    public $lng = 0;
    public $lat = 0;
    public $zoom = 0;
    public $useCollapse = false;

    public function init()
    {
        parent::init();
        $mode = YII_DEBUG ? '-src' : '';
        Yii::app()->clientScript->registerCssFile('/leaflet-1.0.0-rc.1/leaflet.css');
        Yii::app()->clientScript->registerScriptFile('/leaflet-1.0.0-rc.1/leaflet'.$mode.'.js');

        Yii::app()->clientScript->registerCssFile('/leaflet-1.0.0-rc.1/MarkerCluster.css');
        Yii::app()->clientScript->registerScriptFile('/leaflet-1.0.0-rc.1/leaflet.markercluster.js');
    }

    public function run()
    {
        $centerLng = $this->lng;
        $centerLat = $this->lat;
        if (!$this->lng || !$this->lat) {
            $centerLng = Yii::app()->params['defaultLongitude'];
            $centerLat = Yii::app()->params['defaultLatitude'];
        }
        $zoom = $this->zoom;
        if (!$this->zoom)
            $zoom = 16;

        $this->render('leafletMap', array(
            'centerLat' => $centerLat,
            'centerLng' => $centerLng,
            'zoom' => $zoom,
            'markers' => $this->markers,
            'useCollapse' => $this->useCollapse
        ));
    }
}
