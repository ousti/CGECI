<?php

require_once 'BackendController.php';

class Backend_SubscriberController extends Backend_BackendController {

   private $config; 
   
   public function init() {
       parent::init();
       $this->config = Zend_Registry::get('config');
   }
    
    
   public function listAction() {
        $status = (int)$this->params['s'];
        
        $userTable = new Application_Model_DbTable_User();
        $cond = " status = ".$status; 
        $this->view->usersList = $userTable->fetchAll($cond);
        
        if($fm = $this->getHelper('FlashMessenger')->getMessages()) :
            $this->view->fm = $fm[0];
        endif;
        
        if($status == 1) {
            $this->renderScript ('subscriber/list_active.phtml');
        } else if($status == -1) {
            $this->renderScript ('subscriber/list_desactivate.phtml');
        }
        else {
            $this->renderScript ('subscriber/list_new.phtml');
        }
        
        
   } 
    
   
   
    function validateAction() {
       $userTable = new Application_Model_DbTable_User();
       $this->view->user = $userTable->find($this->params['sid'])->current();
   
       if($this->getRequest()->isPost()) {
         $idCompanyEudonet = (int)$this->params['id_company_eudonet'];
         if($idCompanyEudonet == 0) {
             $this->view->errMessage = "Veuillez saisir l'identifiant Eudonet ...";
             return;
         }
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
      
     
    public function addAction() {
       if($this->request->isPost()) {
         $endTime = "14:00";
         $dateReunion = Misc_Utils::getEnDateFromDatePicker($this->params['date_reunion']);
         $resaTable = new Application_Model_DbTable_Reservation();
         $params = array(
                        'user_id' => $this->view->userLoggedIn['cid'],
                        'reservation_date' => $dateReunion,
                        'start_time' => $this->params['heure_debut'],
                        'end_time' => $endTime,
                        'meeting_place_id' => $this->params['meetingPlace'],
                        'meeting_type_id' => $this->params['meetingType'],
                        'participants_number' => $this->params['nombreParticipants'],
                        'commercial_firstname' => $this->params['commercial_firstname'],
                        'commercial_lastname' => $this->params['commercial_lastname'],
                        'commercial_phones' => $this->params['commercial_phones'],
                        'commercial_email' => $this->params['commercial_email'],
                        'status' => 0,
                        'created_by' => $this->view->userLoggedIn['user'],
                        'created_date' => date('Y-m-d H:i:s'),
                   );
         # Insert Reservation
         $resultResaId = $resaTable->insert($params);
         if($resultResaId and count($this->params['meetingOption'])) {
             $resaOptionObj = new Application_Model_DbTable_ReservationOption();
             foreach($this->params['meetingOption'] as $option) {
                 $resaOptionObj->insert(array('reservation_id'=>$resultResaId, 
                                              'meeting_option_id'=>$option));
             }
             $this->redirect('/reservation/list');
         }
         
      }
      else {
          $this->view->errMessage = "Votre demande n'a pas été prise en compte! Veuillez réessayer...";
          $this->forward('new');
      }
      
    }
   
    
  
      
}

