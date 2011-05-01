<?php

class MembershipController extends Zend_Controller_Action
{
	protected $_numPerPage = 10;

    protected $_pageRange = 10;

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
                        //开启会话session
                        Zend_Session::start();
                        $customerNamespace = new Zend_Session_Namespace('customer');
                        //先把会话$login_or_not设置为false
                        $login_or_not = $customerNamespace->login_or_not;
                        if ($login_or_not === TRUE) {
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
                            if (strtolower($_SERVER['REQUEST_METHOD']) ==
                             'post') {
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
                            $customerNamespace->login_or_not = FALSE;
                            echo '<script>alert("您还没有登录，必须要在登录后才能评论");</script>';
                            //echo '<a href="http://localhost/Airline/Customer/customerlogin">请点击我现在登录</a>';
                            //echo $this->_helper->redirector('customerlogin');
    }
    }
    public function customercommentcontentshowAction()
    {
        $this->_helper->layout->disableLayout();
                        //$db = new Application_Model_DbTable_Membership();
                        $db = Zend_Registry::get('db');
                        $sql = "SELECT * FROM membership limit 0,1000";
                        $select = $db->query($sql)->fetchAll();
                        $paginator = Zend_Paginator::factory($select);
                         //Zend_Paginator分页 
                        $numPerPage = $this->_numPerPage;
                        $pageRange = $this->_pageRange;
                        $page = $this->_request->getParam('page', 1);
                        $paginator->setCurrentPageNumber($page)
                            ->setItemCountPerPage($numPerPage)
                            ->setPageRange($pageRange);
                        $this->view->membership = $paginator;
                        //实现评论功能
                        //开启session会话
                        Zend_Session::start();
                        $customerNamespace = new Zend_Session_Namespace('customer');
                        //先把会话$login_or_not设置为false
                        $login_or_not = $customerNamespace->login_or_not;
                        if ($login_or_not === true) {
                            $cus_account = $customerNamespace->cus_account;
                            $db = new Application_Model_DbTable_Customer();
                            $login_customer = "select * from customer where cus_account='$cus_account' and flag='1'";
                            $customerinfo = $db->getCustomerInfo($login_customer);
                            //echo $cus_account;
                            $this->customer = $customerinfo;
                            foreach ($this->customer as $key => $value) {
                                $customer_id = $value['cus_id'];
                                $customer_name = $value['cus_account'];
                            }
                            //echo $customer_id;
                            //echo $customer_name;
                            if (strtolower($_SERVER['REQUEST_METHOD']) ==
                             'post') {
                                //取得前台得传过来的值
                                $comment_content = $this->_request->getPost('comment_content');
                                //$user_pwd = $this->_request->getPost('user_pwd');
                                if ($comment_content != null) {
                                    //实例化
                                    $db = new Application_Model_DbTable_Membership();
                                    $ip = $_SERVER['REMOTE_ADDR']; //获取远端ip
                                    $sqlstr = "insert into membership values(null,'$customer_id','$customer_name',
                                                '$comment_content',now(),'.$ip.','1')";
                                    $adapter = Zend_Registry::get('db');
                                    $result = $adapter->query($sqlstr);
                                    //成功后跳转页面
                                    if ($result > 0) {
                                        echo $this->_helper->redirector('customercommentcontentshow');
                                    }
                                } else {
                                    //失败后返回
                                    echo $this->_helper->redirector('customercommentcontentshow');
                                }
                            }
                        } else {
                            //  验证失败，将会话$login_or_not设置为 false    
                            $customerNamespace->login_or_not = false;
                            echo '<script>alert("您还没有登录，必须要在登录后才能评论");</script>';
    }
    }
    public function customercommentcontentspagingAction()
    {
        // action body
    }

    public function commentreplyAction()
    {
    // action body
        $this->_helper->layout->disableLayout();
            //实现评论回复功能
                //开启session会话
                Zend_Session::start();
                $customerNamespace = new Zend_Session_Namespace('customer');
                //先把会话$login_or_not设置为false
                $login_or_not = $customerNamespace->login_or_not;
                    if (strtolower($_SERVER['REQUEST_METHOD']) =='post') {
                        //取得前台得传过来的值
                        $cus_account= $this->_request->getPost('cus_account');
                        $cus_pwd= $this->_request->getPost('cus_pwd');
                        $comment_content = $this->_request->getPost('comment_content');
                             //验证会员账号和密码是否正确
                             if ($cus_account != null && $cus_pwd != null) {
                                //实例化
                                $db = new Application_Model_DbTable_Customer();
                                //实例一个全局变量
                                $adapter1 = Zend_Registry::get('db');
                                //查询登录会员的信息
                                $sqlstr1 = "select count(*) from customer where  cus_account='$cus_account' 
                                and cus_pwd='$cus_pwd' and flag=1";
                                //echo $sqlstr;
                                $result1 = $adapter1->fetchOne($sqlstr1);
                                //echo $result;
                                   if ($result1 > 0) {
                        	          Zend_Session::start();
                        	          $customerNamespace = new Zend_Session_Namespace('customer');
                        	          //如果输入的会员账号和密码正确，则为Session设值
                        	          //说明已经登录，$login_or_not设为true
                        	          $customerNamespace->login_or_not=true;
                                      $customerNamespace->cus_account = $cus_account; 
                                      //$customerNamespace->cus_pwd = $cus_pwd;
                                      //echo $this->_helper->redirector('customerinfomationshow');
                                      //根据session保存的会员账号取得会员的身份证号码
                                      $db = new Application_Model_DbTable_Customer();
                                      $login_customer = "select * from customer where cus_account='$cus_account' and flag='1'";
                                      $customerinfo = $db->getCustomerInfo($login_customer);
                                      //echo $cus_account;
                                      $this->customer = $customerinfo;
                                         foreach ($this->customer as $key => $value) {
                                             $customer_id = $value['cus_id'];
                                             $customer_name = $value['cus_account'];
                                             }
                                   } else {
                                     //失败后返回
                                     //Zend_Session::start();
                                     //$customerNamespace = new Zend_Session_Namespace('customer');
                                     //如果输入的会员账号和密码不正确，这样就不能登录，$login_or_not设为false
                                     $customerNamespace->login_or_not=false;
                                     echo '<script>alert("会员账号或者密码错误，请重新登录后再回复评论");</script>';
                        }
                    }
               if ($login_or_not === true) {
                    //$cus_account = $customerNamespace->cus_account;
                    //echo $customer_id;
                    //echo $customer_name;
                        if ($cus_account != null && $cus_pwd != null && $comment_content != null) {
                            //实例化
                            $db = new Application_Model_DbTable_Membership();
                            $ip = $_SERVER['REMOTE_ADDR']; //获取远端ip
                            $sqlstr2 = "insert into membership values(null,'$customer_id','$customer_name',
                                        '$comment_content',now(),'$ip','1')";
                            $adapter2 = Zend_Registry::get('db');
                            $result2 = $adapter2->query($sqlstr2);
                            //成功后跳转页面
                            if ($result2 > 0) {
                                echo $this->_helper->redirector('customercommentcontentshow');
                            }
                        } else {
                            //失败后返回
                            echo $this->_helper->redirector('customercommentcontentshow');
                        }
                    } else {
                    //  验证失败，将会话$login_or_not设置为 false    
                    $customerNamespace->login_or_not = false;
                    echo '<script>alert("您还没有登录，必须要在登录后才能评论");</script>';
                    //echo $this->_helper->redirector('customercommentcontentshow');


                } 
            }
      }
    }










