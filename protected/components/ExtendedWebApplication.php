<?php

class ExtendedWebApplication extends CWebApplication
{
    const BLANK_TEMPLATE_NAME = 'blank';
    const USER_ID_COOKIE_NAME = 'authorizedUserId';

    public function run()
    {
        if (YII_DEBUG)
            $this->initErrorHandlers();

        return parent::run();
    }

    function handleShutdown()
    {
        $errfile = "unknown file";
        $errstr  = "shutdown";
        $errno   = E_CORE_ERROR;
        $errline = 0;

        $error = error_get_last();

        if( $error !== NULL) {
            $errno   = $error["type"];
            $errfile = $error["file"];
            $errline = $error["line"];
            $errstr  = $error["message"];

            if (YII_DEBUG) {
                echo '<pre>';
                print_r($error);
                debug_print_backtrace();
            }
            if (Yii::app()->request->isAjaxRequest)
                http_response_code($errno);
        }
    }

    private function initErrorHandlers()
    {
        error_reporting(E_ALL | E_STRICT);
        ini_set('display_errors','On');
        ini_set('log_errors','On');

        set_exception_handler(array($this, 'handleException'));
        set_error_handler(array($this, 'handleError'), error_reporting());
        register_shutdown_function(array($this, 'handleShutdown'));
    }
/*
    public function handleException($exception)
    {
        echo '<pre>';
        print_r($exception);
        debug_print_backtrace();
    }

    public function handleError($code,$message,$file,$line)
    {
        echo '<pre>';
        var_dump($code,$message,$file,$line);
        debug_print_backtrace();
    }
*/

    public function getAdminComponent()
    {
        return $this->getComponent('adminComponent');
    }
}