<?php

class ExtendedDbMigration extends CDbMigration
{
    protected function createLangData($name, $desc, $category)
    {
        $res = array();
        $res[$name] = Yii::t($category, $desc);
        foreach (Yii::app()->params['languages'] as $lang=>$langName) {
            $res[$name.'_'.$lang] = Yii::t($category, $desc, array(), null, $lang);
        }
        return $res;
    }

    protected function createMenu($menuArr, $items)
    {
        $menu = new Menu;
        $menu->multilang();
        $attr = array(
            'name' => $menuArr['name']
        );
        foreach (Yii::app()->params['languages'] as $lang=>$langName) {
            $attr['name_'.$lang] = $menuArr['name_'.$lang];
        }
        $menu->setAttributes($attr);
        $menu->id = $menuArr['id'];
        $menu->save();


        $orderNum = 1;
        foreach ($items as $item) {
            $menuItem = new MenuItem;
            $menuItem->multilang();
            $attr = array(
                'menuId'    => $menuArr['id'],
                'name'      => $item['name'],
                'link'      => $item['link'],
                'orderNum'  => $orderNum++,
                'active'    => 1,
                'visible'   => 1,
            );
            foreach (Yii::app()->params['languages'] as $lang=>$langName) {
                $attr['name_'.$lang] = $item['name_'.$lang];
            }
            $menuItem->setAttributes($attr);
            $menuItem->save();
        }
    }

    protected function deleteMenu($menuId)
    {
        $menuItems = MenuItem::model()->findAllByAttributes(array('menuId'=>$menuId));
        foreach ($menuItems as $item) {
            $item->delete();
        }

        Menu::model()->deleteByPk($menuId);
    }
}