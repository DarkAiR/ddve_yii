<?php

Yii::import('application.models.Menu');

class MenuWidget extends ExtendedWidget
{
    public $template = '';
    public $menuId = Menu::NONE;

    private static $url = null;

    public function run()
    {
        if (empty($this->template))
            return;

        $items = $this->getMenuItems();

        $this->beforeRender($items);
        $this->render($this->template, array('items'=>$items));
    }

    protected function beforeRender(&$itemsArr)
    {
    }

    protected function getMenuItems($parentId = 0)
    {
        $items = MenuItem::model()
            ->onSite()
            ->byParent($parentId)
            ->byMenuId($this->menuId)
            ->orderDefault()
            ->findAll();
        
        $arr = array();
        foreach ($items as $item) {
            if (strpos($item->link, 'http://') === 0 || strpos($item->link, 'https://') === 0) {
                $link = $item->link;
                $blank = 1;
            } else {
                //---------------------------------------------------------------------------------------
                // Проблема 1: необходимо прописывать роуты, что невозможно при динамическом меню
                //$link = isset(Yii::app()->params['routes'][$item->link])
                //    ? array('/'.Yii::app()->params['routes'][$item->link])    // Роуты с языком
                //    : '/'.$item->link;                                        // Ссылка без языка, будет вести на дефолтную страницу
                $link = array('/'.$item->link);    // Роуты с языком
                //---------------------------------------------------------------------------------------
                $link = Yii::app()->params['baseUrl'].CHtml::normalizeUrl($link);
                $blank = 0;
            }

            $arr[] = array(
                'name'      => $item->name,
                'link'      => $link,
                'blank'     => $blank,
                'iconUrl'   => $item->getIconUrl(),
                'enabled'   => $item->active,
                'select'    => $this->getSelect($item),
                'children'  => $this->getMenuItems($item->id)
            );
        }
        return $arr;
    }

    private function getSelect(&$item)
    {
        if (self::$url === null)
            self::$url = Yii::app()->request->getUrlWithoutLanguage();

        return (strpos(self::$url, trim($item->link, '/')) === 0) ? true : false;
    }
}
