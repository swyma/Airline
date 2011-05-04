<?php

class Application_Model_DbTable_Customer extends Zend_Db_Table_Abstract
{

    protected $_name = 'customer';
         //取得已登录会员的信息
     public function getCustomerInfo ($sql)
    {
    	//实例一个全局变量
    	$adapter = Zend_Registry::get('db');
        //执行sql语句
      	$customerinfo =  $adapter->query($sql);
        //返回所有信息
        return $customerinfo;
    }
    //用户的信息增删改
    //这种方法你只要改变$sql就可以
    //其实个人觉得这种方法不安全，因为会涉及数据注入失败情况发生

	function json_decode(){
		
		echo Zend_Json::encode($this->content);
	}
}

