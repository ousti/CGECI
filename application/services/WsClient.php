<?php



final class WSClient {
    
    
    private static $wsClient;
    private $ws;
    private $config;
    
    public function __construct() {
        
    }
    
    
    public function authentication($config, $params) {
        $paramSoap = array('trace' => 1,
                            'soap_version' => SOAP_1_2,
                            'encoding' => 'UTF-8');
        $paramSoapCall = array('parameters' => $params);
        $soapClient = new SoapClient('https://ww5.eudonet.com/V7/app/specif/EUDO_06046/ws/Authenticator.asmx?WSDL',$paramSoap);
        $soapCall = $soapClient -> __soapCall('Authenticate',$paramSoapCall);
        return $soapCall;
    }
    
    
    public static function wsAuthentication($config) {
        $ws = $config->eudonet->ws;
        $wsdl = $ws.''.$config->eudonet->uri->authentication;
        self::$wsClient = new Zend_Soap_Client($wsdl);
        self::$wsClient->setUri($config->eudonet->uri->url);
        
        return self::$wsClient;
    }
   
   
    
    
 
}

