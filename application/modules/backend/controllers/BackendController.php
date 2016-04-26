<?php



abstract class Backend_BackendController extends Zend_Controller_Action {
    
    protected $userIdConnected;
    protected $level;
    protected $logger;
    protected $db;
    protected $requestName;
    protected $controllerName;
    protected $params;
    protected $admin;
    const LAYOUT_PATH = '/modules/backend/layouts/scripts/';
    
    public function init() {
        parent::init();
        
        # Bases Object 
        $this->requestName = $this->getRequest();       
        $this->params = $this->requestName->getParams(); 
        $this->logger = Zend_Registry::get('logger');
        $this->boostrap = $this->getInvokeArg('bootstrap');
        $this->db = $this->boostrap->getPluginResource('db')->getDbAdapter();
        $this->options = $this->boostrap->getOptions();
        
        # Authentication Instance
        $authInstance = Zend_Auth::getInstance();  
        if($authInstance->hasIdentity()) {
            $identity = $authInstance->getIdentity();
            $this->admin = (int)$identity['admin']; 
            $this->view->userLoggedIn = true;
        } else {
            // throw error :: user non connecté   
            $this->redirect("/backend/auth/login");
        }

        // ControllerName
        $this->view->controllerName = $this->requestName->getControllerName();
        $this->view->actionName = $this->requestName->getActionName();
        
        # parent::init();
        $layout = Zend_Layout::getMvcInstance();
        // Set a layout script path:
        $layout->setLayoutPath(APPLICATION_PATH.self::LAYOUT_PATH);
        // choose a different layout script:
        $layout->setLayout('layout_backend');        
        
    }

  

}

