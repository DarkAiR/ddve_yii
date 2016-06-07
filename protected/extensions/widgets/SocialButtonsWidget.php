<?php

class SocialButtonsWidget extends ExtendedWidget
{
    public $isHeader = false;
    public $wideScreen = false;

    public function run()
    {
        $this->render('socialButtons', array(
            'isHeader' => $this->isHeader,
            'wideScreen' => $this->wideScreen,
        ));
    }
}
