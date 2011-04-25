<?php

class Application_Model_DbTable_Hotelinformation extends Zend_Db_Table_Abstract
{

    protected $_name = 'hotelinformation';
    //取得所有用户的信息
    public function getAllHotelinformationInfo ($sql)
    {
    	//实例一个全局变量
    	$adapter = Zend_Registry::get('db');
        //执行sql语句
      	$hotelinformation =  $adapter->query($sql);
        //返回所有信息
        return $hotelinformation;
    }
    //用户的信息增删改
    //这种方法你只要改变$sql就可以
    //其实个人觉得这种方法不安全，因为会涉及数据注入失败情况发生
    public function HanlderHotelinformation($sql){
    	$adapter = Zend_Registry::get('db');
    	$adapter->query($sql);
    }

}

