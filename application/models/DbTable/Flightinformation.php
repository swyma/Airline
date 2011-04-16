<?php

class Application_Model_DbTable_Flightinformation extends Zend_Db_Table_Abstract
{

    protected $_name = 'flightinformation';
	
    public function getAllInfo($sql){
    	$adapter = Zend_Registry::get('db');
      	$flightinformation =  $adapter->query($sql);
        return $flightinformation;
    }

}

