<?php



final class WebService {
    
    private $config;
    private $logger;
    private $token;
    
    public function __construct() {
        $this->config = Zend_Registry::get('config');
        $this->logger = Zend_Registry::get('logger');
    }

    
    public function getToken() {
        $auth = $this->authentication(); 
        if($auth == NULL) {
            return 'XoinL4w11WZ97sAwxVNu07BOPGGpKHEeIJkrzoF0Vyq40GFjJ8HGTu9U/WdSM0j+EIduL01OoNnNdKfRUgCIpx+7eLI278Fr';
        }
        if($auth -> AuthenticateResult -> Success)
            return $auth -> AuthenticateResult -> Token;
    }
    
    /*
     * Authentication :: return token
     */
    public function authentication() {
        $urlWS = $this->config->eudonet->ws.''.$this->config->eudonet->uri->authentication;
        $params = array(
                        'subscriberLogin' => $this->config->subscriber->login,
                        'subscriberPassword' => $this->config->subscriber->password,
                        'userLogin' => $this->config->user->login,
                        'userPassword' => $this->config->user->password,
                        'BaseName' => $this->config->app->db
                        ); 
        $this->logger->info("[Auth][Request][subscriberLogin=".$this->config->subscriber->login."]"
                            . "[subscriberPassword=".$this->config->subscriber->password."]"
                            . "[userLogin=".$this->config->user->login."]"
                            . "[userPassword=".$this->config->user->password."]"
                            . "[BaseName=".$this->config->app->db."]");
        
        try {
        $soapClient = new SoapClient($urlWS, array('trace' => 1, 'soap_version' => SOAP_1_2, 'encoding' => 'UTF-8'));
        $auth = $soapClient ->__soapCall('Authenticate', array('parameters' => $params));
        } catch (Exception $e) {
            return NULL;
        }
        return $auth;
    }
    
    /*
     * ------------ COMPANY 
     */
    public function getCompanyInfo($companyId, $token) {
        $urlWS = $this->config->eudonet->ws.''.$this->config->eudonet->uri->societe;
        $paramsSoapCall = array('sFieldNameId' => 'PMID', 'sOperator' => 0, 'sFileId' => $companyId, 'pageNumber' => 1, 'token' => $token); 
        $this->logger->info("[CompanyInfo][Request=".$urlWS."][Company=".$companyId."] [token=".$token."]"); 
        try {
            $soapClient = new SoapClient($urlWS, array('trace' => 1, 'soap_version' => SOAP_1_2, 'encoding' => 'UTF-8'));
            $result = $soapClient ->__soapCall('SearchById', array('parameters' => $paramsSoapCall));
        } catch(Exception $e) {
            $this->logger->err("Exception => ".$e->getMessage());
            throw new Exception("Impossible d'accéder à la plateForme EUDONET");
        }
        return $result;
    }
    
    public function updateCompanyInfo($companyId, $token, $params) {
        $urlWS = $this->config->eudonet->ws.''.$this->config->eudonet->uri->societe;
        $paramsSoapCall = array('PM' => array('PMID' => $companyId, 
                                              'PM01' => $params['PM01'], 'PM02' => $params['PM02'], 
                                              'PM03' => $params['PM03'], 'PM04' => $params['PM04'], 
                                              'PM05' => $params['PM05'], 'PM06' => $params['PM06'], 
                                              'PM10' => $params['PM10']
                                            ),
                                             'token' => $token
                                ); 
        $this->logger->info("[CompanyUpdate][Request=".$urlWS."][Company=".$companyId."] [token=".$token."]"); 
        try {
            $soapClient = new SoapClient($urlWS, array('trace' => 1, 'soap_version' => SOAP_1_2, 'encoding' => 'UTF-8'));
            $result = $soapClient ->__soapCall('Update', array('parameters' => $paramsSoapCall));
        } catch(Exception $e) {
            $this->logger->err("Exception => ".$e->getMessage());
            throw new Exception("Impossible d'accéder à la plateForme EUDONET");
        }
        return $result;
    }
    
    
    /* -------------------------------------- */
    
    /* ------------ FORMATIONS  * ------------------- */
    
    public function getTrainingList($token) {
        $urlWS = $this->config->eudonet->ws.''.$this->config->eudonet->uri->query;
        $paramsSoapCall = array('lpParam' => array('Debug' => false, 'Token' => $token, 'MainTable' => 100, 
                                                    'ListCol' => '101;135;106;109;139;123;104', 'LineByPage' => 100, 
                                                    'Page' => 0, 'FilterId' => 0)); 
        $this->logger->info("[TrainingsList][Request=".$urlWS."][token=".$token."]"); 
        $soapClient = new SoapClient($urlWS, array('trace' => 1, 'soap_version' => SOAP_1_2, 'encoding' => 'UTF-8'));
        try {
        $result = $soapClient ->__soapCall('GetList', array('parameters' => $paramsSoapCall));
        } catch(Exception $e) { 
            //Zend_Debug::dump($soapClient);
        }
        return $result;
        
    
    }
    
    public function getTrainingDetail($trainingId, $token) {
        $urlWS = $this->config->eudonet->ws.''.$this->config->eudonet->uri->training;
        $paramsSoapCall = array('sFieldNameId' => 'EVTID', 'sOperator' => 0, 'sFileId' => $trainingId, 'pageNumber' => 1, 'token' => $token); 
        $this->logger->info("[TrainingDetail][Request=".$urlWS."][Training=".$trainingId."] [token=".$token."]"); 
        try {
            $soapClient = new SoapClient($urlWS, array('trace' => 1, 'soap_version' => SOAP_1_2, 'encoding' => 'UTF-8'));
            $result = $soapClient ->__soapCall('SearchById', array('SearchById' => $paramsSoapCall));
        } catch(Exception $e) {
            $this->logger->err("Exception => ".$e->getMessage());
            throw new Exception("Impossible d'accéder à la plateForme EUDONET");
        }
        return $result;
    }
    

    

    
    /* ------------ EVENTS  * ------------------- */   
    public function getEventList($token) {
        $urlWS = $this->config->eudonet->ws.''.$this->config->eudonet->uri->query;
        $paramsSoapCall = array('lpParam' => array('Debug' => false, 'Token' => $token, 'MainTable' => 2300, 
                                                    'ListCol' => '2301;2302;2306;2309;2307;2308', 'LineByPage' => 100, 
                                                   'Page' => 0, 'FilterId' => 0)); 
        $this->logger->info("[EventsList][Request=".$urlWS."][token=".$token."]"); 
        $soapClient = new SoapClient($urlWS, array('trace' => 1, 'soap_version' => SOAP_1_2, 'encoding' => 'UTF-8'));
        try {
        $result = $soapClient ->__soapCall('GetList', array('parameters' => $paramsSoapCall));
        } catch(Exception $e) { 
            //Zend_Debug::dump($soapClient);
        }
        return $result;
    }
    
    public function getEventDetail($eventId, $token) {
        $urlWS = $this->config->eudonet->ws.''.$this->config->eudonet->uri->event;
        $paramsSoapCall = array('sFieldNameId' => 'EVTID', 'sOperator' => 0, 'sFileId' => $eventId, 'pageNumber' => 1, 'token' => $token); 
        $this->logger->info("[EventDetail][Request=".$urlWS."][EventId=".$eventId."] [token=".$token."]"); 
        try {
            $soapClient = new SoapClient($urlWS, array('trace' => 1, 'soap_version' => SOAP_1_2, 'encoding' => 'UTF-8'));
            $result = $soapClient ->__soapCall('SearchById', array('SearchById' => $paramsSoapCall));
        } catch(Exception $e) {
            $this->logger->err("Exception => ".$e->getMessage());
            throw new Exception("Impossible d'accéder à la plateForme EUDONET");
        }
        return $result;
    }
    

    
    
    
    
    

    public function getCompaniesList($token) {
        $urlWS = $this->config->eudonet->ws.''.$this->config->eudonet->uri->societe;
        $table = 'PM';
        $paramsSoapCall = array('nFilterId' => 1229, 'pageNumber' => 1, 'token' => $token); 
        $this->logger->info("[CompaniesList][Request=".$urlWS."][table=".$table."] [token=".$token."]"); 
        $soapClient = new SoapClient($urlWS, array('trace' => 1, 'soap_version' => SOAP_1_2, 'encoding' => 'UTF-8'));
        $result = $soapClient ->__soapCall('SearchByFilterId', array('parameters' => $paramsSoapCall));
        return $result;
    }
    
    
    
    public function getPaysList($token) {
        $urlWS = $this->config->eudonet->ws.''.$this->config->eudonet->uri->catalog;
        $table = 'PM.PM24';
        $paramsSoapCall = array('sFieldName' => $table, 'token' => $token); 
        $this->logger->info("[CompaniesList][Request=".$urlWS."][table=".$table."] [token=".$token."]"); 
        $soapClient = new SoapClient($urlWS, array('trace' => 1, 'soap_version' => SOAP_1_2, 'encoding' => 'UTF-8'));
        $result = $soapClient ->__soapCall('Get', array('parameters' => $paramsSoapCall));
        return $result;
    }
    
    
    
    
    
   
 
}

