<?php
/**
 * Built-in client script packages.
 *
 * Please see {@link CClientScript::packages} for explanation of the structure
 * of the returned array.
 *
 * @author Ruslan Fadeev <fadeevr@gmail.com>
 *
 * @var Bootstrap $this
 */
return array(

    'jquery' => array(
        'baseUrl' => $this->getAssetsUrl(),
        'js' => array('js/jquery.js')
    ),
    'bootstrap' => array(
        'baseUrl' => $this->getAssetsUrl(),
        'js' => array('js/bootstrap.js'),
        'depends' => array('bootstrap-css'),
    ),
    'bootstrap-css' => array(
        'baseUrl' => $this->getAssetsFontsUrl(),
        'css' => array('bootstrap.css')
    ),
    'ace' => array(
        'baseUrl' => $this->getAssetsUrl(),
        'js' => array('js/ace-extra.js', 'js/ace-elements.js', 'js/ace.js'),
        'css' => array('css/ace.css'),
        'depends' => array('jquery', 'bootstrap', 'ace-fonts')
    ),
    'ace-fonts' => array(
        'baseUrl' => $this->getAssetsFontsUrl(),
        'css' => array('ace-fonts.css')
    ),
    'font-awesome' => array(
        'baseUrl' => $this->getAssetsFontsUrl(),
        'css' => array('font-awesome.css')
    ),
    'bootbox' => array(
        'baseUrl' => $this->getAssetsUrl(),
        'js' => array('js/bootbox.js'),
        'depends' => array('jquery', 'bootstrap')
    ),
    'select2' => array(
        'baseUrl' => $this->getAssetsUrl(),
        'css' => array('css/select2.css'),
        'js' => array('js/select2.js'),
        'depends' => array('jquery')
    ),
    'ckeditor' => array(
        'baseUrl' => $this->getAssetsUrl() . '/ckeditor',
        'js' => array('ckeditor.js')
    ),
    'x-editable' => array(
        'baseUrl' => $this->getAssetsUrl(),
        'js' => array('js/x-editable/bootstrap-editable.js', 'js/x-editable/ace-editable.js'),
        'depends' => array('jquery', 'bootstrap', 'datepicker', 'x-editable-css')
    ),
    'x-editable-css' => array(
        'baseUrl' => $this->getAssetsFontsUrl(),
        'css' => array('bootstrap-editable.css')
    ),
    // //widgets start
    // 'ui-layout' => array(
    //     'baseUrl' => $this->getAssetsUrl() . '/ui-layout/',
    //     'css' => array('css/layout-default.css'),
    //     'js' => array($this->minify ? 'js/jquery.layout.min.js' : 'js/jquery.layout.js'),
    //     'depends' => array('jquery', 'jquery.ui'),
    // ),
    'datepicker' => array(
        'depends' => array('jquery'),
        'baseUrl' => $this->getAssetsUrl() . '/bootstrap-datepicker/',
        'css' => array($this->minify ? 'css/datepicker.min.css' : 'css/datepicker.css'),
        'js' => array($this->minify ? 'js/bootstrap-datepicker.min.js' : 'js/bootstrap-datepicker.js', 'js/bootstrap-datepicker-noconflict.js') 
        // ... the noconflict code is in its own file so we do not want to touch the original js files to ease upgrading lib
    ),
    // 'datetimepicker' => array(
    //     'depends' => array('jquery'),
    //     'baseUrl' => $this->getAssetsUrl() . '/bootstrap-datetimepicker/', // Not in CDN yet
    //     'css' => array($this->minify ? 'css/bootstrap-datetimepicker.css' : 'css/bootstrap-datetimepicker.css'),
    //     'js' => array($this->minify ? 'js/bootstrap-datetimepicker.min.js' : 'js/bootstrap-datetimepicker.js')
    // ),
    // 'date' => array(
    //     'baseUrl' => $this->enableCdn ? '//cdnjs.cloudflare.com/ajax/libs/datejs/1.0/' : $this->getAssetsUrl() . '/js/',
    //     'js' => array('date.min.js')
    // ),

    // 'moment' => array(
    //     'baseUrl' => $this->getAssetsUrl(),
    //     'js' => array('js/moment.min.js'),
    // ),
    // 'picker' => array(
    //     'baseUrl' => $this->getAssetsUrl() . '/picker',
    //     'js' => array('bootstrap.picker.js'),
    //     'css' => array('bootstrap.picker.css'),
    //     'depends' => array('bootstrap.js')
    // ),
    // 'bootstrap.wizard' => array(
    //     'baseUrl' => $this->getAssetsUrl() . '/bootstrap-wizard',
    //     'js' => array($this->minify ? 'jquery.bootstrap.wizard.min.js' : 'jquery.bootstrap.wizard.js')
    // ),
    // 'ajax-cache' => array(
    //     'baseUrl' => $this->getAssetsUrl() . '/ajax-cache',
    //     'js' => array('jquery.ajax.cache.js'),
    // ),
    // 'jqote2' => array(
    //     'baseUrl' => $this->getAssetsUrl() . '/jqote2',
    //     'js' => array('jquery.jqote2.min.js'),
    // ),
    // 'json-grid-view' => array(
    //     'baseUrl' => $this->getAssetsUrl() . '/json-grid-view',
    //     'js' => array('jquery.json.yiigridview.js'),
    //     'depends' => array('jquery', 'jqote2', 'ajax-cache')
    // ),
    // 'group-grid-view' => array(
    //     'baseUrl' => $this->getAssetsUrl() . '/group-grid-view',
    //     'js' => array('jquery.group.yiigridview.js'),
    //     'depends' => array('jquery', 'jqote2', 'ajax-cache')
    // ),
    // 'redactor' => array(
    //     'baseUrl' => $this->getAssetsUrl() . '/redactor',
    //     'js' => array($this->minify ? 'redactor.min.js' : 'redactor.js'),
    //     'css' => array('redactor.css'),
    //     'depends' => array('jquery')
    // ),
    // 'passfield' => array(
    //     'depends' => array('jquery'),
    //     'baseUrl' => $this->getAssetsUrl() . '/bootstrap-passfield', // Not in CDN yet
    //     'css' => array($this->minify ? 'css/passfield.min.css' : 'css/passfield.min.css'),
    //     'js' => array($this->minify ? 'js/passfield.min.js' : 'js/passfield.min.js')
    // ),
    'timepicker' => array(
        'baseUrl' => $this->getAssetsUrl() . '/bootstrap-timepicker',
        'js' => array($this->minify ? 'js/bootstrap-timepicker.min.js' : 'js/bootstrap-timepicker.js'),
        'css' => array($this->minify ? 'css/bootstrap-timepicker.min.css' : 'css/bootstrap-timepicker.css'),
        'depends' => array('bootstrap')
    ),
    // 'highcharts' => array(
    //     'baseUrl' => $this->enableCdn ? '//code.highcharts.com' : $this->getAssetsUrl() . '/highcharts',
    //     'js' => array($this->minify ? 'highcharts.js' : 'highcharts.src.js')
    // ),
    'nestable-list' => array(
         'baseUrl' => $this->getAssetsUrl(),
         'js' => array('js/jquery.nestable.js'),
         'depends' => array('jquery')
    ),
    'translate' => array(
        'baseUrl' => $this->getAssetsUrl(),
        'js' => array('js/translate.js'),
        'depends' => array('jquery', 'bootstrap')
    ),
);
