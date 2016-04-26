<?php

require_once 'BaseController.php';

class MyaccountController extends BaseController {

    
    
   
    
   public function indexAction() {
        $userTable = new Application_Model_DbTable_User();
        $this->view->info = $userTable->find($this->userSession['sid'])->current()->toArray();
        
        $ws = new WebService();
        try {
             $rs = $ws->getCompanyInfo($this->userSession['id_company_eudonet'], $this->userSession['token']);
        } catch(Exception $e) {
            $this->errMessage = $e->getMessage();
        }
        if($rs->SearchByIdResult -> Results) {
            $data = $rs->SearchByIdResult->Results->PMData->enc_value;
            $this->view->infoCompany = $data;
        } else {
            $this->view->infoCompany = NULL;
        }
        
        if($fm = $this->getHelper('FlashMessenger')->getMessages()) :
            $this->view->fm = $fm[0];
        endif;
   } 
   
   
   /*
    * Updating an account
    */
   public function editAction() {
       
        $ws = new WebService();
        try {
             $rs = $ws->getCompanyInfo($this->userSession['id_company_eudonet'], $this->userSession['token']);
        } catch(Exception $e) {
            $this->errMessage = $e->getMessage();
        }
        if($rs->SearchByIdResult -> Results) {
            $data = $rs->SearchByIdResult->Results->PMData->enc_value;
            $this->view->infoCompany = $data;
        } else {
            $this->view->infoCompany = NULL;
        }
   }
   
   
   public function updateAction() {
        $ws = new WebService();
        try {
             $rs = $ws->updateCompanyInfo($this->userSession['id_company_eudonet'], $this->userSession['token'], $this->params);
             $isUpdate =  $rs->UpdateResult->Success;
             if($isUpdate) {
                # Sending email to BackOffice & Customer List
                $mailer = $this->config->mail;
                $mailing = new Mailing();
                $mailing->setSubject("Modification Information Société")
                        ->setTo($mailer->admin->toArray())
                        ;
                $mailing->infoMail = array(
                                    'doBy' => $this->userSession['user'],
                                    'company' => $this->userSession['company'],
                                   ); 
                $mailing->setScriptHtml('alert_update_company_info.phtml');
                try {
                      $mailing->send(); 
                      $this->logger->info('Mail Update Info company OK '.$this->userSession['company']);
                } catch (Exception $exc) {
                      $this->logger->err("Erreur envoi de mail update Info company  ".$exc->getTraceAsString());
                }            
                $this->logger->info("Modification Infos societé :[".$this->params['PM01']." ID:[".$this->userSession['id_company_eudonet']."]");
                $this->getHelper('FlashMessenger')->addMessage($this->config->message->myaccount->update->success);
                $this->_redirect('myaccount/index'); 
             } else {
                 //$this->view->errMessage = $this->config->message->myaccount->update->error;
                 $this->view->errMessage = $rs;
             }

        } catch(Exception $e) {
            $this->view->errMessage = $e->getMessage();
        }
        $this->forward('edit');
   }
   
   
   
    
   
    
   
    
    
    
}

