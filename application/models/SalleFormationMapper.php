<?php

class Application_Model_SalleFormationMapper {

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
            $this->setDbTable('Application_Model_DbTable_SalleFormation');
        }
        return $this->_dbTable;
    }
    
    
    
    public function findAll($params = array(), $order = NULL, $all = FALSE) {
        $table = $this->getDbTable();
        $select = $table->select();
        $select->setIntegrityCheck(false)
                ->from(array('r' => 'reservation_salle'), array('r.*'))
                ->joinLeft(array('u' => 'user'), 'r.id_user = u.id', array('u.*'));
                
        ; //print_r($params);
        
        if(!$all) {
           
        }
        
        if(is_null($order))
            $select->order('created_at DESC');
        else {
            $select->order($order);
        }
        
        // echo ($select->__toString()); 
        
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

