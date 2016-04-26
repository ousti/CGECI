<?php


require_once 'BaseController.php';

class AuthController extends Zend_Controller_Action {

    
    
   public function loginAction()
    {
        $request = $this->getRequest();
        if($request->isPost()){
                $params = $this->getRequest()->getParams();
                if($params['login'] <> '' and $params['pwd'] <> '') {
                    $userTable = new Application_Model_DbTable_User();
                    $auth = Zend_Auth::getInstance();            
                    $authAdapter = new Zend_Auth_Adapter_DbTable($userTable->getAdapter(),'user','login','pwd',' ? ');
                    $authAdapter->setIdentityColumn('login');
                    $authAdapter->setIdentity($params['login'])
                                ->setCredential($params['pwd']);
                    $select = $authAdapter->getDbSelect();
                    //$select->where('status = 1');
                    $result = $auth->authenticate($authAdapter);           
                    if($result->isValid()) {
                        $resultObject = $authAdapter->getResultRowObject();
                        if(!$resultObject->status){ 
                            $auth->clearIdentity();
                            $this->view->errorMessage = "Votre compte est en cours de validation par notre équipe! Merci de bien vouloir patienter";
                        } else {
                            // Get Token
                            $ws = new WebService();
                            $token = $ws->getToken();
                            $storage = new Zend_Auth_Storage_Session();
                            $storage->write(array('sid'=>$resultObject->id, 
                                                  'user'=>$resultObject->firstname.' '.$resultObject->lastname,
                                                  'company'=>$resultObject->company_name,
                                                  'id_company_eudonet'=>$resultObject->id_company_eudonet,
                                                  'token' => $token
                                    ));
                            # Setting expire or Zend Auth
                            Zend_Session::regenerateId(); 
                            $sessionAuth = new Zend_Session_Namespace("Zend_Auth");
                            $this->_redirect('myaccount/index');
                       }
                
                    } else {
                       $this->view->errorMessage = "Paramètres incorrect! Veuillez saisir des accès corrects";  
                    }
                } else {
                   $this->view->errorMessage = "Veuillez saisir votre login et votre mot de passe!";   
                }
         } 
         if($fm = $this->getHelper('FlashMessenger')->getMessages()) :
            $this->view->fm = $fm[0];
         endif;
         $this->_helper->layout()->setLayout('auth_layout');
        
    } 
    
    
    
    public function logoutAction()
    {
        Zend_Auth::getInstance()->clearIdentity();
        Zend_Session::destroy();
        $this->_redirect('auth/login');
    }
    
    
    public function authenticationAction()
    {
       try {
           $ws = new WebService();
           $auth = $ws->authentication();
          Zend_Debug::dump($auth);
       } catch (Zend_Config_Exception $exc) {
           echo "message derreur : ".$exc->getMessage();
    }

        
        /*
        $users = new Application_Model_DbTable_Utilisateur();
        $form = new Application_Form_LoginForm();
        $this->view->form = $form;
        
        if($this->getRequest()->isPost()){
            if($form->isValid($_POST)){
                $wsdl_uri = 'http://ws.ldap.ocit.ci/';
                $url = 'http://10.242.70.229:28080/ci.orange.ldap/ldapWSService?wsdl';
                $client = new Zend_Soap_Client($url);
                $client->setUri($wsdl_uri);
                $client->setSoapVersion(1);
                $data = $form->getValues();
                $resultat = $client->authenticate(array('username'=>$data['login'],'password'=>$data['pwd']));
               
                if($resultat->return->check){
                    $auth = Zend_Auth::getInstance();
                    $authAdapter = new Zend_Auth_Adapter_DbTable($users->getAdapter(),'utilisateur','login','role','? or 1 = 1');
                    $authAdapter->setIdentityColumn('login');
                               // ->setCredentialColumn('any');
                    $authAdapter->setIdentity($data['login'])
                                ->setCredential('any');
                    // Récupérer l'objet select (par référence)
                    $select = $authAdapter->getDbSelect();
                    $select->where('statut = 1');
                    $result = $auth->authenticate($authAdapter);
                    if($result->isValid()) {
                    $storage = new Zend_Auth_Storage_Session();
                    $storage->write($authAdapter->getResultRowObject());
                    $this->_redirect('index/index');
                    } else {
                       $this->view->errorMessage = "Vous n'avez pas le droit d'accéder à l'application! Contactez l'adminustrateur";  
                    }
                } else {
                    $this->view->errorMessage = "Login/Mot de passe incorrect! Veuillez réessayer!";
                }         
            }
        }
        
        $this->_helper->layout()->setLayout('auth_layout');
        //$this->_helper->layout()->disableLayout(); 
        //$this->_helper->viewRenderer->setNoRender(true);
         * 
         */
        $this->_helper->layout()->disableLayout(); 
        //$this->_helper->viewRenderer->setNoRender(true);
    } 
    
    
}

