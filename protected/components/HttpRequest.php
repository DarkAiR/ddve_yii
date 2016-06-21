<?php

class HttpRequest extends CHttpRequest
{
    private $_csrfToken = null;

    public function getRequestUri()
    {
        return parent::getRequestUri();
    }

    protected function normalizeRequest()
    {
        // normalize request
        if(function_exists('get_magic_quotes_gpc') && get_magic_quotes_gpc())
        {
            if(isset($_GET))
                $_GET=$this->stripSlashes($_GET);
            if(isset($_POST))
                $_POST=$this->stripSlashes($_POST);
            if(isset($_REQUEST))
                $_REQUEST=$this->stripSlashes($_REQUEST);
            if(isset($_COOKIE))
                $_COOKIE=$this->stripSlashes($_COOKIE);
        }
    }

    /**
     * Returns the random token used to perform CSRF validation.
     * The token will be read from cookie first. If not found, a new token
     * will be generated.
     * @return string the random token for CSRF validation.
     * @see enableCsrfValidation
     */
    public function getCsrfToken()
    {
        $session = Yii::app()->session;
        if ($this->_csrfToken===null) {
            $sessionToken =  sha1(uniqid(mt_rand(),true));
            $session->add($this->csrfTokenName, $sessionToken);
            $this->_csrfToken = $sessionToken;
        }
        return $this->_csrfToken;
    }

    /**
     * Performs the CSRF validation.
     * This is the event handler responding to {@link CApplication::onBeginRequest}.
     * The default implementation will compare the CSRF token obtained
     * from a cookie and from a POST field. If they are different, a CSRF attack is detected.
     * @param CEvent $event event parameter
     * @throws CHttpException if the validation fails
     */
    public function validateCsrfToken($event=null)
    {
        $valid = false;

        if ($this->getIsPostRequest() ||
            $this->getIsPutRequest() ||
            $this->getIsPatchRequest() ||
            $this->getIsDeleteRequest())
        {
            $session = Yii::app()->session;

            $method = $this->getRequestType();
            switch ($method) {
                case 'POST':
                    $userToken = $this->getPost($this->csrfTokenName);
                    break;
                case 'PUT':
                    $userToken = $this->getPut($this->csrfTokenName);
                    break;
                case 'PATCH':
                    $userToken = $this->getPatch($this->csrfTokenName);
                    break;
                case 'DELETE':
                    $userToken = $this->getDelete($this->csrfTokenName);
                    break;
            }

            if (!empty($userToken) && $session->contains($this->csrfTokenName)) {
                $sessionToken = $session->itemAt($this->csrfTokenName);
                $valid = ($sessionToken === $userToken);
            }
        }
        return $valid;
    }

    /**
     * Remove language from url
     */
    public function getUrlWithoutLanguage()
    {
        $url = Yii::app()->request->getPathInfo();
        $domains = explode('/', ltrim($url, '/'));
        if (in_array($domains[0], array_keys(Yii::app()->params['languages']))) {
            array_shift($domains);
            $url = implode('/', $domains);
        }
        return $url;
    }
}
