<?php

class MembershipController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }

    public function indexAction()
    {
        // action body
    }

    public function customercommentAction()
    {
            // action body
        $this->_helper->layout->disableLayout();
        Zend_Session::start();
        $customerNamespace = new Zend_Session_Namespace('customer');
        //先把会话$login_or_not设置为false
        $login_or_not=$customerNamespace->login_or_not;
        if($login_or_not===TRUE){
        $cus_account = $customerNamespace->cus_account;
        $db = new Application_Model_DbTable_Customer();
        $allcustomer = "select * from customer where cus_account='$cus_account' and flag='1'";
        $customerinfo = $db->getCustomerInfo($allcustomer);
//echo $cus_account;
        $this->customer = $customerinfo;
        foreach ($this->customer as $key => $value) {
            $customer_id = $value['cus_id'];
            $customer_name = $value['cus_account'];
        }
//echo $customer_id;
//echo $customer_name;
        if (strtolower($_SERVER['REQUEST_METHOD']) == 'post') {
            //取得前台得传过来的值
            $comment_content = $this->_request->getPost(
            'comment_content');
            //$user_pwd = $this->_request->getPost('user_pwd');
            if ($comment_content != null) {
                //实例化
                $db = new Application_Model_DbTable_Membership();
                $ip = $_SERVER['REMOTE_ADDR']; //获取远端ip
                $sqlstr = "insert into membership values(null,'$customer_id','$customer_name',
                                '$comment_content',now(),'$ip','1')";
                $adapter = Zend_Registry::get('db');
                $result = $adapter->query($sqlstr);
                //成功后跳转页面
                if ($result > 0) {
                    //$//success= '<script>alert("发表评论成功");</script>';
                    echo $this->_helper->redirector('customercommentcontentshow');
                   // $this->view->success=$success;
                }
            } else {
                //失败后返回
                echo $this->_helper->redirector('customercomment');
            }
        } 
        } else {       	
		//  验证失败，将会话$login_or_not设置为 false    
		$customerNamespace->login_or_not=FALSE;   
		echo '<script>alert("您还没有登录，必须要在登录后才能评论");</script>';
		echo '<a href="http://localhost/Airline/Customer/customerlogin">请点击我现在登录</a>';
        //echo $this->_helper->redirector('customerlogin');
        }
  }
    public function customercommentcontentshowAction()
    {
         // action body
        $this->_helper->layout->disableLayout();
        //$db = new Application_Model_DbTable_Membership();
        $db = Zend_Registry::get('db');
        $sql = "SELECT * FROM membership limit 0,1000";
        $select = $db->query($sql)->fetchAll();
        $paginator = Zend_Paginator::factory($select);
        /* Zend_Paginator分页 */
        $numPerPage = $this->_numPerPage;
        $pageRange = $this->_pageRange;
        $page = $this->_request->getParam('page', 1);
        $paginator->setCurrentPageNumber($page)
            ->setItemCountPerPage($numPerPage)
            ->setPageRange($pageRange);
        $this->view->membership = $paginator;
    }

    public function customercommentcontentspagingAction()
    {
        // action body
    }


}







