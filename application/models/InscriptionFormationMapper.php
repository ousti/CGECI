<?php

class Application_Model_InscriptionFormationMapper {

    protected $_dbTable;
    protected $logger;
    

    public function __construct() {
       
    }

    public function setDbTable($dbTable) {
        if (is_string($dbTable)) {
            $dbTable = new $dbTable();
        }
        if (!$dbTable instanceof Zend_Db_Table_Abstract) {
            throw new Exception('Invalid table data gateway provided');
        }
        $this->_dbTable = $dbTable;
        return $this;
    }

    
    public function getDbTable() {
        if (null === $this->_dbTable) {
            $this->setDbTable('Application_Model_DbTable_InscriptionFormation');
        }
        return $this->_dbTable;
    }
    
    
    
    public function findAll($params = array(), $order = NULL) {
        $table = $this->getDbTable();
        $select = $table->select();
        $select->setIntegrityCheck(false)
                ->from(array('if' => 'inscription_formation'), array('if.*'))
                ->join(array('u' => 'user'), 'if.id_user = u.id', array('u.*'))
                ->group('id_formation')
        ; //print_r($params);
        
        if(is_array($params) and count($params)):
            
            # by Resa
            if(isset($params['rid'])) :
                $select->where("r.id =  ? ",$params['rid']); 
            endif;
             # by Customer
            if(isset($params['cid'])) :
                $select->where("r.customer_id =  ? ",$params['cid']); 
            endif;
           

        endif;
        
        if(is_null($order))
            $select->order('inscris_le DESC');
        else {
            $select->order($order);
        }
        
       //echo ($select->__toString()); 
        
        return $this->getDbTable()->fetchAll($select);
    }
    
    
    
    public function countNbreReservations($cid, $status = NULL) {
        $table = $this->getDbTable();
        $select = $table->select();
        $select->setIntegrityCheck(false)
               ->from(array('r' => 'reservation'), array('count(r.id) as nbResa'))
               ->where("r.customer_id =  ? ",$cid)
               
        ; 
        
        if(!is_null($status))
            $select->where("r.status =  ? ",$status);
        
        return $this->getDbTable()->fetchRow($select);
    } 
    
    
    public function totalReservation($cid) {
        return $this->countNbreReservations($cid);
    }
    
    public function totalReservationOK($cid) {
        return $this->countNbreReservations($cid, 1);
    }
    
    public function totalReservationAnnule($cid) {
        return $this->countNbreReservations($cid, -1);
    }
    
    public function totalReservationEnCours($cid) {
        return $this->countNbreReservations($cid, 0);
    }
    
    
    public function dateDerniereReunion($cid) {
        $table = $this->getDbTable();
        $select = $table->select();
        $select->setIntegrityCheck(false)
               ->from(array('r' => 'reservation'), array('MAX(reservation_date) as last_reservation_date', 'start_time', 'end_time'))
               ->where("r.customer_id =  ? ",$cid)
               ->where("r.status = 1")
               ->group("reservation_date")
        ; //echo ($select->__toString()); 
        return $this->getDbTable()->fetchRow($select);
    }
    
    
    public function getProchaineReunion($cid, $currentDate) {
        $table = $this->getDbTable();
        $select = $table->select();
        $select->setIntegrityCheck(false)
               ->from(array('r' => 'reservation'), array('MIN(reservation_date) as last_reservation_date', 
                                                         'start_time', 'end_time', 'participants_number', 'testing_date'))
               ->join(array('p' => 'meeting_place'), 'r.meeting_place_id = p.id', array('p.place'))  
               ->where("r.customer_id =  ? ",$cid)
               ->where("r.status = 1")
               ->where("reservation_date >= ? ", $currentDate)
               ->group("reservation_date")
        ; //echo ($select->__toString()); 
        return $this->getDbTable()->fetchRow($select);
    }
    
    

    
    


}

