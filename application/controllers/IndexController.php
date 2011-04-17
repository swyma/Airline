<?php
//require_once 'Zend/Db/Table.php';
//require_once 'Zend/Db.php';
class IndexController extends Zend_Controller_Action
{
    public function init ()
    {
        /* Initialize action controller here */
    }
    public function indexAction ()
    {
        // action body
        //$db = new Application_Model_DbTable_Manager();
        //$userinfo = $db->fetchAll();
        
        //$db=new AppendIterator($iterator)
        //$sql = $db->quoteInto('Select boo_fare from bookinformation where boo_autoid= ?', 
       // '1');
        //$result = $db->query($sql);
        // 使用PDOStatement对象$result将所有结果数据放到一个数组中
       // $rows = $result->fetchAll();
        $adapter = Zend_Registry::get('db');
       	$userinfo =  $adapter->query("SELECT * FROM flightcompany a,flightinformation b where a.com_code=b.com_code");
        foreach ($userinfo as $value) {
            echo $value['com_name'];
            echo $value['com_code'];
        }
    }
}

