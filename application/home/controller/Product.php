<?php
namespace app\home\controller;

use think\Config;
use \think\db;
use app\home\model\Goods as GoodsModel;

use app\home\model\Product as ProductMode;




/**
 * 产品控制器
 * @author duxinxin
 * @date 2020/04/26
 */

class Product extends Common
{

    /**
     * 产品列表
     *
     * @access index
     * @author duxinxin
     * @date 2020/04/26
     */
  public function index(){

      $GoodsModel  = new GoodsModel();
      $ProductMode = new ProductMode();

      $data =  $ProductMode->show();

      $classData =  $GoodsModel->classList(Config::get('CLASS_SHOP'));

      $class = array_combine(array_column($classData, 'id'), $classData);

      $classData = array_combine(array_column($classData, 'id'), $classData);


      foreach ($data as $k => $v){

              $classData[$v['class_id']]['show'][] = $v;

          }

       foreach($classData as $key => $val){

           if(isset($val['show'])){
               $classData[$key]['count'] = count($val['show']);
           }else{
               $classData[$key]['count'] = 0;

           }

        }

      $last_names = array_column($classData,'count');
      array_multisort($last_names,SORT_DESC,$classData);

      $this ->assign(
          [

              'classData'  => $class,

              'data' => $classData,

          ]
      );
      return $this->fetch();
  }

    /**
     * 添加产品
     *
     * @access add
     * @author duxinxin
     * @date 2020/04/26
     */

    public function add(){

         $postData = $this->request->post();

         $Data = [

             'product_name' => $postData['product_name'],

             'purchase'    => $postData['purchase'],

             'new_purchase'    => $postData['new_purchase'],

             'price'    => $postData['price'],

             'new_price'    => $postData['new_price'],

             'class_id'   => $postData['class'],

             'state'    => Config::get('STATE_YES'),

             'creat_time'  => time(),

         ];

         $ProductMode = new ProductMode();
         $data =  $ProductMode->insertAdd($Data);

         if($data) {
             return $this->success('保存成功', 'home/product/index');
         }else{
             return $this->error('保存失败', 'home/product/index');
         }

    }

    /**
     * 修改产品
     *
     * @access update
     * @author duxinxin
     * @date 2020/04/26
     */
    public function  update(){

        if ($this->request->isPost()){

            $id = $this->request->post('id');
            $purchase = $this->request->post('purchase');
            $flas = $this->request->post('flas');

            if($flas == 1){
                $where = ['purchase' => $purchase];
            } if($flas == 2){
                $where = ['new_purchase' => $purchase];
            } if($flas == 3){
                $where = ['price' => $purchase];
            } if($flas == 4){
                $where = ['new_price' => $purchase];
            }if($flas == 5){
                $where = ['product_name' => $purchase];
            }
        }else{

            $id = $this->request->get('id');
            $state = $this->request->get('status');
            $where = ['state' => $state];
        }

        $ProductMode = new ProductMode();

        $data = $ProductMode->edit($id, $where);

        if($data) {
            return json("操作成功！");
        }else{
            return json("操作失败！");
        }
    }

    /**
     * 清空现售价和现批价 操作
     *
     * @access clean
     * @author duxinxin
     * @date 2020/04/26
     */
    public function clean(){

        $where = [
            'new_price' => '',
            'price'     => '',
        ];
        $ProductMode = new ProductMode();
        $data = $ProductMode->clean($where);
        if($data) {
            return json("200");
        }else{
            return json("操作失败！");
        }

    }

    /**
     * 生成现售价 生成现批价
     *
     * @access creat
     * @author duxinxin
     * @date 2020/04/26
     */
    public function creat(){

        $flag = $this->request->post('flag');

        if($flag == 1){

            $price = $this->request->post('price');

            $where =  ['price' =>  Db::raw('purchase+'.$price)];

        }else if($flag == 2){

            $new_price = $this->request->post('new_price');

            $where =  ['new_price' =>  Db::raw('new_purchase+'.$new_price)];

        }

        $ProductMode = new ProductMode();

        $data = $ProductMode->clean($where);

        if($data) {
            return $this->success('生成成功', 'home/product/index');
        }else{
            return $this->error('生成失败', 'home/product/index');
        }
    }


    /**
     * 导出采购exel
     *
     * @access excel
     * @author duxinxin
     * @date 2020/04/26
     */
    public function excel(){

        ini_set('memory_limit','1024M');
        vendor("PHPExcel.PHPExcel");
        $objPHPExcel = new \PHPExcel();
        $modelname = "欧美采购报价表" . date('Y-m-d').'.xls';

        $GoodsModel = new GoodsModel();
        $ProductMode = new ProductMode();

        $classData =  $GoodsModel->classList(Config::get('CLASS_SHOP'));

        $classData = array_combine(array_column($classData, 'id'), $classData);

        $data =  $ProductMode->show();

        if (!$data) {
            echo '<script type="text/javascript">alert("无记录");</script>';
            exit;
        }


        foreach ($data as $k => $v){

            $classData[$v['class_id']]['show'][] = $v;

        }

        foreach($classData as $key => $val){

            if(isset($val['show'])){
                $classData[$key]['count'] = count($val['show']);
            }else{
                $classData[$key]['count'] = 0;
            }

        }

        $last_names = array_column($classData,'count');
        array_multisort($last_names,SORT_DESC,$classData);


        $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(30);
        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(10);
        $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(10);
        $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(5);

        // 表头
        $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A3')
            ->setCellValue('B3')
            ->setCellValue('C3')
            ->setCellValue('D3');
        //合并
        $objPHPExcel->getActiveSheet()->mergeCells('A1:S1');
        $objPHPExcel->getActiveSheet()->setCellValue('A1', date('Y-m-d').'欧美报价表');
        $objPHPExcel->getActiveSheet()->mergeCells('A2:S2');
        $objPHPExcel->getActiveSheet()->setCellValue('A2', '（10起、20起、30起、50起、100起另外议价）若有涨跌以当天实际价格为准！产品主要渠道：韩国免税店、海免、日上、欧洲个别国家免税店地区采购');

        $objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setName('宋体')->setSize(24)->setBold(true)->getColor()->setRGB('FF0000');
        $objPHPExcel->getActiveSheet()->getStyle('A2')->getFont()->setName('宋体')->setSize(18)->setBold(true)->getColor()->setRGB('FF0000');

        // 内容
        $column = 3;
        foreach($classData as $key => $val){

            $objPHPExcel->getActiveSheet(0)->setCellValue('A' . $column, $val['classname']);
            $objPHPExcel->getActiveSheet(0)->setCellValue('B' . $column, '单价');
            $objPHPExcel->getActiveSheet(0)->setCellValue('C' . $column, '5起');
            $objPHPExcel->getActiveSheet(0)->setCellValue('D' . $column, '');

            $objPHPExcel->getActiveSheet()->getStyle('A'.$column)->getFont()->setName('宋体')->setSize(15)->setBold(true)->getColor()->setRGB('FFFFFF');
            $objPHPExcel->getActiveSheet()->getStyle('A'.$column)->getFill()->applyFromArray(array('type' => \PHPExcel_Style_Fill::FILL_SOLID, 'startcolor' => array('rgb' => 'blue')));

            $objPHPExcel->getActiveSheet()->getStyle('B'.$column)->getFont()->setName('宋体')->setSize(15)->setBold(true)->getColor()->setRGB('FFFFFF');
            $objPHPExcel->getActiveSheet()->getStyle('B'.$column)->getFill()->applyFromArray(array('type' => \PHPExcel_Style_Fill::FILL_SOLID, 'startcolor' => array('rgb' => 'FF0000')));

            $objPHPExcel->getActiveSheet()->getStyle('C'.$column)->getFont()->setName('宋体')->setSize(15)->setBold(true)->getColor()->setRGB('FFFFFF');
            $objPHPExcel->getActiveSheet()->getStyle('C'.$column)->getFill()->applyFromArray(array('type' => \PHPExcel_Style_Fill::FILL_SOLID, 'startcolor' => array('rgb' => 'FF0000')));
            $column++;

            foreach($val['show'] as $k => $v){

                $objPHPExcel->getActiveSheet(0)->setCellValue('A' . $column, $v['product_name']);
                $objPHPExcel->getActiveSheet(0)->setCellValue('B' . $column, ($v['purchase'] == 0) ? ' ' : $v['purchase']);
                $objPHPExcel->getActiveSheet(0)->setCellValue('C' . $column, ($v['new_purchase'] == 0) ? ' ' : $v['new_purchase']);
                $objPHPExcel->getActiveSheet()->getStyle('A'.$column)->getFont()->setName('宋体')->setSize(13)->setBold(true);
                $objPHPExcel->getActiveSheet()->getStyle('B'.$column)->getFont()->setName('宋体')->setSize(13)->setBold(true)->getColor()->setRGB('FF0000');
                $objPHPExcel->getActiveSheet()->getStyle('C'.$column)->getFont()->setName('宋体')->setSize(13)->setBold(true)->getColor()->setRGB('FF0000');

                $column++;
            }
        }


        $objPHPExcel->getActiveSheet()->setTitle($modelname);
        $objPHPExcel->setActiveSheetIndex(0);

        // 输出
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename='.$modelname);
        header('Cache-Control: max-age=0');

        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');  //excel5为xls格式，excel2007为xlsx格式

        $objWriter->save('php://output');
        exit;

    }


    //导用户
    public function UserChuan(){

//        $sql = "select * from user order by id desc ";
//        $result = Db::connect("old_db")->query($sql);
//
//        $data = [];
//        foreach ($result as $key => $val){
//
//            $data[$key]['iphone'] = $val['phone'] ? $val['phone']: '';
//            $data[$key]['name'] = $val['username'] ? $val['username']: '';
//            $data[$key]['sex'] = '男';
//            $data[$key]['remarks'] = '智东西用户';
//            $data[$key]['addtime'] = time();
//            $data[$key]['status'] = 1;
//
//        }
//
//        $arr =  Db::table('WeChatUser')->insertAll($data);
//
//        print_r($arr);die;



//        ini_set('memory_limit','3072M');    // 临时设置最大内存占用为3G
//        set_time_limit(0);   // 设置脚本最大执行时间 为0 永不过期

//        $sql = "select wxid,max(name) as name from D_class_users  group by wxid";
//
//        $result = Db::connect()->query($sql);
//
//        $count = count($result);
//        $size = ceil($count/1000);
//
//        for ($i=1;$i<=$size;$i++) {
//
//
//            $sql1 = 'select wxid,max(name) as name,max(nickname) as  nickname, max(company) as company, max(post) as post from D_class_users  group by wxid  limit '.($i-1)*1000 .','.'1000';
//
//            $arr = Db::connect()->query($sql1);
//
//
//            if($arr){
//
//                $data = [];
//                foreach ($arr as $key => $val){
//
//                $data[$key]['WeChat'] = $val['wxid'] ? $val['wxid']: '';
//                $data[$key]['name'] = $val['name'] ? $val['name']: '';
//                $data[$key]['sex'] = '男';
//                $data[$key]['address'] = $val['nickname'].$val['company'].$val['post'];
//                $data[$key]['addtime'] = time();
//                $data[$key]['status'] = 10;
//
//                }
//
//               Db::table('WeChatUser')->insertAll($data);
//
//                unset($data);
//
//            }
//        }


//        $sql = "select tel FROM D_user_spacefield where tel != '' GROUP BY tel";
//
//        $result = Db::connect()->query($sql);
//
//        $count = count($result);
//
//
//        $size = ceil($count/1000);
//
//        for ($i=1;$i<=$size;$i++) {
//
//
//            $sql1 = "select tel,max(username) as username, max(sex) as sex,max(resideprovince) as resideprovince,max(residecity) as residecity,max(job) as job,max(userinfo) as userinfo,max(weixin) as weixin  FROM D_user_spacefield where tel != ' ' "."GROUP BY tel  limit ".($i-1)*1000 .','.'1000';
//
//            $arr = Db::connect()->query($sql1);
//
//
//            if($arr){
//
//                $data = [];
//
//                foreach ($arr as $key => $val){
//
//                $data[$key]['WeChat'] = $val['tel'] ? $val['tel']: '';
//                $data[$key]['name'] = $val['username'] ? $val['username']: '';
//                $data[$key]['sex'] = $val['sex'] = 1 ? '男' : '女';
//                $data[$key]['address'] = $val['resideprovince'].'-'.$val['residecity'].'-'.$val['job'];
//                $data[$key]['remarks'] = $val['userinfo'];
//                $data[$key]['addtime'] = time();
//                $data[$key]['status'] = 10;
//
//                }
//
//               Db::table('WeChatUser')->insertAll($data);
//
//                unset($data);
//
//            }
//        }

        echo 1;die;

//        return Db::table('D_class_users')->select();



    }


    //处理车东西 数据 平均值处理
    public function chuli(){

        ini_set('memory_limit','1024M');
        //引入类库

        $filename = ROOT_PATH . 'public' . DS . 'uploads/车东西数据2020.1.1_2020.9.30.xls';

        vendor("PHPExcel.PHPExcel");

        $objPHPExcel = new \PHPExcel();

        $objReader = \PHPExcel_IOFactory::createReader('Excel5');        //如果写称Excel2017有些情况下会报错
        $obj_PHPExcel = $objReader->load($filename, $encode = 'utf-8');             //加载文件内容,编码utf-8
        $excel_array = $obj_PHPExcel->getsheet(0)->toArray();

//        print_r($excel_array);die;

        //获取全部的
        $arr = [];
        foreach($excel_array as $k => $v){

            if($v[10] == '全部'){
                $v['data'] = substr($v[0] , 0,4).'-'.substr($v[0] , 4,2).'-'.substr($v[0] , 6,2);
               $arr[]= $v;
            }
        }


        $res = [];
        //排除周末的
        foreach ($arr as $key => $val){

            $time = $this->week($val['data']);

            if($time == '2'){

                $res[]= $val;

            }

        }


        $a_week = [];
        // 得到每周 数据
        foreach($res as $i => $j){

            $b = date('W',strtotime($j['data']));

            if($b){
                $a_week[$b][] = ++ $j;
            }

        }

        //处理每周数据
        foreach($a_week as $u => $t){
            $datatime = '';

            $sum = 0;
            foreach($t as $t2){

                $sum +=(int) $t2[1];
                $datatime .= "\n".$t2['data'] ;

            }

            $num = count($t);
            $a_week[$u]['start'] = end($t)['data'];
            $a_week[$u]['end'] = reset($t)['data'];
            $a_week[$u]['count'] = $num;
            $a_week[$u]['sum'] = $sum;
            $a_week[$u]['agv'] =  number_format($sum/$num, 2);
            $a_week[$u]['week'] = $u;
        }

//        print_r($a_week);die;


        //处理每月数据
        $a_month = [];
        foreach($res as $i => $j){

            $b = date('m',strtotime($j['data']));
            if($b){
                $a_month[$b][] = ++ $j;
            }

        }


        foreach($a_month as $u => $t){

            $sum = 0;
            $datatime = '';
            foreach($t as $t2){

                $sum +=(int) $t2[1];

                $datatime .= "\n".$t2['data'] ;
            }


            $num = count($t);

            $a_month[$u]['start'] = end($t)['data'];
            $a_month[$u]['end'] = reset($t)['data'];
            $a_month[$u]['count'] = $num;
            $a_month[$u]['sum'] = $sum;
            $a_month[$u]['agv'] =  number_format($sum/$num, 2);
            $a_month[$u]['month'] = $u;
        }


//        print_r($a_month);die;

        //处理每季度
        $a_jidu = [];
        foreach($res as $i => $j){


            $b = ceil((date('n', strtotime($j['data'])))/3);
            if($b){
                $a_jidu[$b][] = ++ $j;
            }

        }

        //处理每季度
        foreach($a_jidu as $u => $t){

            $sum = 0;
            $datatime = '';

            foreach($t as $t2){

                $sum +=(int) $t2[1];
            }

            $num = count($t);
            $a_jidu[$u]['start'] = end($t)['data'];
            $a_jidu[$u]['end'] = reset($t)['data'];
            $a_jidu[$u]['count'] = $num;
            $a_jidu[$u]['sum'] = $sum;
            $a_jidu[$u]['agv'] =  number_format($sum/$num, 2);
            $a_jidu[$u]['jidu'] = $u;
        }


        $objPHPExcel = new \PHPExcel();
        //4.激活当前的sheet表
        $objPHPExcel->setActiveSheetIndex(0);
        //5.设置表格头（即excel表格的第一行）
        $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A1', '日期')
            ->setCellValue('B1', '全部阅读次数总和')
            ->setCellValue('C1', '全部阅读次数平均数')
//            ->setCellValue('D1', '时间范围')

            ->setCellValue('E1', '日期')
            ->setCellValue('F1', '全部阅读次数总和')
            ->setCellValue('G1', '全部阅读次数平均数')
//            ->setCellValue('H1', '时间范围')

            ->setCellValue('I1', '日期')
            ->setCellValue('J1', '全部阅读次数总和')
            ->setCellValue('K1', '全部阅读次数平均数');
//            ->setCellValue('L1', '时间范围');
        //设置A列水平居中
        $objPHPExcel->setActiveSheetIndex(0)->getStyle('A1')->getAlignment()
            ->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

        //6.循环刚取出来的数组，将数据逐一添加到excel表格。
        $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('B')->setWidth(15);
        $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('C')->setWidth(20);
        $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('E')->setWidth(20);
        $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('F')->setWidth(20);
        $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('G')->setWidth(20);
        $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('I')->setWidth(15);
        $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('J')->setWidth(20);
        $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('K')->setWidth(20);


        foreach($a_week as $c => $m){


            $objPHPExcel->getActiveSheet()->setCellValue('A'.($c+2),'第'.$m['week'].'周');//日期
            $objPHPExcel->getActiveSheet()->setCellValue('B'.($c+2),$m['sum']);//全部阅读次数总和
            $objPHPExcel->getActiveSheet()->setCellValue('C'.($c+2),$m['agv']);//全部阅读次数平均数
//            $objPHPExcel->getActiveSheet()->setCellValue('D'.($c+2),$m['datatime']);//日期

        }
//
        foreach($a_month as $c => $m){

                $objPHPExcel->getActiveSheet()->setCellValue('E'.($c+2),'第'.$m['month'].'月');//ID
                $objPHPExcel->getActiveSheet()->setCellValue('F'.($c+2),$m['sum']);//标签码
                $objPHPExcel->getActiveSheet()->setCellValue('G'.($c+2),$m['agv']);//防伪码
//                $objPHPExcel->getActiveSheet()->setCellValue('H'.($c+2),$m['datatime']);//防伪码

        }
//
        foreach($a_jidu as $c => $m){

                $objPHPExcel->getActiveSheet()->setCellValue('I'.($c+2),'第'.$m['jidu'].'季度');//ID
                $objPHPExcel->getActiveSheet()->setCellValue('J'.($c+2),$m['sum']);//标签码
                $objPHPExcel->getActiveSheet()->setCellValue('K'.($c+2),$m['agv']);//防伪码
//                $objPHPExcel->getActiveSheet()->setCellValue('L'.($c+2),$m['datatime']);//防伪码

        }
        //7.设置保存的Excel表格名称
        $filename = '车东西数据处理表'.date('ymd',time()).'.xls';
        //8.设置当前激活的sheet表格名称；
        $objPHPExcel->getActiveSheet()->setTitle('车东西数据处理');
        //9.设置浏览器窗口下载表格
        header("Content-Type: application/force-download");
        header("Content-Type: application/octet-stream");
        header("Content-Type: application/download");
        header('Content-Disposition:inline;filename="'.$filename.'"');
        //生成excel文件
        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        //下载文件在浏览器窗口
        $objWriter->save('php://output');
        exit;



    }


    function week($str){

        if((date('w',strtotime($str))==6) || (date('w',strtotime($str)) == 0)){
            return '1';
        }else{
           return '2';
        }
    }

}
