<?php

// require_once 'BaseController.php';

class IndexController extends Zend_Controller_Action {

   private $token;
   
   public function init(){
       $this->token = Utils::getInitToken();
       parent::init();
   }
   
   
   public function indexAction()
   {
        
        $ws = new WebService();
        $today = date('Y-m-d'); 
        $this->view->formations = array();
        $this->view->events = array();
        
        $f = $ws->getTrainingList($this->token); 
        if(!is_null($f)) :
            $formations = $f->GetListResult->Datas->Datas;
            foreach($formations as $item) :
                //$compareDate = Utils::compareDate($today, $item->FieldsValue->FieldValues[4]->Value);
                if(true)
                    $this->view->formations[] = $item; 
            endforeach;
        endif;        
        
        $f = $ws->getEventList($this->token);
        if(!is_null($f)) :
            $events = $f->GetListResult->Datas->Datas;
            foreach($events as $item) :
                //$compareDate = Utils::compareDate($today, $item->FieldsValue->FieldValues[3]->Value);
                if(true)
                    $this->view->events[] = $item; 
            endforeach;
        endif;
        
        $table = new Application_Model_DbTable_SalleFormation();
        $this->view->salles = $table->fetchAll()->toArray();
        
        if($fm = $this->getHelper('FlashMessenger')->getMessages()) :
            $this->view->fm = $fm[0];
        endif;
        
   } 
    
    
    public function trainingDetailAction() {
        $id = $this->getRequest()->getParam("idf");
        $ws = new WebService();
        $d = $ws->getTrainingDetail($id, $this->token);        
        $this->view->training = $d->SearchByIdResult->Results->EVENTData->enc_value;
        //Zend_Debug::dump($d);
    }
    
    public function eventDetailAction() {
        $id = $this->getRequest()->getParam("ide");
        $ws = new WebService();
        $d = $ws->getEventDetail($id, $this->token);        
        $this->view->event = $d->SearchByIdResult->Results->EVENT_13Data->enc_value;
        Zend_Debug::dump($d);
    }
    
    
    public function reservationRoomAction() {
        $this->params = $this->getRequest()->getParams();
        //$this->view->formParams = $this->getRequest()->getParams();
       
       if($this->getRequest()->isPost()) {
          $table = new Application_Model_DbTable_ReservationSalle();
          $data = array('reservant' => $this->params['fullname'], 'phone' => $this->params['phone'], 
                        'place' => $this->params['capacite'], 'id_user' => 0, 
                        'date_debut' => $this->params['date'], 'jour' => $this->params['jour'], 
                        'heure' => $this->params['heure'], 'service' => $this->params['services'], 
                        'created_at' => date('Y-m-d H:i:s'));
          $rs = $table->insert($data);
          if($rs) {
                $this->config = Zend_Registry::get('config');
                $this->logger = Zend_Registry::get('logger');
                # Sending email to BackOffice
                $mailer = $this->config->mail;
                $mailing = new Mailing();
                $mailing->setSubject("Demande de reservation de salle")
                        ->setTo($mailer->admin->toArray())
                        ;
                $mailing->infoMail = array(
                                    'doBy' => $this->params['fullname'],
                                    'company' => 'Société anonyme',
                                   ); 
                $mailing->setScriptHtml('reservation_room.phtml');
                try {
                      $mailing->send(); 
                      $this->logger->info('Mail Demande Réservation de '.$this->params['fullname']);
                } catch (Exception $exc) {
                      $this->logger->err("Erreur envoi de mail Demande Réservation  ".$exc->getTraceAsString());
                }            
                $this->getHelper('FlashMessenger')->addMessage($this->config->message->room->reservation->success);
                $this->_redirect('index/index'); 
          } else {
            $this->view->errMessage = $this->config->message->room->reservation->error;
            $this->forward('reservation-room');  
          }
       }
    }
    
    
    
    
    
}

