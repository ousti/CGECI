<?php

require_once 'BackendController.php';

class Backend_EventController extends Backend_BackendController {

   private $config; 
   
   public function init() {
       parent::init();
       $this->config = Zend_Registry::get('config');
   }
    
    
   public function subscriberAction() {
        $inscrisMapper = new Application_Model_InscriptionEvenementMapper();
        $inscris = $inscrisMapper->findAll();
        $this->view->inscris = array();
        foreach($inscris as $i) :
            $ide = $i['id_evenement'];
            $this->view->inscris[$ide]['evenement'] = $i['evenement'];
            $this->view->inscris[$ide]['inscris'][] = $i;
        endforeach;
        
        if($fm = $this->getHelper('FlashMessenger')->getMessages()) :
            $this->view->fm = $fm[0];
        endif;
        
   } 
    
   
   
   
    
  
      
}

