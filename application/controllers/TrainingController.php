<?php


require_once 'BaseController.php';

class TrainingController extends BaseController {
    
    
    public function listAction() { 
        $ws = new WebService();
        $f = $ws->getTrainingList($this->userSession['token']);
        if(!is_null($f)) :
            $this->view->formations = $f->GetListResult->Datas->Datas; 
        endif;
        $table = new Application_Model_DbTable_InscriptionFormation();
        $mySubscribes = $table->fetchAll("id_user = ".$this->userSession['sid'])->toArray();
        $this->view->mySubscribes = array();
        foreach ($mySubscribes as $my) {
           $this->view->mySubscribes[] = $my['id_formation'];
        }
        if($fm = $this->getHelper('FlashMessenger')->getMessages()) :
            $this->view->fm = $fm[0];
        endif;
    }
    
    
    public function subscribeAction() {
       $this->view->formParams = $this->params;
       
       if($this->requestName->isPost()) {
          $table = new Application_Model_DbTable_InscriptionFormation();
          $data = array('id_formation' => $this->params['idf'], 'id_user' => $this->userSession['sid'], 
                        'formation' => $this->params['l'], 'inscris_le' => date('Y-m-d'));
          $rs = $table->insert($data);
          if($rs) {
              # Sending email to BackOffice
                $mailer = $this->config->mail;
                $mailing = new Mailing();
                $mailing->setSubject("Inscription Formation")
                        ->setTo($mailer->admin->toArray())
                        ;
                $mailing->infoMail = array(
                                    'formation' => $this->params['l'],
                                    'doBy' => $this->userSession['user'],
                                    'company' => $this->userSession['company'],
                                   ); 
                $mailing->setScriptHtml('training_subscription.phtml');
                try {
                      $mailing->send(); 
                      $this->logger->info('Mail Inscription Formation '.$this->userSession['company']);
                } catch (Exception $exc) {
                      $this->logger->err("Erreur envoi de mail Inscription Formation  ".$exc->getTraceAsString());
                }            
                $this->getHelper('FlashMessenger')->addMessage($this->config->message->training->subscribe->success);
                $this->_redirect('training/list'); 
          } else {
            $this->view->errMessage = $this->config->message->training->subscribe->error;
            $this->forward('subscribe');  
          }
       }
    }
    
    
    
    
  
   

}

