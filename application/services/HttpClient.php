<?php



final class HttpClient {
    
    
    private $httpClient;
    private $ws;
    
    
    public function __construct($key,$endPoint) {
        
        try {
            $this->setWs(new Zend_Config_Ini(APPLICATION_PATH."/configs/webservices.ini",$key));
            
        } catch (Zend_Config_Exception $exc) {
            echo '[HttpClient][Construct][Zend_Config_Exception]'.$exc->getTraceAsString();
        }
        
        try {
           $chaine = $this->getWs()->baseURL.$this->getWs()->$endPoint; 
           $this->setHttpClient(new Zend_Http_Client($chaine));
            
        } catch (Zend_Uri_Exception $exc) {
            echo '[HttpClient][Construct][Zend_Uri_Exception]'.$exc->getTraceAsString();
        } 
        
    }
    
    
    public function getHttpClient() {
        return $this->httpClient;
    }

    public function setHttpClient($httpClient) {
        $this->httpClient = $httpClient;
    }

    public function getWs() {
        return $this->ws;
    }

    public function setWs($ws) {
        $this->ws = $ws;
    }




    
   
   
    
    
 
}

