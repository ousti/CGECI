<?php


require_once 'BaseController.php';

class EventController extends BaseController {
    
    
    public function listAction() { 
        $ws = new WebService();
        $f = $ws->getEventList($this->userSession['token']);
        if(!is_null($f)) :
            $this->view->events = $f->GetListResult->Datas->Datas; 
        endif;
        $table = new Application_Model_DbTable_InscriptionEvenement();
        $myEvents = $table->fetchAll("id_user = ".$this->userSession['sid'])->toArray();
        $this->view->myEvents = array();
        foreach ($myEvents as $my) {
           $this->view->myEvents[] = $my['id_evenement'];
        }
        if($fm = $this->getHelper('FlashMessenger')->getMessages()) :
            $this->view->fm = $fm[0];
        endif;
        
    }
    
    
    
    public function subscribeAction() {
       $this->view->formParams = $this->params;
       
       if($this->requestName->isPost()) {
          $table = new Application_Model_DbTable_InscriptionEvenement();
          $data = array('id_evenement' => $this->params['ide'], 'id_user' => $this->userSession['sid'], 
                        'evenement' => $this->params['l'], 'inscris_le' => date('Y-m-d H:i:s'));
          $rs = $table->insert($data);
          if($rs) {
              # Sending email to BackOffice
                $mailer = $this->config->mail;
                $mailing = new Mailing();
                $mailing->setSubject("Inscription Evenement")
                        ->setTo($mailer->admin->toArray())
                        ;
                $mailing->infoMail = array(
                                    'evenement' => $this->params['l'],
                                    'doBy' => $this->userSession['user'],
                                    'company' => $this->userSession['company'],
                                   ); 
                $mailing->setScriptHtml('event_subscription.phtml');
                try {
                      $mailing->send(); 
                      $this->logger->info('Mail Inscription Evenement '.$this->userSession['company']);
                } catch (Exception $exc) {
                      $this->logger->err("Erreur envoi de mail Inscription Evenement  ".$exc->getTraceAsString());
                }            
                $this->getHelper('FlashMessenger')->addMessage($this->config->message->event->subscribe->success);
                $this->_redirect('event/list'); 
          } else {
            $this->view->errMessage = $this->config->message->event->subscribe->error;
            $this->forward('subscribe');  
          }
       }
    }

   
  
   

}

