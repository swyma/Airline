<?php

class Application_Model_DbTable_Flightinformation extends Zend_Db_Table_Abstract
{

    protected $_name = 'flightinformation';
	
    public function getAllInfo($sql){
    	$adapter = Zend_Registry::get('db');
      	$flightinformation =  $adapter->query($sql);
        return $flightinformation;
    }

 //取得取消的航班信息并通告
    public function getfliCancleInfo($sql){
    	$adapter=Zend_Registry::get('db');
    	$fliCancleInfo=$adapter->query($sql);
    	return $fliCancleInfo;
    }
   
    //取得航班取消的星期 及航班有优惠的星期
    public function getWeek($sql){
    	$adapter=Zend_Registry::get('db');
    	$fliCancleWeek=$adapter->query($sql);
    	return $fliCancleWeek;
    }
    
    //取得航班优惠信息
    public function getfliPreference($sql){
    	$adapter=Zend_Registry::get('db');
    	$fliPreferenceInfo=$adapter->query($sql);
    	return $fliPreferenceInfo;
    }

}

