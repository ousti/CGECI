<?php

require_once 'BackendController.php';

class Backend_TrainingRoomController extends Backend_BackendController {

   private $config; 
   
   public function init() {
       parent::init();
       $this->config = Zend_Registry::get('config');
   }
    
    
   public function reservationAction() {
        $inscrisMapper = new Application_Model_SalleFormationMapper(array(), NULL, TRUE);
        $this->view->reservations = $inscrisMapper->findAll();
        
        if($fm = $this->getHelper('FlashMessenger')->getMessages()) :
            $this->view->fm = $fm[0];
        endif;
   } 
     
      
}

