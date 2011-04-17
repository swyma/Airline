<?php
class CustomerController extends Zend_Controller_Action
{
    public function init ()
    {
        /* Initialize action controller here */
    }
    public function indexAction ()
    {
        // action body
    }
    public function registerAction ()
    {
        $this->_helper->layout->disableLayout();
        // action body
        if (strtolower($_SERVER['REQUEST_METHOD']) == 'post') {
            $cus_account = $this->_request->getPost('cus_account');
            $cus_pwd = $this->_request->getPost('cus_pwd');
            $cus_pwd2 = $this->_request->getPost('cus_pwd2');
            $cus_sex = $this->_request->getPost('cus_sex');
            $cus_id = $this->_request->getPost('cus_id');
            $cus_telnumber = $this->_request->getPost('cus_telnumber');
            $cus_email = $this->_request->getPost('cus_email');
            $cus_time = $this->_request->getPost('cus_time');
            $db = new Application_Model_DbTable_Customer();
            //将前台传过来的值进行数组化,为inser服务
            $data = array('cus_account' => $cus_account, 
            'cus_pwd' => $cus_pwd, 'cus_id' => $cus_id, 'cus_sex' => $cus_sex, 
            'cus_telnumber' => $cus_telnumber, 'cus_email' => $cus_email, 
            'cus_integral' => 0, 'flag' => true);
            //该方法的api是这样的insert($array)
            $db->insert($data);
             //成功后跳转页面
        }
    }
    public function bookticketAction ()
    {
        // action body
    }
}





