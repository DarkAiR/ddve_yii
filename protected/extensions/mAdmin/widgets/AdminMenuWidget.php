<?php

Yii::import('zii.widgets.CMenu');

class AdminMenuWidget extends CMenu
{
    public $vertical = false;
    private $level = 0;

    /**
     * Initializes the widget.
     */
    public function init()
    {
        parent::init();

        if (!$this->vertical) {
            $this->submenuHtmlOptions = array(
                'class' => "dropdown-menu dropdown-light-blue dropdown-caret submenu-right"
            );
        } else {
            $this->submenuHtmlOptions = array(
                'class' => 'submenu'
            );
        }
        $this->itemCssClass = 'dropdown';

        $classes = array('nav');
        $classes[] = $this->vertical ? 'nav-list' : 'navbar-nav';

        if (!empty($classes)) {
            $classes = implode(' ', $classes);
            if (isset($this->htmlOptions['class']))
                $this->htmlOptions['class'] .= ' ' . $classes;
            else
                $this->htmlOptions['class'] = $classes;
        }
    }

    /**
     * Renders the content of a menu item.
     * Note that the container and the sub-menus are not rendered here.
     * @param array $item the menu item to be rendered. Please see {@link items} on what data might be in the item.
     * @return string the rendered item
     */
    protected function renderMenuItem($item)
    {
        if (isset($item['icon'])) {
            if (strpos($item['icon'], 'icon') === false && strpos($item['icon'], 'fa') === false)
                $item['icon'] = 'fa fa-' . implode(' fa-', explode(' ', $item['icon']));
            $item['label'] = '<i class="' . $item['icon'] . '"></i> ' . $item['label'];
        }

        if (!isset($item['linkOptions']))
            $item['linkOptions'] = array();

        if (isset($item['items']) && !empty($item['items'])) {
            if (empty($item['url']))
                $item['url'] = '#';

            if (isset($item['linkOptions']['class']))
                $item['linkOptions']['class'] .= ' dropdown-toggle';
            else
                $item['linkOptions']['class'] = 'dropdown-toggle';

            $item['linkOptions']['data-toggle'] = 'dropdown';
            if (!$this->vertical)
                $item['label'] .= ' <span class="caret"></span>';
        }

        if (isset($item['url'])) {
            $text = $item['label'];
            if ($this->vertical) {
                if (isset($item['items']) && count($item['items'])) {
                    $text .= '<b class="arrow fa fa-angle-down"></b>';
                }
                // Пропускаем первые два уровня, на остальных делаем стрелки
                for ($i=2; $i < $this->level; $i++) { 
                    $text = '<i class="menu-icon fa fa-caret-right"></i>' . $text;
                }
            }
            return CHtml::link($text, $item['url'], $item['linkOptions']);
        }
        return $item['label'];
    }

    /**
     * Recursively renders the menu items.
     * @param array $items the menu items to be rendered recursively
     */
    protected function renderMenuRecursive($items)
    {
        $this->level++;
        parent::renderMenuRecursive($items);
        $this->level--;
    }
}