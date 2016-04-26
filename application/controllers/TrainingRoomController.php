<?php


require_once 'BaseController.php';

class TrainingRoomController extends BaseController {
    
    
    public function listAction() { 
        $table = new Application_Model_DbTable_SalleFormation();
        $this->view->salles = $table->fetchAll()->toArray();
        if($fm = $this->getHelper('FlashMessenger')->getMessages()) :
            $this->view->fm = $fm[0];
        endif;
    }
    
    public function myResaAction() { 
        $rs = new Application_Model_DbTable_ReservationSalle();
        $this->view->myResas = $rs->fetchAll("id_user = ".$this->userSession['sid'])->toArray();
        if($fm = $this->getHelper('FlashMessenger')->getMessages()) :
            $this->view->fm = $fm[0];
        endif;
    }
    
    
    public function reservationAction() {
       $this->view->formParams = $this->params;
       
       if($this->requestName->isPost()) {
          $table = new Application_Model_DbTable_ReservationSalle();
          $data = array('place' => $this->params['capacite'], 'id_user' => $this->userSession['sid'], 
                        'date_debut' => $this->params['date'], 'jour' => $this->params['jour'], 
                        'heure' => $this->params['heure'], 'service' => $this->params['services'], 
                        'created_at' => date('Y-m-d H:i:s'));
          $rs = $table->insert($data);
          if($rs) {
              # Sending email to BackOffice
                $mailer = $this->config->mail;
                $mailing = new Mailing();
                $mailing->setSubject("Demande de reservation de salle")
                        ->setTo($mailer->admin->toArray())
                        ;
                $mailing->infoMail = array(
                                    'doBy' => $this->userSession['user'],
                                    'company' => $this->userSession['company'],
                                   ); 
                $mailing->setScriptHtml('reservation_room.phtml');
                try {
                      $mailing->send(); 
                      $this->logger->info('Mail Demande Réservation '.$this->userSession['company']);
                } catch (Exception $exc) {
                      $this->logger->err("Erreur envoi de mail Demande Réservation  ".$exc->getTraceAsString());
                }            
                $this->getHelper('FlashMessenger')->addMessage($this->config->message->room->reservation->success);
                $this->_redirect('training-room/my-resa'); 
          } else {
            $this->view->errMessage = $this->config->message->room->reservation->error;
            $this->forward('reservation');  
          }
       }
    }
    
    
    
    
  
   

}

