<?php
class BookinformationController extends Zend_Controller_Action
{
    public function init ()
    {
        /* Initialize action controller here */
    }
    public function indexAction ()
    {
        // action body
    }
    public function showAction ()
    {
        // action body
        //显示航班信息
        $db = new Application_Model_DbTable_Flightinformation();
        $flightinfo = "SELECT com_code,air_code,fli_everyday,fli_no,fli_discount,fli_baddress,fli_aaddress,SUBSTRING(fli_atime,12,5) as fli_atime, SUBSTRING(fli_btime,12,5) as fli_btime,fli_Ffare,fli_Yfare,fli_Cfare,fli_refundtime,fli_refund FROM flightinformation where flag=1";
        $flightinformation = $db->getAllInfo($flightinfo);
        $this->view->flightinformation = $flightinformation;
    }
}



