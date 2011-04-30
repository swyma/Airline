<?php

class FlightcompanyController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }

    public function indexAction()
    {
        
    }
    public function flightcomanyshowAction()
    {
     $this->_helper->layout->disableLayout();
                // action body
             if (strtolower($_SERVER['REQUEST_METHOD']) == 'post') {
                	//取得前台得传过来的值
                    $com_name = $this->_request->getPost('com_name');
                    
                    if ($com_name != null ) {
                		// action body
                		$db = new Application_Model_DbTable_Flightcompany();
                		//查询所有用户的信息
                		$all_flightcompany="SELECT com_code,com_name,com_address,com_information FROM flightcompany where com_name='".$com_name."' ";
                		$flightcompany = $db->getAllflightcompany($all_flightcompany);
                		$this->view->flightcompanyshow= $flightcompany;
                	//	$this->_helper->redirector('search');
                    }else{
                    	echo "条件未满足！";
                    }
    }

   

	}
    }



