<?php


class AccountController extends Zend_Controller_Action {

   private $logger;
   private $config; 
   
   public function init() {
       parent::init();
       $this->config = Zend_Registry::get('config');
       $this->logger = Zend_Registry::get('logger');
   }
    
    
   public function createAction() {
      
      # Post parameter
      if($this->getRequest()->isPost()){
          $params = $this->getRequest()->getParams();
          $userTable = new Application_Model_DbTable_User();
          $parameters = array(
                        'firstname' => $params['firstname'],
                        'lastname' => $params['lastname'],
                        'company_name' => $params['company_name'],
                        'address' => $params['address'],
                        'phone_number' => $params['phones'],
                        'id_company_eudonet' => 0,
                        'email' => $params['email'],
                        'login' => $params['login'],
                        'pwd' => $params['pwd'],
                        'status' => 0,
                        'created_date' => date('Y-m-d H:i:s')
                    ); 
          $ret = $userTable->insert($parameters);
          if($ret) {
            # Logger 
            $this->logger->info("Enregistrement d'un nouvel adherent :[".$params['firstname'].' '.$params['lastname']."], Societe:[".$params['company_name']."]");
            # Sending email to BackOffice & Customer List
            $mailer = $this->config->mail;
            $mailing = new Mailing();
            $mailing->setSubject("Enregistrement d'une nouvelle inscription")
                    ->setTo($mailer->admin->toArray())
                    ;
            $mailing->infoMail = array(
                                'user' => $params['firstname'].' '.$params['lastname'],
                                'company' => $params['company_name'],           
                                'email' => $params['email'],           
                                'phones' => $params['phones']           
                            ); 
            $mailing->setScriptHtml('new_user.phtml');
            try {
                  $mailing->send(); 
                  $this->logger->info('Mail Enregistrement nouvel adherent OK');
            } catch (Exception $exc) {
                  $this->logger->err("Erreur envoi de mail creation de demande  ".$exc->getTraceAsString());
            }            
            $this->getHelper('FlashMessenger')->addMessage($this->config->message->user->subscribe->success);
            $this->_redirect('auth/login');
          } else {
              $this->view->errorMessage = $this->config->message->user->subscribe->error;              
          }
        } 
    } 
   
   
   
   
   
   public function forgotpwdAction() {
        # Post parameter
      if($this->getRequest()->isPost()){
          $params = $this->getRequest()->getParams();
          $userTable = new Application_Model_DbTable_User();
          $user = $userTable->fetchRow("email = '".$params['email']."' ");
          if($user) {
            $this->logger->info("Mot de passe oublié, adresse OK ".$params['email']);
            $mailing = new Mailing();
            $mailing->setSubject("Mot de passe oublié")
                    ->setTo($params['email'])
                    ;
            $mailing->infoMail = array(
                                'user' => $user['firstname'].' '.$user['lastname'],
                                'password' => $user['pwd']           
                            ); 
            $mailing->setScriptHtml('forgot_password.phtml');
            try {
                  $mailing->send(); 
                  $this->logger->info('Mot de passe oublié transmis par mail à ==> '.$params['email']);
                  $this->getHelper('FlashMessenger')->addMessage($this->config->message->password->forgotten->success);
                  $this->_redirect('auth/login');
            } catch (Exception $exc) {
                  $this->view->errorMessage = $this->config->message->mailing->undelivrable; 
                  $this->logger->err("Erreur envoi de mail mot de passe oublié à  ".$params['email']);
            }            
            
          } else {
             $this->logger->err("Mot de passe oublié, adresse inconnue ".$params['email']);
             $this->view->errorMessage = $this->config->message->password->forgotten->error; 
          }
      }
   }
    
    
   
   
    
    
    
}

