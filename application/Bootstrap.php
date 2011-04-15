<?php
class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
    protected function _initDBConnection ()
    {
        $params = array('host' => 'localhost', 'username' => 'root', 
        'password' => '123', 'dbname' => 'db_airline','charset'=>'utf8');
        $db = Zend_Db::factory('PDO_MYSQL', $params);
        Zend_Db_Table::setDefaultAdapter($db);
        Zend_Registry::set('db', $db);
    }
    protected function _initViewHelpers ()
    {
        $this->bootstrap('layout');
        $layout = $this->getResource('layout');
        $view = $layout->getView();
        $view->doctype('XHTML1_STRICT');
        $view->headMeta()->appendHttpEquiv('Content-Type', 
        'text/html;charset=utf-8');
        $view->headTitle()->setSeparator('-');
        $view->headTitle('航程网');
    }
}

