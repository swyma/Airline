<?php
class BookinformationController extends Zend_Controller_Action
{
	protected $_numPerPage = 10;
	protected $_fare=0;
    protected $_pageRange = 10;
    public function init ()
    {
        /* Initialize action controller here */
    }
    public function indexAction ()
    {
        // action body
                $parm = $this->_request->getParam('parm');
                if (strtolower($_SERVER['REQUEST_METHOD']) == 'post') {
                    //取得前台得传过来的值
                    $text = '%' . $this->_request->getPost('txt') . '%';
                    $db = Zend_Registry::get('db');
                    //搜索
                    $sql = $db->quoteInto(
                    "SELECT fli_autoid,com_code,air_code,fli_everyday,fli_no," .
                     "fli_discount,fli_baddress,fli_aaddress,substring(fli_btime,12,5) as fli_btime," .
                     "substring(fli_atime,12,5) as fli_atime,fli_Ffare,fli_Cfare,fli_Yfare FROM flightinformation " .
                     "WHERE com_code like ? or fli_no like ? " .
                     "or fli_baddress like ? or fli_aaddress like ? ", $text);
                    $select = $db->query($sql)->fetchAll();
                    $paginator = Zend_Paginator::factory($select);
                    /* Zend_Paginator分页 */
                    $numPerPage = $this->_numPerPage;
                    $pageRange = $this->_pageRange;
                    $page = $this->_request->getParam('page', 1);
                    $paginator->setCurrentPageNumber($page)
                        ->setItemCountPerPage($numPerPage)
                        ->setPageRange($pageRange);
                    $this->view->flightinformation = $paginator;
                    $this->view->i = 0;
                    $params = array('parm' => $this->_request->getPost('txt'));
                    $this->_helper->redirector('index', 'Bookinformation', null, 
                    $params);
                } else 
                    if ($this->_request->getParam('parm') != "") {
                        $text = '%' . $this->_request->getParam('parm') . '%';
                        $db = Zend_Registry::get('db');
                        //搜索
                        $sql = $db->quoteInto(
                        "SELECT fli_autoid,com_code,air_code,fli_everyday,fli_no," .
                         "fli_discount,fli_baddress,fli_aaddress,substring(fli_btime,12,5) as fli_btime," .
                         "substring(fli_atime,12,5) as fli_atime,fli_Ffare,fli_Cfare,fli_Yfare FROM flightinformation " .
                         "WHERE com_code like ? or fli_no like ? " .
                         "or fli_baddress like ? or fli_aaddress like ? ", $text);
                        $select = $db->query($sql)->fetchAll();
                        $paginator = Zend_Paginator::factory($select);
                        /* Zend_Paginator分页 */
                        $numPerPage = $this->_numPerPage;
                        $pageRange = $this->_pageRange;
                        $page = $this->_request->getParam('page', 1);
                        $paginator->setCurrentPageNumber($page)
                            ->setItemCountPerPage($numPerPage)
                            ->setPageRange($pageRange);
                        $this->view->flightinformation = $paginator;
                        $this->view->i = 0;
                         //成功后跳转页面
                    } else {
                        $db = new Application_Model_DbTable_Flightinformation();
                        $sql = "SELECT fli_autoid,com_code,air_code,fli_everyday,fli_no," .
                         "fli_discount,fli_baddress,fli_aaddress,substring(fli_btime,12,5) as fli_btime," .
                         "substring(fli_atime,12,5) as fli_atime,fli_Ffare,fli_Cfare,fli_Yfare FROM flightinformation";
                        /* Zend_Paginator分页 */
                        $numPerPage = $this->_numPerPage;
                        $pageRange = $this->_pageRange;
                        $page = $this->_request->getParam('page', 1);
                        $offset = $numPerPage * $page;
                        $select = $db->getAllInfo($sql)->fetchAll();
                        $paginator = Zend_Paginator::factory($select);
                        //分页取数据
                        $paginator->setCurrentPageNumber($page)
                            ->setItemCountPerPage($numPerPage)
                            ->setPageRange($pageRange);
                        $this->view->flightinformation = $paginator;
                        $this->view->i = 0;
                    }
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


