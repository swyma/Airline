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
                $cus_account = $customerNamespace->cus_account;
                $db = new Application_Model_DbTable_Customer();
                $allcustomer = "select * from customer where cus_account='$cus_account' and flag='1'";
                $customerinfo = $db->getCustomerInfo($allcustomer);
                echo $cus_account;
                $this->customer = $customerinfo;
                foreach($this->customer as $key=>$value){
               	    $customer_id=$value['cus_id'];
                	$customer_name=$value['cus_account'];       	
                }
                echo $customer_id;
                echo $customer_name;
            if (strtolower($_SERVER['REQUEST_METHOD']) == 'post') {    	 
                	//取得前台得传过来的值
                    $comment_content = $this->_request->getPost('comment_content');
                    //$user_pwd = $this->_request->getPost('user_pwd');
                    if ($comment_content != null ) {
                    	//实例化
                        $db = new Application_Model_DbTable_Membership();
                        $ip=$_SERVER['REMOTE_ADDR'];//获取远端ip
                        $sqlstr="insert into membership values(null,'$customer_id','$customer_name',
                        '$comment_content',now(),'$ip','1')";
                        $adapter = Zend_Registry::get('db');
            	        $result=$adapter->query($sqlstr);
                        //成功后跳转页面
                        if($result > 0){
                        	echo "发表评论成功！";
                        	echo $this->_helper->redirector('customercommentcontentshow');
                        }
                        
                    } else {
                    	//失败后返回
                        echo $this->_helper->redirector('customercomment');
                    }
                 }
    }

    public function customercommentcontentshowAction()
    {
         // action body
         $this->_helper->layout->disableLayout();
         $db = new Application_Model_DbTable_Membership();
         //查询所有会员评论的信息
         $all_contentinfo="SELECT * FROM membership";
         $contentinfo = $db->getAllContentInfo($all_contentinfo);
         $this->view->membership = $contentinfo;
         //多表联合查询
         $content_info="SELECT m.customer_name,m.comment_content FROM membership m ,customer c where 
         m.customer_name=c.cus_account and m.customer_id=c.cus_id";
         $allcontent = $db->getAllContentInfo($content_info);
         $this->view->customer = $allcontent;
    }


}





