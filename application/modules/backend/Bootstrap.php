<?php

//require_once 'misc/Utils.php';
//require_once 'misc/Mailing.php';

class Backend_Bootstrap extends Zend_Application_Module_Bootstrap {
    
    
    protected function _initLog()
    {
        /*$options = $this->getOption('resources');
        $logOptions = $options['log'];
        $logger = Zend_Log::factory($logOptions);
        Zend_Registry::set('logger', $logger);*/
    }
    
    /*
    protected function _initDoctype() {
      $this->bootstrap( 'view' );
      $view = $this->getResource( 'view' );
      $view->navigation = array();
      $view->subnavigation = array();
      $view->headTitle( 'Module One' );
      $view->headLink()->appendStylesheet('/css/clear.css');
      $view->headLink()->appendStylesheet('/css/main.css');
      $view->headScript()->appendFile('/js/jquery.js');
      $view->doctype( 'XHTML1_STRICT' );
      //$view->navigation = $this->buildMenu();
   }*/
    
    
    protected function _initLayout(){
        
    }
    

}

