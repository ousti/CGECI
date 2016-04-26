<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

final class Utils {
    
    
    public static function formatDateToFr($date, $hour = FALSE) {
        
        return ( $hour == FALSE ? date('d.m.Y',strtotime($date)) : date('d.m.Y H:i:s',strtotime($date)));
    }
    
    public static function compareDate($date1, $date2) {
        $d1 = strtotime($date1);
        $d2 = strtotime($date2);
        return ($d1 <= $d2);
    }
    
    public static function getInitToken() {
        $defaultNamespace = new Zend_Session_Namespace('Init');
        if (!isset($defaultNamespace->token)) {
            $ws = new WebService();
            $defaultNamespace->token = $ws->getToken();
        }
        return $defaultNamespace->token;
    }
    
    
    
    
}




?>
