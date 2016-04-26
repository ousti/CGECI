<?php


abstract class BaseController extends Zend_Controller_Action {

     protected $requestName;
     protected $controllerName;
     protected $actionName;
     protected $params;
     protected $logger;
     protected $config;
     protected $userSession;

    /**
     *
     * @param string $method
     * @param string $args
     */
    public function __call($method, $args) {
        
    }

    public function init() {
        # Authentication Instance
        $authInstance = Zend_Auth::getInstance();
        if($authInstance->hasIdentity()) {
            $this->view->userLoggedIn = $authInstance->getIdentity();
            $this->userSession = $authInstance->getIdentity();
        } else {
            $this->redirect("/auth/login");
        }
        $this->requestName = $this->getRequest(); 
        $this->view->controllerName = $this->requestName->getControllerName();
        $this->view->actionName = $this->requestName->getActionName();
        $this->params = $this->requestName->getParams();
        
        # Define logger & config
        $this->logger = Zend_Registry::get('logger');
        $this->config = Zend_Registry::get('config');
    }
    
   
    
    
    
}

