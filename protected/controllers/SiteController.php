<?php

class SiteController extends Controller
{
    public function actionIndex()
    {
        $this->render('index', array(
        ));
    }

    /**
     * This is the action to handle external exceptions.
     */
    public function actionError()
    {
        // Для админской страницы редиректим на другую обработку ошибок
        $pathParts = explode('/', Yii::app()->request->getPathInfo());
        if ($pathParts[0] == 'admin') {
            Yii::app()->runController('admin/admin/error');
        } else {
            $error = Yii::app()->errorHandler->error;
            if ($error) {
                if (Yii::app()->request->isAjaxRequest) {
                    echo $error['message'];
                } else {
                    $this->render('error', $error);
                }
            }
        }
    }

    /**
     * Displays the login page
     */
    public function actionLogin()
    {
        $model = new LoginForm;

        // if it is ajax validation request
        if (isset($_POST['ajax']) && $_POST['ajax'] === 'login-form') {
            echo CActiveForm::validate($model);
            Yii::app()->end();
        }

        // collect user input data
        if (isset($_POST['LoginForm'])) {
            $model->attributes = $_POST['LoginForm'];
            // validate user input and redirect to the previous page if valid
            if ($model->validate() && $model->login()) {
                $this->redirect(Yii::app()->user->getReturnUrl(array('site/index')));
            }
        }
        // display the login form
        $this->render('login', array('model' => $model));
    }

    /**
     * Logs out the current user and redirect to homepage.
     */
    public function actionLogout()
    {
        Yii::app()->user->logout();
        $this->redirect(Yii::app()->homeUrl);
    }
}
