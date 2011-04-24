<?php
//require_once 'Zend/Db/Table.php';
//require_once 'Zend/Db.php';
class IndexController extends Zend_Controller_Action
{
    protected $_numPerPage = 3; /* 每页5条 */
    protected $_pageRange = 10; /* 页数范围为10 */
    public function init ()
    {
        /* Initialize action controller here */
    }
    public function indexAction ()
    {
        // action body
        $db = new Application_Model_DbTable_Flightinformation();
        /*获取最近30天内的数目
         * select day(boo_time) as day,count(boo_autoid)as count,boo_time from bookinformation 
where flag_pass=0 and date_sub(now(), interval 30 day)<=date(boo_time)
group by DATE_FORMAT(boo_time,'%m %d')
        */
        $sql = "select DATE_FORMAT(boo_time,'%m-%d') as day,count(boo_autoid)as count from bookinformation " .
         "where flag_pass=0 and date_sub(now(), interval 30 day)<=date(boo_time) " .
         "group by DATE_FORMAT(boo_time,'%m %d')";
        $result = $db->getAllInfo($sql)->fetchAll();
        $this->view->result=$result;
    }
    public function announceShowAction ()
    {
        // action body
        /*
                 * 以下是显示通告信息
                 */
        $this->_helper->layout->disableLayout(); //去掉母版页
        $db = new Application_Model_DbTable_Flightinformation();
        //取得航班取消的星期数
        $fWeek = "select fli_everyday  from flightinformation where fli_refundtime='取消' and flag=1";
        $fW = $db->getWeek($fWeek);
        // 取得航班取消的总数
        //实例一个全局变量
        $adapter = Zend_Registry::get('db');
        $fliCancleNum_sql = "select count(fli_everyday) from flightinformation where fli_refundtime='取消' and flag=1";
        $fliCancleNum = $adapter->fetchOne($fliCancleNum_sql);
        //取得系统星期
        $sys_week = date('w');
        switch ($sys_week) {
            case 0:
                $str = "星期天";
                break;
            case 1:
                $str = "星期一";
                break;
            case 2:
                $str = "星期二";
                break;
            case 3:
                $str = "星期三";
                break;
            case 4:
                $str = "星期四";
                break;
            case 5:
                $str = "星期五";
                break;
            case 6:
                $str = "星期六";
                break;
        }
        foreach ($fW as $key => $value) {
            $week = $value['fli_everyday'];
            //取得取消航班的信息
            $fcI = " select fli_no ,fli_baddress ,fli_aaddress from flightinformation " .
             " where fli_refundtime='取消' and flag=1 and fli_everyday='" . $week .
             "'";
            $fliCancleInfo = $db->getfliCancleInfo($fcI);
            for ($i = 0; $i < $fliCancleNum; $i ++) {
                if (strcmp($str, $week)) {
                    $this->view->fliCancleInfo = $fliCancleInfo;
                }
            }
        }
    }
    public function dirgramshowAction ()
    {
        // action body
       
    }
    public function fliPreferenceAction ()
    {
        // action body
        /*
                 * 以下是显示当天的航班优惠信息
                 */
        $this->_helper->layout->disableLayout();
        $db = new Application_Model_DbTable_Flightinformation();
        //取得系统星期
        $week = date('w');
        switch ($week) {
            case 0:
                $str = "星期天";
                break;
            case 1:
                $str = "星期一";
                break;
            case 2:
                $str = "星期二";
                break;
            case 3:
                $str = "星期三";
                break;
            case 4:
                $str = "星期四";
                break;
            case 5:
                $str = "星期五";
                break;
            case 6:
                $str = "星期六";
                break;
        }
        //取得航班有优惠的星期总数
        //实例一个全局变量
        $adapter = Zend_Registry::get('db');
        $fliPre_sql = "select count(fli_everyday) from flightinformation where fli_discount<1 and flag=1 and fli_refundtime='正常'";
        $fliPre_num = $adapter->fetchOne($fliPre_sql);
        //取得航班有优惠的星期
        $fliPre_week = "select fli_everyday from flightinformation where fli_discount<1 and flag=1 and fli_refundtime='正常'";
        $fliPreference_week = $db->getWeek($fliPre_week);
        foreach ($fliPreference_week as $key => $value) {
            $week = $value['fli_everyday'];
            //取得有航班优惠的信息
            $fliPreference_sql = "select com_code ,air_code,fli_baddress,fli_aaddress,fli_discount from flightinformation where fli_discount<1 and flag=1 and fli_refundtime='正常' " .
             " and fli_everyday='" . $week . "'";
            $fliPreferenceInfo = $db->getfliPreference($fliPreference_sql)->fetchAll();
            $paginator = Zend_Paginator::factory($fliPreferenceInfo);
            /* Zend_Paginator分页 */
            $numPerPage = $this->_numPerPage;
            $pageRange = $this->_pageRange;
            $page = $this->_request->getParam('page', 1);
            $paginator->setCurrentPageNumber($page)
                ->setItemCountPerPage($numPerPage)
                ->setPageRange($pageRange);
            for ($i = 0; $i < $fliPre_num; $i ++) {
                if (strcmp($str, $week)) {
                    $this->view->fliPreferenceInfo = $paginator;
                }
            }
        }
    }
    public function flidiscountPagelistAction ()
    {
        // action body
    }
}





