<?php

class SubscribeWidget extends ExtendedWidget
{
    public $model;
    public $attribute;

    public function run()
    {
        $this->render('subscribe', array(
        ));
    }
}
