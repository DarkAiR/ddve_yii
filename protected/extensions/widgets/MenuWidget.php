<?php

Yii::import('application.models.Menu');

class MenuWidget extends ExtendedWidget
{
    public $template = '';
    public $menuId = Menu::NONE;

    public function run()
    {
        if (empty($this->template))
            return;

        $url = Yii::app()->request->getPathInfo();
        $items = $this->getMenuItems();

        $itemsArr = array();
        foreach ($items as $item)
        {
            // Убираем язык из урла 
            $domains = explode('/', ltrim($url, '/'));
            if (in_array($domains[0], array_keys(Yii::app()->params['languages']))) {
                array_shift($domains);
                $url = implode('/', $domains);
            }
            $select = (strpos($url, trim($item['link'], '/')) === 0)
                ? true
                : false;

            $blank = 0;
            if (strpos($item['link'], 'http://') === 0 || strpos($item['link'], 'https://') === 0) {
                $link = $item['link'];
                $blank = 1;
            } else {
                $link = isset(Yii::app()->params['routes'][$item['link']])
                    ? array('/'.Yii::app()->params['routes'][$item['link']])    // Роуты с языком
                    : '/'.$item['link'];                                        // Ссылка без языка, будет вести на дефолтную страницу
                $link = Yii::app()->params['baseUrl'].CHtml::normalizeUrl($link);
            }

            $iconUrl = $item['iconUrl'];

            $itemsArr[] = array(
                'name'      => $item['name'],
                'link'      => $link,
                'select'    => $select,
                'iconUrl'   => $iconUrl,
                'blank'     => $blank,
                'enabled'   => $item['active']
            );
        }

        $this->beforeRender($itemsArr);
        $this->render($this->template, array('items'=>$itemsArr));
    }

    protected function beforeRender(&$itemsArr)
    {
    }

    protected function getMenuItems()
    {
        $items = MenuItem::model()
            ->onSite()
            ->byParent(0)
            ->byMenuId($this->menuId)
            ->orderDefault()
            ->findAll();
        $res = array();
        foreach ($items as $item) {
            $res[] = array(
                'name'      => $item->name,
                'link'      => $item->link,
                'iconUrl'   => $item->getIconUrl(),
                'active'    => $item->active,
            );
        }
        return $res;
    }
}
