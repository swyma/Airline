<?php

class FlightcompanyController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }

    public function indexAction()
    { // action body
        $db = new Application_Model_DbTable_Flightcompany;
        //查询所有用户的信息
        $all_Fligntcompany="SELECT * FROM flightcompany";
        $Fligntcompany = $db->getAllflightcompany($all_Fligntcompany);
        $this->view->Flightcompany = $Fligntcompany;
        //多表联合查询
        
    	
        // action body
    }


}