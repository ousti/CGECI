<?php

require_once 'BackendController.php';

class Backend_TrainingController extends Backend_BackendController {

   private $config; 
   
   public function init() {
       parent::init();
       $this->config = Zend_Registry::get('config');
   }
    
    
   public function subscriberAction() {
        $inscrisMapper = new Application_Model_InscriptionFormationMapper();
        $inscris = $inscrisMapper->findAll();
        $this->view->inscris = array();
        foreach($inscris as $i) :
            $idf = $i['id_formation'];
            $this->view->inscris[$idf]['formation'] = $i['formation'];
            $this->view->inscris[$idf]['inscris'][] = $i;
        endforeach;
        
        if($fm = $this->getHelper('FlashMessenger')->getMessages()) :
            $this->view->fm = $fm[0];
        endif;
        
        /*if($status == 1) {
            $this->renderScript ('subscriber/list_active.phtml');
        } else if($status == -1) {
            $this->renderScript ('subscriber/list_desactivate.phtml');
        }
        else {
            $this->renderScript ('subscriber/list_new.phtml');
        }*/
        
        
   } 
    
   
   
    function validateAction() {
       $userTable = new Application_Model_DbTable_User();
       $this->view->user = $userTable->find($this->params['sid'])->current();
   
       if($this->getRequest()->isPost()) {
         $params = array('id_company_eudonet' => $this->params['id_company_eudonet'], 
                         'status' => 1, 
                         'updated_date' => date('Y-m-d H:i:s'));
         $result = $userTable->update($params, "id = ".$this->params['sid']);
         if($result) { 
            $mailer = $this->config->mail;
            $mailing = new Mailing();
            $mailing->setSubject("CGECI CI : confirmation d'inscription")
                    ->setTo(array($this->view->user['email']))
                    ->setBcc($mailer->admin->toArray())
                    ;
            $mailing->infoMail = array(
                                'subscriber' => $this->view->user['firstname'].' '.$this->view->user['lastname'],
                                'societe' => $this->view->user['company_name'],           
                                'login' => $this->view->user['login'],           
                            ); 
            $mailing->setScriptHtml('confirm_subscription.phtml');
            try {
                  $mailing->send(); 
                  $this->logger->info('Confirmation inscription ==> '.$this->view->user['email']);
            } catch (Zend_Mail_Exception $exc) {
                 $this->logger->err('Erreur envoi de mail au client ==> '.$this->view->user['email']. ' :::: '.print_r($exc->getTrace()));
            }       
            $this->getHelper('FlashMessenger')->addMessage('Validation adhésion effectuée avec succès! ');
            $this->redirect('backend/subscriber/list/s/1');
         } else {
            $this->view->errMessage = "La validation de l'adhésion n'a pas été prise en compte! Veuillez réessayer..."; 
         }
         
      }
    }
      
     
      
}

