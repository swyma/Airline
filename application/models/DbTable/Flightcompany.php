<?php

class Application_Model_DbTable_Flightcompany extends Zend_Db_Table_Abstract
{

    protected $_name = 'flightcompany';
 public function getAllflightcompany ($sql)
    {
    	//实例一个全局变量
    	$adapter = Zend_Registry::get('db');
        //执行sql语句
    	$fligntcompany =  $adapter->query($sql);
        //返回所有信息
        return $fligntcompany;
    }
}