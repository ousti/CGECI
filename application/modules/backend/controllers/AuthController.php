<?php

class Backend_AuthController extends Zend_Controller_Action {

   const ACCOUNT = 'admin@cgeci';
   const PWD = 'pwd@cgeci';
   
   public function loginAction()
    {
        if($this->getRequest()->isPost()){
            $params = $this->getRequest()->getParams();
            if($params['login']==self::ACCOUNT and $params['pwd']==self::PWD) {
                $storage = new Zend_Auth_Storage_Session();
                $storage->write(array('id'=>rand(1,10), 
                                      'admin'=> 'Administrator'));
                $this->_redirect('/backend/subscriber/list/s/0');
            } else {
               $this->view->errorMessage = "Paramètres incorrect! Veuillez saisir des accès corrects";  
            }
        }
         $layout = Zend_Layout::getMvcInstance();
         $layout->setLayoutPath(APPLICATION_PATH.'/modules/backend/layouts/scripts/');
         $layout->setLayout('auth_layout');
         
    } 
    
    public function logoutAction()
    {
        Zend_Auth::getInstance()->clearIdentity();
        Zend_Session::destroy();
        $this->_redirect('/backend/auth/login');
    }
    
}

