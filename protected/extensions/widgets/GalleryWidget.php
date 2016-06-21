<?php

class GalleryWidget extends ExtendedWidget
{
    public $images = [];

    public function init()
    {
        TwigFunctions::importResource('js', 'jquery.colorbox.js');
        TwigFunctions::importResource('css', 'colorbox.css');
        
        parent::init();
    }

    public function run()
    {
        $this->render('gallery', array(
            'images' => $this->images
        ));
    }
}
