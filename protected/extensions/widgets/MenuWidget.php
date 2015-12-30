<?php

Yii::import('application.models.Menu');

abstract class MenuWidget extends ExtendedWidget
{
    public $model;
    public $attribute;

    protected $menuId = Menu::NONE;
    protected $template = '';

    public function run()
    {
        if (empty($this->template))
            return;

        $url = trim( Yii::app()->request->url, '/' );
        $items = MenuItem::model()
            ->onSite()
            ->byParent(0)
            ->byMenuId($this->menuId)
            ->orderDefault()
            ->findAll();

        $itemsArr = array();
        foreach ($items as $item)
        {
            // Убираем язык из урла 
            $domains = explode('/', ltrim($url, '/'));
            if (in_array($domains[0], array_keys(Yii::app()->params['languages']))) {
                array_shift($domains);
                $url = implode('/', $domains);
            }
            $select = (strpos($url, trim($item->link, '/')) === 0)
                ? true
                : false;

            $blank = 0;
            if (strpos($item->link, 'http://') === 0 || strpos($item->link, 'https://') === 0) {
                $link = $item->link;
                $blank = 1;
            } else {
                $link = isset(Yii::app()->params['routes'][$item->link])
                    ? array('/'.Yii::app()->params['routes'][$item->link])  // Роуты с языком
                    : '/'.$item->link;                                      // Ссылка без языка, будет вести на дефолтную страницу
                $link = CHtml::normalizeUrl($link);
            }

            $iconUrl = $item->getIconUrl();

            $itemsArr[] = array(
                'name'      => $item->name,
                'link'      => $link,
                'select'    => $select,
                'iconUrl'   => $iconUrl,
                'blank'     => $blank,
                'enabled'   => $item->active
            );
        }

        $this->beforeRender($itemsArr);
        $this->render($this->template, array('items'=>$itemsArr));
    }

    protected function beforeRender(&$itemsArr)
    {
    }
}
