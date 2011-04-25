<?php

class HotelinformationController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }

    public function indexAction()
    {
        // action body
    }
    
     public function hotelinfoAction()
    {
        // action body
        $this->_helper->layout->disableLayout();
        if (strtolower($_SERVER['REQUEST_METHOD']) == 'post') {
        	$hotelinfo = $this->_request->getPost('hotelinfo');
        	if($hotelinfo!=null){
        		$db = new Application_Model_DbTable_Hotelinformation();
        		$all_hotelinformation="SELECT hotel_name,hotel_level,hotel_tel,hotel_city,hotel_address,hotel_page,hotel_picture FROM hotelinformation where hotel_name='".$hotelinfo."'";
        		$hotelinformation = $db->getAllHotelinformationInfo($all_hotelinformation);
                $this->view->hotelinformation = $hotelinformation;
                $this->_helper->redirector('hotelinfo');
        	}
        }
    }
    

    public function searchAction()
    {
        $this->_helper->layout->disableLayout();
                // action body
             if (strtolower($_SERVER['REQUEST_METHOD']) == 'post') {
                	//取得前台得传过来的值
                    $hotel_name = $this->_request->getPost('hotel_name');
                    $hotel_level = $this->_request->getPost('hotel_level');
                    $hotel_city = $this->_request->getPost('hotel_city');
                    if ($hotel_name != null || $hotel_level != null|| $hotel_city != null) {
                		// action body
                		$db = new Application_Model_DbTable_Hotelinformation();
                		//查询所有用户的信息
                		$all_hotelinformation="SELECT hotel_name,hotel_level,hotel_tel,hotel_city,hotel_address,hotel_page,hotel_picture FROM hotelinformation where hotel_name='".$hotel_name."' or hotel_level='".$hotel_level."' or hotel_city='".$hotel_city."'";
                			$hotelinformation = $db->getAllHotelinformationInfo($all_hotelinformation);
                			$this->view->hotelinformation = $hotelinformation;
                	//	$this->_helper->redirector('search');
                    }else{
                    	echo "条件未满足！";
                    }
    }

   

	}


}
