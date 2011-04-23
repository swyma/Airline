<?php
class CustomerController extends Zend_Controller_Action
{

    protected $_numPerPage = 10;

    protected $_discount=1;
    
    protected $_fare=1;
    
    protected $_pageRange = 10;

    protected $_user = '';

    public function init()
    {
        /* Initialize action controller here */
    }

    public function indexAction()
    {
        // action body
            //echo "hello";
    }

    public function bookticketAction()
    {
        // action body
    	$this->_helper->layout->disableLayout();
        $adapter = Zend_Registry::get('db');
        $param = $this->_request->getParam('parm');
        if ($param != "") {
            //将用户要订的航班信息传递到前台
            $book_flightinfo_sql = "select * from flightinformation where fli_autoid='" .
             $param . "'";
            $book_flightinfo = $adapter->fetchAll($book_flightinfo_sql);
            $this->view->book_flightinfo = $book_flightinfo;
        } else {
            echo "error";
        }
    }

    public function bookAction()
    {
    }

    public function ticketAction()
    {
        /**
                 * 从前台取来的数据:
                 * 1.用户	$cus_id
                 * 2.用户选择的航班:	$flight_no (形如:CA123)
                 * 3.航班起飞的日期:	$fli_everyday
                 * 4.用户选择的舱位:	$fli_cangwei
                 */
                //$db = new Application_Model_DbTable_Flightinformation();
                //实例一个全局变量
                $adapter = Zend_Registry::get('db');
                $this->_helper->layout->disableLayout();
                $this->_helper->contextSwitch()->initJsonContext();
                $this->getResponse()->setHeader('Content-Type', 'application/json');
                //业务一：
                //取得用户的id
                /**
                 * 这部分要等负责用户登录完成后才能进行
                 */
                //$cus_id = $this->_request->getPost("cus_id"); //success
                $cus_id="2";
                //echo $cus_id;
                //取得用户选择的航班号,用户输入的格式为:CA123
                $flight_no = $this->_request->getPost(
                "flight_no"); //success
                //echo $flight_no;
                //对取得的数据库进行拆分
                /*
                * 方法比较灵活
                * 解决方案:	1.可能要用户进行输入才行
                * 		  	2.前台获取
                * 			3.自己强拆
                */
                $com_code = substr($flight_no, 0, 2); //航空公司代号--->CA
                $fli_no = substr($flight_no, 2, 3); //取得航班号码--->123
                //var_dump($com_code="CC");
                //$fli_no="123";
                //取得选择的星期几的航班
                $fli_everyday = $this->_request->getPost(
                "fli_everyday");
                //业务二：
                /**
                 * 1.根据用户的ID和用户选择的航班号进行判断
                 * 2.涉及的表为customer->cus_id,bookinformation->com_code
                 * 3."select count(com_code) from bookinformation where cus_id='".$cus_id."' and com_code='".$com_code."'";
                 */
                if ($com_code != null && $cus_id != NULL && $fli_no != null) {
                    //首先判断有没有这个航班号
                    $fli_flightinformation = "select count(com_code) from flightinformation where flag=1 and fli_refundtime='正常' and fli_refund='运行' and fli_no='" .
                     $fli_no . "' and com_code='" . $com_code . "' and fli_everyday='" .
                     $fli_everyday . "'";
                    $flight_occur = $adapter->fetchOne($fli_flightinformation);
                    if ($flight_occur > 0) {
                        //航班处理运行状态
                        //后台业务:用户订票了flag=0,退票为flag=1
                        $accept = "select count(com_code) from bookinformation where flag_pass=0 and cus_id='" .
                         $cus_id . "' and boo_no ='" . $fli_no . "' and com_code='" .
                         $com_code . "' and boo_everyday='" . $fli_everyday . "'";
                        //$accept="select count(com_code) from bookinformation where cus_id='123' and com_code='CC'";
                        //取得总记录数,返回的是一个整型
                        $flightinformation = $adapter->fetchOne(
                        $accept);
                        if ($flightinformation == 0) {
                            //echo $flightinformation;
                            /*
                             * 用户无该航班记录，可以进行下一次操作
                             * 取得用户选择的舱位
                                                                					 */
                            $fli_cangwei = $this->_request->getPost("fli_cangwei");
                            if ($fli_cangwei != null) {
                                /**
                                 * 取舱位并取得相应的舱位的标准价格
                                 * 业务受后台设计影响,逻辑思维比较乱
                                 * 该业务要经过讨论后方可进行
                                 *
                                 * 方案: 1.系统进入退票表查看退票表信息
                                 * 2.退票有相应航班有相应航班,相应舱位的退票信息,则可以订票
                                 * 3.如果退票信息没有退票信息的,但在flightinformation中有相应航班,相应舱位的也可以订票
                                 */
                                //业务三:取得相应舱位的人数和标准价格
                                $fli_cangweiinfo = "";
                                if ($fli_cangwei == "公务舱") {
                                    $GLOBALS['$fli_cangweiinfo'] = "select fli_Ynumber as fli_number,fli_Yfare as fli_fare,fli_discount from flightinformation where flag=1 and com_code='" .
                                     $com_code . "' and fli_no='" . $fli_no .
                                     "' and fli_everyday='" . $fli_everyday . "'";
                                    //取得公务舱的现有人数和标准价格
                                    //首先进入退票表里看信息,如果有相应航班相应舱位的退票信息
                                    /**
                                     * 退票信息字段说明
                                     * 查询退票表信息:$refund_info_sql
                                     * 判断是否为空信息:$refund_info
                                     */
                                    $refund_info_sql = "select count(r.boo_number) from refundrecord r ,bookinformation b where r.boo_autoid=b.boo_autoid and b.com_code='" .
                                     $com_code . "' and b.boo_no='" . $fli_no .
                                     "' and b.boo_berth='" . $fli_cangwei .
                                     "' and b.boo_everyday='" . $fli_everyday . "'";
                                    $refund_info = $adapter->fetchOne($refund_info_sql);
                                    if ($refund_info > 0) {
                                        //如果退票表有相应航班相应舱位信息的话
                                        $permit = true;
                                    } else {
                                        //退票表里没有相关信息
                                        /**
                                         * $fli_cangweiinfo:用户所先舱位的相关信息的输出
                                         * $fli_cangweicount:舱位人数的多少
                                         */
                                        //判断该航班没有有没有座位提供
                                        $fli_cangwei_sql = "select count(*) from flightinformation where flag=1 and com_code='" .
                                         $com_code . "' and fli_no='" . $fli_no .
                                         "' and fli_everyday='" . $fli_everyday . "'";
                                        $fli_cangweicount = $adapter->fetchOne(
                                        $fli_cangwei_sql);
                                        if ($fli_cangweicount > 0) {
                                            //航班的相应舱位有座位提供
                                            $permit = true;
                                        } else {
                                            //航班没有,退票表也没有座位提供
                                            $permit = false;
                                        }
                                    }
                                } else 
                                    if ($fli_cangwei == "经济舱") {
                                        //取出经济舱所有关于该舱位的所有信息
                                        /**
                                         * 1.直接取经济舱的价格fli_Cfare
                                         */
                                        $GLOBALS['$fli_cangweiinfo'] = "select fli_Cnumber as fli_number,fli_Cfare as fli_fare,fli_discount as fli_discount from flightinformation where flag=1  and com_code='" .
                                         $com_code . "' and fli_no='" . $fli_no .
                                         "'and fli_everyday='" . $fli_everyday . "'";
                                        $refund_info_sql = "select count(r.boo_number) from refundrecord r ,bookinformation b where r.boo_autoid=b.boo_autoid and b.com_code='" .
                                         $com_code . "' and b.boo_no='" . $fli_no .
                                         "' and b.boo_berth='" . $fli_cangwei .
                                         "' and b.boo_everyday='" . $fli_everyday . "'";
                                        $refund_info = $adapter->fetchOne(
                                        $refund_info_sql);
                                        if ($refund_info > 0) {
                                            //如果退票表有相应航班相应舱位信息的话
                                            $permit = true;
                                        } else {
                                            //退票表里没有相关信息
                                            /**
                                             * $fli_cangweiinfo:用户所先舱位的相关信息的输出
                                             * $fli_cangweicount:舱位人数的多少
                                             */
                                            $fli_cangwei_sql = "select count(*) from flightinformation where flag=1 and fli_Cnumber>0 and com_code='" .
                                             $com_code . "' and fli_no='" . $fli_no .
                                             "' and fli_everyday='" . $fli_everyday . "'";
                                            $fli_cangweicount = $adapter->fetchOne(
                                            $fli_cangwei_sql);
                                            if ($fli_cangweicount > 0) {
                                                //航班的相应舱位有座位提供
                                                $permit = true;
                                            } else {
                                                //航班没有,退票表也没有座位提供
                                                $permit = false;
                                            }
                                        }
                                    } else 
                                        if ($fli_cangwei == "头等舱") {
                                            $GLOBALS['$fli_cangweiinfo'] = "select fli_Fnumber as fli_number,fli_Cfare as fli_fare,fli_discount from flightinformation where flag=1 and com_code='" .
                                             $com_code . "' and fli_no='" . $fli_no .
                                             "' and fli_everyday='" . $fli_everyday . "'";
                                            $refund_info_sql = "select count(r.boo_number) from refundrecord r ,bookinformation b where r.boo_autoid=b.boo_autoid and b.com_code='" .
                                             $com_code . "' and b.boo_no='" . $fli_no .
                                             "' and b.boo_berth='" . $fli_cangwei .
                                             "' and b.boo_everyday='" . $fli_everyday .
                                             "'";
                                            $refund_info = $adapter->fetchOne(
                                            $refund_info_sql);
                                            if ($refund_info > 0) {
                                                //如果退票表有相应航班相应舱位信息的话
                                                $permit = true;
                                            } else {
                                                //退票表里没有相关信息
                                                /**
                                                 * $fli_cangweiinfo:用户所先舱位的相关信息的输出
                                                 * $fli_cangweicount:舱位人数的多少
                                                 */
                                                $fli_cangwei_sql = "select count(*) from flightinformation where flag=1 and fli_Cnumber>0 and com_code='" .
                                                 $com_code . "' and fli_no='" . $fli_no .
                                                 "' and fli_everyday='" . $fli_everyday .
                                                 "'";
                                                $fli_cangweicount = $adapter->fetchOne(
                                                $fli_cangwei_sql);
                                                if ($fli_cangweicount > 0) {
                                                    //航班的相应舱位有座位提供
                                                    $permit = true;
                                                } else {
                                                    //航班没有,退票表也没有座位提供
                                                    $permit = false;
                                                }
                                            }
                                        }
                                //已经允许用户订票
                                if ($permit) {
                                    $moneyandseat = $adapter->query(
                                    $GLOBALS['$fli_cangweiinfo']);
                                    //$cangweiInfo = $moneyandseat->fetchAll();
                                    foreach ($moneyandseat as $key => $value) {
                                        $standardMoney = $value['fli_fare']; //取得标准价格
                                        $standardNumber = $value['fli_number']; //取得舱位现有座位数
                                        $fli_discount = $value['fli_discount']; //取得该航班的折扣
                                    }
                                    //舱位有空座
                                    //由于标准价格已经取出来了,进行下一步业务
                                    //业务四:获取用户的积分和等级情况
                                    /**
                                     * 此业务也会涉及到航空公司代号
                                     * 1.取得会员现有的积分
                                     * 2.取得积分表的金,银,铜的基本积分,以及相应的折扣
                                     * 3.将会员现在的积分和积分表的基本积分进行比对,然后进行相应的打折
                                     */
                                    $fare_type = "select * from faretype where far_comcode='" . $com_code . "'";
                                    $faretype = $adapter->query($fare_type);
                                    $faretype_info = $faretype->fetchAll();
                                    foreach ($faretype_info as $key => $value) {
                                        $far_goldscore = $value['far_goldscore'];
                                        $far_golddiscount = $value['far_golddiscount'];
                                        $far_silscore = $value['far_silscore'];
                                        $far_silddiscount = $value['far_sildiscount'];
                                        $far_broscore = $value['far_broscore'];
                                        $far_brodiscount = $value['far_brodiscount'];
                                    }
                                    //获取用户的积分信息
                                    $cus_integral = "select cus_integral from customer where cus_id='" . $cus_id . "'";
                                    //$discount=1;
                                    //实现业务:用户的积分与积分表的积分进行比较
                                    $cus_score = $adapter->fetchOne( $cus_integral);
                                    //积分进行比较,取得相应的折扣
                                    if($cus_integral>$far_broscore){
	                                    if ($cus_score >= $far_goldscore) {
	                                        $this->_discount = $far_golddiscount;
	                                    } else if ($cus_score >= $far_silscore) {
	                                        $this->_discount = $far_silddiscount;
	                                    } else if ($cus_score >= $far_broscore) {
	                                        $this->_discount = $far_brodiscount;
	                                    }
	                                    //折中折,生成最终的票价
	                                    $this->$_fare = $standardMoney * $this->_discount * $fli_discount; //最终用户价格
                                    }else{
                                    	$this->$_fare = $standardMoney * $fli_discount; //最终用户价格
                                    }
                                    
                                    
                                    
                                    /**
                                     * 业务:	生成座位号码
                                     * 涉及的表:refundrecord
                                     * 如何取boo_autoid是个问题,进行匹配
                                     * boo_autoid是个自增的字段,是主键,有唯一性,所以退票表的boo_autoid也是唯一的,通过多表联合查询,是可以取出来的
                                     */
                                    //取得航班号及航空公司信息,根据boo_autoid来判断,猛哦,这设计
                                    //获取用户boo_autoid
                                    //$boo_autoid="select boo_autoid from refundrecode";
                                    //$refund_ticket="select com_code,boo_no from bookinformation where ";
                                    $seat = "select r.boo_number from refundrecord r ,bookinformation b where r.boo_autoid=b.boo_autoid and b.flag_pass=1 and b.com_code='" .
                                     $com_code . "' and b.boo_no='" . $fli_no .
                                     "' and b.boo_everyday='" . $fli_everyday . "'";
                                    //取得第一条记录中的字段
                                    //取到第一个座位号
                                    $seat_virtual_no = $adapter->fetchOne(
                                    $seat);
                                    // 如果seat_no不能空就把取到的一个座位号安排给现有人员
                                    if ($seat_virtual_no !=
                                     null) {
                                        $seat_no = $seat_virtual_no; //真正的座位号
                                    } else {
                                        //退票表的信息为空的话,则到flightinformation找
                                        //得到相应航班的座位号
                                        if ($fli_cangwei ==
                                         "经济舱") {
                                            $seat_no_sql = "select fli_Cnumber as seat_no from flightinformation where com_code='" .
                                             $com_code . "' and fli_no='" . $fli_no .
                                             "' and flag=1";
                                            $seat_no = $adapter->fetchOne($seat_no_sql);
                                        } else 
                                            if ($fli_cangwei == "头等舱") {
                                                $seat_no_sql = "select fli_Fnumber as seat_no from flightinformation where com_code='" .
                                                 $com_code . "' and fli_no='" . $fli_no .
                                                 "' and flag=1";
                                                $seat_no = $adapter->fetchOne(
                                                $seat_no_sql);
                                            } else 
                                                if ($fli_cangwei == "公务舱") {
                                                    $seat_no_sql = "select fli_Ynumber as seat_no from flightinformation where com_code='" .
                                                     $com_code . "' and fli_no='" .
                                                     $fli_no . "' and flag=1";
                                                    $seat_no = $adapter->fetchOne(
                                                    $seat_no_sql);
                                                }
                                    }
                                    //得到用户最后真正的座位号
                                    $cus_real_seat = $seat_no;
                                    /**
                                     * 业务:	1.将订票信息插入到数据订票表信息中
                                     * 2.与些同时要更新数据的座位号信息
                                     *
                                     */
                                    //取得该航班的所有信息
                                    /**
                                     * 1.通过用户所先的唯一航班得出所有信息
                                     * 2.取出相应的字段进行取值
                                     * $fli_allinfo:查询航班信息的sql语句
                                     * $fli_info:航班信息的所有字段,(object)
                                     * $fli_information:航班信息的所有字段(array)
                                     */
                                    $fli_allinfo = "select * from flightinformation where com_code='" . $com_code . "' and fli_no='" . $fli_no . "' and fli_everyday='" . $fli_everyday . "'";
                                    //取出各个字段的值
                                    $fli_info = $adapter->query($fli_allinfo);
                                    $fli_information = $fli_info->fetchAll();
                                    foreach ($fli_information as $key => $value) {
                                        //$fli_everyday=$value['fli_everyday'];					//航班周几起飞
                                        $fli_aaddress = $value['fli_aaddress']; //航班起飞地点
                                        $fli_baddress = $value['fli_baddress']; //航班降落地点
                                        $fli_btime = $value['fli_btime']; //航班起飞时间
                                        $fli_atime = $value['fli_atime']; //航班降落时间
                                    }
                                    //将数据库的值insert到订票表里
                                    //$book_ticket="insert into bookinformation(com_code,cus_id,boo_everyday,boo_no,boo_baddress,boo_aaddress,boo_atime,boo_btime,boo_berth,boo_number,boo_fare,book_time,flag_pay,flag_type,flag_pass) values('".$com_code."','".$cus_id."','".$fli_erveryday."','".$fli_no."','".$fli_baddress."','".$fli_aaddress."','".$fli_btime."','".$fli_cangwei."','".$cus_real_seat."','".$fare."','".$time."','1','0','0')";
                                    //$insert_ticket_info=$adapter->query($book_ticket);		//插入数据
                                    //取得系统当前时间
                                    $time = date("Y-m-d H:i:s");
                                    //实例化
                                    $db = new Application_Model_DbTable_Bookinformation();
                                    //相关数据数组化,为insert服务
                                    $data = array(
                                    'com_code' => $com_code, 'cus_id' => $cus_id, 
                                    'boo_everyday' => $fli_everyday, 'boo_no' => $fli_no, 
                                    'boo_baddress' => $fli_baddress, 
                                    'boo_aaddress' => $fli_aaddress, 
                                    'boo_atime' => $fli_atime, 'boo_btime' => $fli_btime, 
                                    'boo_berth' => $fli_cangwei, 
                                    'boo_number' => $cus_real_seat, 'boo_fare' => $this->$_fare, 
                                    'boo_time' => $time, 'flag_pay' => '1', 
                                    'flag_type' => '1', 'flag_pass' => '0');
                                    //该方法的api是这样的insert($array)
                                    $reuslt_insert = $db->insert($data);
                                    if ($reuslt_insert > 0) {
                                        /**
                                         * 业务:更新用户的积分
                                         * 积分规则:票价多少就给积分多少；
                                         */
                                        //$cus_integral_update="update ";
                                        $db = new Application_Model_DbTable_Customer();
                                        /*$data_update=array('cus_integal'=>$fare+$cus_score);
                                        // where语句
                                        $where = $db->quoteInto('cus_id = ?', $cus_id);
                                        // 更新表数据,返回更新的行数
                                        $result_update = $db->update($data_update, $where);*/
                                        $data_update_sql = "update customer set cus_integral='" . ($this->$_fare + $cus_score) . "' where cus_id='" . $cus_id . "'";
                                        $result_update = $adapter->query($data_update_sql);
                                        if ($result_update) {
                                            /**
                                             * 业务:更新相应舱位的座位号
                                             * 座位号=座位号-1
                                             */
                                            $db = new Application_Model_DbTable_Flightinformation();
                                            //if($seat_virtual_no!=null){				//退票表信息不为空
                                            if ($fli_cangwei =="经济舱") {
                                                if ($seat_virtual_no != null) {
                                                    //删除退票表相应的信息
                                                    $seat_update = "delete from refundrecord where boo_autoid=(select boo_autoid from bookinformation where flag_pass=1 and com_code='" .
                                                     $com_code . "' and boo_no='" .
                                                     $fli_no . "' and boo_number='" .
                                                     $seat_virtual_no .
                                                     "' and boo_everyday='" .
                                                     $fli_everyday . "')";
                                                     //$reuslt=$adapter->query($seat_update);	//删除完毕
                                                } else {
                                                    $seat_update = "update flightinformation set fli_Cnumber='" .
                                                     ($seat_no - 1) . "' where fli_no='" .
                                                     $fli_no . "' and com_code='" .
                                                     $com_code . "' and fli_everyday='" .
                                                     $fli_everyday . "'";
                                                }
                                            } else if ($fli_cangwei == "头等舱") {
                                                    if ($seat_virtual_no != null) {
                                                        $seat_update = "delete from refundrecord where boo_autoid=(select boo_autoid from bookinformation where flag_pass=1 and com_code='" .
                                                         $com_code . "' and boo_no='" .
                                                         $fli_no . "' and boo_number='" .
                                                         $seat_virtual_no .
                                                         "' and boo_everyday='" .
                                                         $fli_everyday . "')";
                                                         //$reuslt=$adapter->query($delete_seat);	//删除完毕
                                                    } else {
                                                        $seat_update = "update flightinformation set fli_Fnumber='" .
                                                         ($seat_no - 1) .
                                                         "' where fli_no='" . $fli_no .
                                                         "' and com_code='" . $com_code .
                                                         "'and fli_everyday='" .
                                                         $fli_everyday . "'";
                                                    }
                                                } else if ($fli_cangwei == "公务舱") {
                                                        if ($seat_virtual_no != null) {
                                                            $seat_update = "delete from refundrecord where boo_autoid=(select boo_autoid from bookinformation where flag_pass=1 and com_code='" .
                                                             $com_code . "' and boo_no='" .
                                                             $fli_no .
                                                             "' and boo_number='" .
                                                             $seat_virtual_no .
                                                             "' and boo_everyday='" .
                                                             $fli_everyday . "')";
                                                             //$reuslt=$adapter->query($delete_seat);	//删除完毕
                                                        } else {
                                                            $seat_update = "update flightinformation set fli_Ynumber='" .
                                                             ($seat_no - 1) .
                                                             "' where fli_no='" . $fli_no .
                                                             "' and com_code='" .
                                                             $com_code .
                                                             "'and fli_everyday='" .
                                                             $fli_everyday . "'";
                                                        }
                                                    }
                                            $reuslt_update_seat = $adapter->query($seat_update);
                                            if ($reuslt_update_seat) {
                                                //更新成功完成,所有业务流程已经过了
                                                //生成机票编号:当前时间+用户id+用户的座号
                                                $ticket_no = time() .
                                                 $cus_id . $cus_real_seat;
                                                //将信息传递给前台,前台接受并显示相应的信息
                                                $show_sql = "select com_code,cus_id,boo_everyday,boo_no,boo_baddress,boo_aaddress,SUBSTRING(boo_btime,12,5) as boo_btime,SUBSTRING(boo_atime,12,5) as boo_atime,boo_berth,boo_number,boo_fare,boo_time from bookinformation where com_code='" .
                                                 $com_code . "' and cus_id='" . $cus_id . "' and boo_everyday='" . $fli_everyday . "' and boo_no='" . $fli_no . "' and flag_pass=0";
                                                //执行sql语句
                                                $flightinformation = $adapter->query($show_sql);
                                                $rows = $flightinformation->fetchAll();
                                                $ticketinformation = Zend_Json::encode($rows);
                                                echo $tick_informations = substr($ticketinformation, 0, strlen($ticketinformation) - 2) .(",\"ticket_no\":\"") . $ticket_no ."\"}]";
                                                 //echo "订票成功!";
                                            }
                                        }
                                    }
                                } else {
                                    echo "舱位已经满人";
                                }
                            }
                        } else {
                            echo "你已经订过此航班信息了,一人一次只能订一张票!";
                        }
                    } else {
                        echo "该航班不存在";
                    }
                     //echo $flightinformation;
                } else {
                    echo "前台传值错误!";
                }
    }

    public function customerregisterAction()
    {
        // action body
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
                }
                 //成功后跳转页面
    }

    public function customerloginAction()
    {
        $this->_helper->layout->disableLayout();
                Zend_Session::start();
                $customerNamespace = new Zend_Session_Namespace('customer'); //使用SESSION存储数据时要设置命名空间
                /*$userNamespace->setExpirationHops(5);// 5 次访问后，会话过期
                                                                        $userNamespace->setExpirationSeconds(60);//命名空间 "user" 将在第一次访问后 60 秒，或者访问 5 次后过期。
                                                                        $userNamespace->__unset();//注销session*/
                if (strtolower($_SERVER['REQUEST_METHOD']) == 'post') {
                    //取得前台得传过来的值
                    //$cus_id = $this->_request->getPost('cus_id');
                    $cus_account = $this->_request->getPost(
                    'cus_account');
                    $cus_pwd = $this->_request->getPost('cus_pwd');
                    $customerNamespace->cus_account = $cus_account; //设置值
                    $customerNamespace->cus_pwd = $cus_pwd;
                    if ($cus_account != null && $cus_pwd != null) {
                        //实例化
                        $db = new Application_Model_DbTable_Customer();
                        //实例一个全局变量
                        $adapter = Zend_Registry::get('db');
                        //查询登录会员的信息
                        $sqlstr1 = "select count(*) from customer where  cus_account='" .
                         $cus_account . "' and
                                                                          cus_pwd='" .
                         $cus_pwd . "' and flag=1";
                        echo $sqlstr1;
                        $result = $adapter->fetchOne($sqlstr1);
                        echo $result;
                        if ($result > 0) {
                            echo "success";
                            echo $this->_helper->redirector('customerinfomationshow');
                        } else {
                            //失败后返回
                            echo "账户或密码有错，请重新登录！";
                            echo $this->_helper->redirector('login');
                        }
                    }
                }
    }

    public function customerinfomationshowAction()
    {
        // action body
                $this->_helper->layout->disableLayout();
                Zend_Session::start();
                $customerNamespace = new Zend_Session_Namespace('customer');
                $cus_account = $customerNamespace->cus_account;
                $cus_pwd = $customerNamespace->cus_pwd;
                $db = new Application_Model_DbTable_Customer();
                //查询所有用户的信息
                $adapter = Zend_Registry::get('db');
                $sqlstr1 = "select * from customer where  cus_account='" . $cus_account . "' and
                                         cus_pwd='" . $cus_pwd .
                 "' and flag=1";
                $customerinfo = $adapter->query($sqlstr1);
                // echo ($customerinfo);
                $customerinfor = $customerinfo->fetchAll();
                $this->view->customers = $customerinfor;
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
                foreach ($this->customer as $key => $value) {
                    $customer_id = $value['cus_id'];
                    $customer_name = $value['cus_account'];
                }
                echo $customer_id;
                echo $customer_name;
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
                            echo "发表评论成功！";
                            echo $this->_helper->redirector('customerinformationshow');
                        }
                    } else {
                        //失败后返回
                        echo $this->_helper->redirector(
                        'customercomment');
                    }
                }
    }

    public function flightsearchAction()
    {
        // action body
                $this->_helper->layout->disableLayout();
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
                    $this->_helper->redirector('flightsearch', 'Customer', null, 
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

    public function pagelistAction()
    {
        // action body
    }

    public function bookhistoryAction()
    {
        // action body
                $this->_helper->layout->disableLayout();
                $user='1';
                $this->_user=$user;
                
                $db = new Application_Model_DbTable_Flightinformation();
                $sql = "select a.boo_autoid,a.com_code,a.boo_no,a.boo_everyday,a.boo_baddress,".
                "a.boo_aaddress,substring(a.boo_btime,12,5)as boo_btime,substring(a.boo_atime,12,5) as boo_atime,".
                "a.boo_berth,a.boo_number,a.boo_fare,a.boo_time,a.flag_pay,a.flag_pass ".
                "from bookinformation a,customer b ".
                "where a.cus_id=".$this->_user." and a.cus_id=b.cus_id and flag_type=1 ".
                "order by a.boo_time desc";
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

    public function refundticketAction()
    {
        // action body
    // action body
        $this->_helper->layout->disableLayout();
	    /**
	     * 用户退票业务
	     * 解决方案:	1.用户模糊查询
	     * 2.根据用户查询结果,div显示用户的订票信息
	     * 3.退票应注意,如果航班取消或者系统关闭时,则不能退票
	     */
        //得到用户订票的航班信息
        $adapter = Zend_Registry::get('db');
        $param = $this->_request->getParam('parm');
        if($param!=""){
        	//根据得到的boo_autoid获取用户订票的所有信息
        	/**
        	 * 	业务流程:	1.订票表信息标记删除
        	 * 			2.更新退票表
        	 * 			3.积分更新
        	 * 			4.成功返回
        	 * 
        	 *  保留业务:更新银行表(讨论后确定是否要增加)
        	 */
        	//1.
  			$refundticket_sql="select * from bookinformation where boo_autoid='".$param."'";
        	//取出各个字段的值
            $refundticket = $adapter->query($refundticket_sql);
            $refundticketAllinfo = $refundticket->fetchAll();
            foreach ($refundticketAllinfo as $key => $value) {
               $boo_number = $value['boo_number']; 				//航班座位号
               $cus_id = $value['cus_id']; 						//用户ID
               $boo_fare=$value['boo_fare'];					//票价,为积分更新服务
            }
            $update_bookticket_sql="update bookinformation set flag_pass='1' where boo_autoid='".$param."'";
            $update_bookticket=$adapter->query($update_bookticket_sql);
            if($update_bookticket){
            	//2.
            	$update_refundticket_sql="insert into refundrecord(boo_autoid,boo_number) values ('".$param."','".$boo_number."')";
            	$update_refundticket=$adapter->query($update_refundticket_sql);
            	if($update_refundticket){
            		//取得用户现有的积分
            		$cus_current_integral_sql="select cus_integral from customer where cus_id='" . $cus_id . "'";
            		$cus_current_integral=$adapter->fetchOne($cus_current_integral_sql);
            		
            		//3.
            		$update_customer_integral_sql="update customer set cus_integral='".($cus_current_integral - $boo_fare)."'";
            		$update_customer_integral=$adapter->query($update_customer_integral_sql);
            		if($update_customer_integral){
            			echo "退票成功!";
            		}else{
            			echo "积分表更新失败!";
            		}
            	}else{
            		echo "退票表信息更新失败!";
            	}
            }else{
            	echo "订票表退票更新失败!";
            }
            
        }
    }


}


























