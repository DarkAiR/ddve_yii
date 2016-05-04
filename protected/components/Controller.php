<?php
/**
 * Controller is the customized base controller class.
 * All controller classes for this application should extend from this base class.
 */
class Controller extends CController
{
    /**
     * @var string the default layout for the controller view. Defaults to '//layouts/column1',
     * meaning using a single column layout. See 'protected/views/layouts/column1.php'.
     */
    public $layout = false;
    /**
     * @var array context menu items. This property will be assigned to {@link CMenu::items}.
     */
    public $menu = array();
    /**
     * @var array the breadcrumbs of the current page. The value of this property will
     * be assigned to {@link CBreadcrumbs::links}. Please refer to {@link CBreadcrumbs::links}
     * for more details on how to specify this property.
     */
    public $breadcrumbs = array();

    protected $_canonicalUrl;


    public function init()
    {    
        // Регистрируем все необходимые скрипты
        Yii::app()->getClientScript()->registerCoreScript('jquery');

        // Принудительно ставим язык в GET параметры и для всего сайта
        if (empty($_GET['language']))
            $_GET['language'] = Yii::app()->sourceLanguage;
        Yii::app()->language = $_GET['language'];
        
        // Выставляем заголовок страницы
        $pageTitle = Yii::app()->name;
        if (!empty($this->breadcrumbs)) {
            $pageTitle .= ' - '. implode(' / ', array_keys($this->breadcrumbs));
        }
        $this->setPageTitle($pageTitle);

        parent::init();
    }

    protected function afterRender($view, &$output)
    {
        $output = LocalConfigHelper::parseText($output);
    }

    /**
     * Default canonical url generator, will remove all get params beside 'id' and generates an absolute url.
     * If the canonical url was already set in a child controller, it will be taken instead.
     */
    public function getCanonicalUrl()
    {
        if ($this->_canonicalUrl === null) {
            $params = array();
            if (isset($_GET['id'])) {
                //just keep the id, because it identifies our model pages
                $params = array('id' => $_GET['id']);
            }
            $this->_canonicalUrl = Yii::app()->createAbsoluteUrl($this->route, $params);
        }
        return $this->_canonicalUrl;
    }

    /**
     * Заполняем breadcrumbs
     */
    public function setBreadcrumbs($links, $useHomeLink = true)
    {
        if ($useHomeLink) {
            $this->breadcrumbs = array_merge(array(
                Yii::t('app', 'Главная') => Yii::app()->homeUrl
            ), $links);
        } else {
            $this->breadcrumbs = $links;
        }
    }
}