<?php

class AdminComponent extends CApplicationComponent
{
    /**
     * @var array list of script packages (name=>package spec).
     */
    public $packages = array();

    public $minify = false;

    /**
     * @var CClientScript Something which can register assets for later inclusion on page.
     */
    public $assetsRegistry;

    public $_assetsUrl;
    public $_assetsFontsUrl;

    /**
     * @var AdminComponent
     */
    private static $_instance;


    public function init()
    {
        // Prevents the extension from registering scripts and publishing assets when ran from the command line.
        if ($this->isInConsoleMode())
            return;

        self::setInstance($this);
        $this->setRootAliasIfUndefined();
        $this->setAssetsRegistryIfNotDefined();
        $this->includeAssets();
        parent::init();
    }

    protected function isInConsoleMode()
    {
        return Yii::app() instanceof CConsoleApplication || PHP_SAPI == 'cli';
    }

    protected function setRootAliasIfUndefined()
    {
        if (Yii::getPathOfAlias('adminComponent') === false)
            Yii::setPathOfAlias('adminComponent', realpath(dirname(__FILE__) . '/..'));
    }

    protected function includeAssets()
    {
        $this->appendUserSuppliedPackagesToOurs();
        $this->addOurPackagesToYii();
        $this->registerCssPackagesIfEnabled();
    }

    protected function appendUserSuppliedPackagesToOurs()
    {
        $packages = require(Yii::getPathOfAlias('adminComponent.components') . '/packages.php');
        $this->packages = CMap::mergeArray(
            $packages,
            $this->packages
        );
    }

    protected function addOurPackagesToYii()
    {
        foreach ($this->packages as $name => $definition)
            $this->assetsRegistry->addPackage($name, $definition);
    }

    protected function registerCssPackagesIfEnabled()
    {
        if (Yii::app()->request->isAjaxRequest)
            return;

        $this->assetsRegistry->registerPackage('font-awesome');
        $this->assetsRegistry->registerPackage('ace');
    }

    public function registerAssetCss($name, $media = '')
    {
        $this->assetsRegistry->registerCssFile($this->getAssetsUrl() . "/css/{$name}", $media);
    }

    public function registerAssetJs($name, $position = CClientScript::POS_END)
    {
        $this->assetsRegistry->registerScriptFile($this->getAssetsUrl() . "/js/{$name}", $position);
    }

    public function getAssetsUrl()
    {
        if (isset($this->_assetsUrl))
            return $this->_assetsUrl;
        return $this->_assetsUrl = Yii::app()->getAssetManager()->publish(
            Yii::getPathOfAlias('adminComponent.assets')
        );
    }

    public function getAssetsFontsUrl()
    {
        if (isset($this->_assetsFontsUrl))
            return $this->_assetsFontsUrl;
        return $this->_assetsFontsUrl = Yii::app()->getAssetManager()->publish(
            Yii::getPathOfAlias('adminComponent.assets.css')
        );
    }

    protected function setAssetsRegistryIfNotDefined()
    {
        if (!$this->assetsRegistry)
            $this->assetsRegistry = Yii::app()->getClientScript();
    }

    public function registerPackage($name)
    {
        return $this->assetsRegistry->registerPackage($name);
    }

    public static function setInstance($value)
    {
        if ($value instanceof AdminComponent)
            self::$_instance = $value;
    }

    public static function getInstance()
    {
        if (self::$_instance === null) {
            // Lets find inside current module
            $module = Yii::app()->getController()->getModule();
            if ($module) {
                if ($module->hasComponent('adminComponent')) {
                    self::$_instance = $module->getComponent('adminComponent');
                }
            }
            // Still nothing?
            if (self::$_instance === null) {
                if (Yii::app()->hasComponent('adminComponent')) {
                    self::$_instance = Yii::app()->getComponent('adminComponent');
                }
            }
        }
        return self::$_instance;
    }
}
