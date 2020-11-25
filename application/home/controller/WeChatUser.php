<?php
namespace app\home\controller;

use think\Config;
use \think\db;

use app\home\model\WeChatUser as WeChatUserMode;
use app\home\controller\Helps\imgHelper as imgHelper;
use app\home\controller\Helps\orc as orc;



/**
 * 微信客户管理控制器
 * @author duxinxin
 * @date 2020/04/26
 */

class WeChatUser extends Common
{



    //图片识别测试

    public function  test1(){

        $imgPath = "http://127.0.0.1/thinkphp/public/uploads/invoicePhoto/20200811/52fe6aa5b044b1f64ca8679bee9cb583.jpeg";
        $gjPhone = new orc($imgPath);

//进行颜色分离
        $gjPhone->getHec();

//画出横向数据
        $horData = $gjPhone->magHorData();

//      print_r($horData);die;
        echo "===============横向数据==============<br/><br/><br/>";
//        $gjPhone->drawWH($horData);
// 画出纵向数据
//        $verData = $gjPhone->magVerData($horData);
//        echo "<br/><br/><br/>===============纵向数据==============< br/><br/><br/>";
//        $gjPhone->drawWH($verData);

// 输出电话
        $phone = $gjPhone->showPhone($horData);

        print_r($phone);die;


    }
    /**
     * 微信客户列表
     *
     * @access Show
     * @author duxinxin
     * @date 2020/04/26
     */
    public function Show(){
        $imgHelper = new imgHelper();
        $imgHelper->pvConfig('pv::WeChatUserShow', '微信客户列表');

        $status = $this->request->get('status');

        if(empty($status)){
            $status = Config::get('STATE_YES');
        }
        $condition = $this->build($this->request->get(), $status);

        $WeChatUserMode = new WeChatUserMode();
        $data =  $WeChatUserMode->show($condition);
        $this ->assign(
            [
                'list'       => $data,
                'search'     => $condition['where'],

                ]

        );
        return $this->fetch();
    }


    public function build($params, $state){

        $condition = [];

        if (!empty($params['iphone'])) {
            $condition['iphone'] = trim($params['iphone']);
        }
        if (!empty($params['WeChat'])) {
            $condition['WeChat'] = trim($params['WeChat']);
        }

        $condition['status'] = $state;

        return $params =  [
            'where'    => $condition,
        ];
    }

    /**
     * 微信客户添加
     *
     * @access add
     * @author duxinxin
     * @date 2020/04/26
     */
    public function add(){

            $imgHelper = new imgHelper();
            //添加pv
            $imgHelper->pvConfig('pv::WeChatUserAdd', '微信客户添加页面');

            return $this->fetch();

    }

    public function addpost(){

        $postData = $this->request->post();


        $Data = [

            'name' => !empty($postData['name'])? $postData['name'] : '',

            'WeChat' => !empty($postData['WeChat'])? $postData['WeChat'] : '',

            'iphone' => !empty($postData['iphone'])? $postData['iphone'] : '',

            'sex' => !empty($postData['sex'])? $postData['sex'] : '',

            'remarks' => !empty($postData['remarks'])? $postData['remarks'] : '',

            'address' => !empty($postData['address'])? $postData['address'] : '',

            'status' => Config::get('STATE_YES'),

            'addtime' => time(),

        ];
        $WeChatUserMode = new WeChatUserMode();
        $data =  $WeChatUserMode->dataInsert($Data);

        if($data) {
            return json("保存成功！");
        }else{
            return json("保存失败！");
        }

    }





    /**
     * 微信客户状态操作
     *
     * @access status
     * @author duxinxin
     * @date 2020/04/26
     */
    public function status(){

        $id = $this->request->post('id');
        $status = $this->request->post('status');
        $WeChatUserMode = new WeChatUserMode();
        $where = ['status' => $status];

        $data = $WeChatUserMode->edit($id, $where);

        if($data) {
            return json("操作成功！");
        }else{
            return json("操作失败！");
        }
    }



    /**
     * 微信客户编辑操作
     *
     * @access edit
     * @author duxinxin
     * @date 2020/04/26
     */
    public function edit(){

            $id = $this->request->get('id');
            $WeChatUserMode = new WeChatUserMode();
            $data =  $WeChatUserMode->WeChatUserFind(['id' => $id]);

            $getStatus = $this->getStatus();

            //添加pv
            $imgHelper = new imgHelper();
            $imgHelper->pvConfig('pv::WeChatUserEdit', '客户微信编辑页面');
            $this ->assign([
                'data'       => $data,
                'getStatus'       => $getStatus,
            ]);
            return $this->fetch();

    }

    public function  editpost(){

        $id = $this->request->post('id');
        $postData = $this->request->post();


        $Data = [

            'name' => !empty($postData['name'])? $postData['name'] : '',

            'WeChat' => !empty($postData['WeChat'])? $postData['WeChat'] : '',

            'iphone' => !empty($postData['iphone'])? $postData['iphone'] : '',

            'sex' => !empty($postData['sex'])? $postData['sex'] : '',

            'remarks' => !empty($postData['remarks'])? $postData['remarks'] : '',

            'address' => !empty($postData['address'])? $postData['address'] : '',

            'status' => $postData['status'],


        ];

        $WeChatUserMode = new WeChatUserMode();
        $data =  $WeChatUserMode->edit($id, $Data);

        if($data) {
            return json("修改成功！");
        }else{
            return json("修改失败！");
        }
    }

    /**
     *  配置状态数组
     *
     *
     * @method getStatus
     */

    public function getStatus(){

        return $getStatus =  [

            Config::get('STATE_YES') => [
                'id' => Config::get('STATE_YES'),
                'name' => '正常'
            ],

            Config::get('STATE_NO') => [
                'id' => Config::get('STATE_NO'),
                'name' => '删除'
            ],

            Config::get('STATE_SIGN') => [
                'id' => Config::get('STATE_SIGN'),
                'name' => '标记'
            ],
            Config::get('STATE_SHENHE') => [
                'id' => Config::get('STATE_SHENHE'),
                'name' => '待审核'
            ],
            Config::get('STATE_OK') => [
                'id' => Config::get('STATE_OK'),
                'name' => '已完成'
            ],

        ];
    }


    /**
     * 微信客户回收站列表
     *
     * @access userbin
     * @author duxinxin
     * @date 2020/04/26
     */
    public function userbin(){

        $imgHelper = new imgHelper();
        //添加pv
        $imgHelper->pvConfig('pv::WeChatUserBin', '微信客户回收站页面');
        $condition = $this->build($this->request->get(), Config::get('STATE_NO'));

        $WeChatUserMode = new WeChatUserMode();
        $data =  $WeChatUserMode->show($condition);


        $this ->assign(
            [
                'list'       => $data,
                'search'     => $condition['where'],
            ]
        );
        return $this->fetch();
    }

    /**
     * 微信客户申请好友列表
     *
     * @access userStart
     * @author duxinxin
     * @date 2020/04/26
     */
    public function userstart(){

        $imgHelper = new imgHelper();
        //添加pv
        $imgHelper->pvConfig('pv::WeChatUserBinStart', '微信客户申请好友列表页面');
        $condition = $this->build($this->request->get(), Config::get('STATE_SIGN'));

        $WeChatUserMode = new WeChatUserMode();
        $data =  $WeChatUserMode->show($condition);


        $this ->assign(
            [
                'list'       => $data,
                'search'     => $condition['where'],
            ]
        );
        return $this->fetch();
    }

    /**
     * 微信客户好友列表
     *
     * @access userStart
     * @author duxinxin
     * @date 2020/04/26
     */
    public function showlist(){

        $imgHelper = new imgHelper();
        //添加pv
        $imgHelper->pvConfig('pv::WeChatUserBinStart', '微信客户申请好友列表页面');
        $condition = $this->build($this->request->get(), Config::get('STATE_OK'));

        $WeChatUserMode = new WeChatUserMode();
        $data =  $WeChatUserMode->show($condition);


        $this ->assign(
            [
                'list'       => $data,
                'search'     => $condition['where'],
            ]
        );
        return $this->fetch();
    }

    /**
     * 微信客户待审核列表
     *
     * @access usergo
     * @author duxinxin
     * @date 2020/04/26
     */
    public function usergo(){

        $imgHelper = new imgHelper();
        //添加pv
        $imgHelper->pvConfig('pv::WeChatUserGo', '微信客户待审核页面');
        $condition = $this->build($this->request->get(), Config::get('STATE_SHENHE'));

        $WeChatUserMode = new WeChatUserMode();
        $data =  $WeChatUserMode->show($condition);


        $this ->assign(
            [
                'list'       => $data,
                'search'     => $condition['where'],
            ]
        );
        return $this->fetch();
    }

    /**
     * 微信客户导入
     *
     * @access import
     * @author duxinxin
     * @date 2020/04/26
     */
    public function import(){

        vendor("PHPExcel.PHPExcel");
        $excel = new \PHPExcel();

        $file = request()->file('file');  // 获取表单上传文件

        if(empty($file)){
            return $this->error('请选择上传文件', 'home/wechatuser/usergo');
        }

        $info = $file->move(ROOT_PATH . 'public' . DS . 'uploads/weixinPhoto/Excel');

        if($info){

            $filename = ROOT_PATH . 'public' . DS . 'uploads/weixinPhoto/Excel/' .$info->getSaveName();
            $objReader = \PHPExcel_IOFactory::createReader('Excel5');        //如果写称Excel2017有些情况下会报错
            $obj_PHPExcel = $objReader->load($filename, $encode = 'utf-8');             //加载文件内容,编码utf-8
            $excel_array = $obj_PHPExcel->getsheet(0)->toArray();               //转换为数组格式
            $arr = reset($excel_array);     //excel表格第一行的值
            unset($excel_array[0]);         //删除第一个数组(标题)

            if(count($arr) < 6) {
                unlink($filename);
                return $this->error('您的表格格式不正确', 'home/wechatuser/usergo');
            }
            //数据正确，开始导入数据
            foreach($excel_array as $k => $v) {

                if(!empty($v[0])){
                    $data[] = [
                        'name' => !empty($v[0]) ? $v[0]: '',
                        'iphone'=> !empty($v[1]) ? $v[1]: '',
                        'WeChat' => !empty($v[2]) ? $v[2]: '',
                        'sex' => !empty($v[3]) ? $v[3]: '',
                        'address' => !empty($v[4]) ? $v[4]: '',
                        'remarks' => !empty($v[5]) ? $v[5]: '',
                        'status' => Config::get('STATE_YES'),
                        'addtime' => time(),

                    ];
                }

            }
        }
        $WeChatUserMode = new WeChatUserMode();
        $arr = $WeChatUserMode->insertAddAll($data);
        if($arr){
            unlink($filename);
            return $this->success('导入成功', 'home/wechatuser/usergo');
        }else{
            return $this->error('导入失败', 'home/wechatuser/usergo');
        }


    }

    /**
     * 微信客户导入模版下载
     *
     * @access import
     * @author duxinxin
     * @date 2020/04/26
     */
    public function module(){

        $header=['客户名称','手机号','微信号','性别','地址','备注'];
        $data=[
            ['案例1','iphone','WeChat','男','某某市某某区某某县某某楼','备注'],
        ];
        $this->excelExport($header,$data);
    }

    public function excelExport( $headArr = [], $data = []) {

        $fileName = '客户信息模板'. date("Y-m-d", time()) . ".xls";
        vendor("PHPExcel.PHPExcel");
        $objPHPExcel = new \PHPExcel();
        $objProps =$objPHPExcel->getProperties();
        $key = ord("A"); // 设置表头

        foreach ($headArr as $v) {
            $colum = chr($key);
            $objPHPExcel->getActiveSheet()->getColumnDimension($colum)->setWidth(30);
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($colum . '1' , $v);
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($colum . '1', $v);
            $key += 1;
        }
        $column = 2;
        $objActSheet = $objPHPExcel->getActiveSheet();
        foreach ($data as $key => $rows) { // 行写入
            $span = ord("A");
            foreach ($rows as $keyName => $value) { // 列写入
                $objActSheet->setCellValue(chr($span) . $column, $value);
                $span++;
            }
            $column++;
        }

        $fileName = iconv("utf-8", "utf-8", $fileName); // 重命名表

        $objPHPExcel->setActiveSheetIndex(0); // 设置活动单指数到第一个表,所以Excel打开这是第一个表
        header('Content-Type: application/vnd.ms-excel');
        header("Content-Disposition: attachment;filename=$fileName");
        header('Cache-Control: max-age=0');
        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save('php://output'); // 文件通过浏览器下载
        exit();
    }


    /**
     * 微信客户导出
     *
     * @access usergo
     * @author duxinxin
     * @date 2020/04/26
     */
    public function excel()
    {
        ini_set('memory_limit','1024M');
        vendor("PHPExcel.PHPExcel");
        $objPHPExcel = new \PHPExcel();
        $objProps = $objPHPExcel->getProperties();
        $modelname = "微信客户信息列表" . date('Y-m-d').'.xls';
        $WeChatUserMode = new WeChatUserMode();
        $where['status'] = Config::get('STATE_YES');
        $showdata =  $WeChatUserMode->showExcel($where);

        if (!$showdata) {
            echo '<script type="text/javascript">alert("无记录");</script>';
            exit;
        }
        $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(10);
        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(30);
        $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(5);
        $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(70);
        $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(30);
        $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(10);
        // 表头
        $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A1', '用户ID')
            ->setCellValue('B1', '客户名称')
            ->setCellValue('C1', '电话')
            ->setCellValue('D1', '微信')
            ->setCellValue('E1', '性别')
            ->setCellValue('F1', '地址')
            ->setCellValue('G1', '备注')
            ->setCellValue('H1', '创建时间');
        // 内容
        $column = 2;
        foreach ($showdata as $k => $data) {
            $objPHPExcel->getActiveSheet(0)->setCellValue('A' . $column, $data['id']);
            $objPHPExcel->getActiveSheet(0)->setCellValue('B' . $column, !empty($data['name']) ? $data['name']: '暂无');
            $objPHPExcel->getActiveSheet(0)->setCellValue('C' . $column, !empty($data['iphone']) ? '手机号：'.$data['iphone']: '暂无');
            $objPHPExcel->getActiveSheet(0)->setCellValue('D' . $column, !empty($data['WeChat']) ? '微信号：'.$data['WeChat']: '暂无');
            $objPHPExcel->getActiveSheet(0)->setCellValue('E' . $column, !empty($data['sex']) ? $data['sex']: '暂无');
            $objPHPExcel->getActiveSheet(0)->setCellValue('F' . $column, !empty($data['address']) ? $data['address']: '暂无');
            $objPHPExcel->getActiveSheet(0)->setCellValue('G' . $column, !empty($data['remarks']) ? $data['remarks']: '暂无');
            $objPHPExcel->getActiveSheet(0)->setCellValue('H' . $column, date("Y-m-d H:i:s",$data['addtime']));
            $column++;
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

    //同步

    public function go(){

        $a = 0.14;
        $b = sprintf("%.2f",$a * 10);;
//        $b = 1400;
        $c = 1.4;

        var_dump($b);die;

        echo "<script>alert('暂停中')</script>";

        echo '暂停中';die;
        $WeChatUserMode = new WeChatUserMode();
        $data =  $WeChatUserMode->showSync();


        $newData = [];
        $i = 0;

        foreach ($data as $k => $v) {

            $newData[$i]['name'] = !empty($v['name']) ? $v['name']: '';
            $newData[$i]['WeChat'] = !empty($v['contact']) ? $v['contact']: '';
            $newData[$i]['iphone'] = !empty($v['phone']) ? $v['phone']: '';
            $newData[$i]['sex'] = !empty($v['fax']) ? $v['fax']: '';
            $newData[$i]['address'] = !empty($v['address']) ? $v['address']: '';
            $newData[$i]['remarks'] = !empty($v['desc']) ? $v['desc']: '';
            $newData[$i]['addtime'] = !empty($v['add_time']) ? $v['add_time']: '';
            $newData[$i]['status'] = 1 ;
            $i++;
        }

        $count = count($data);
        $size = ceil($count/100);

        for ($i=1;$i<=$size;$i++) {

            $arr = $WeChatUserMode->insertAddAll(array_slice($newData,($i-1)*100,100));

        }
        if($arr){
            echo '完成';die;
        }else{
            echo '失败';die;
        }


    }


    public function gosync(){
        echo "<script>alert('暂停中')</script>";

        echo '暂停中';die;
        $WeChatUserMode = new WeChatUserMode();
        $data =  $WeChatUserMode->showSyncA();


        foreach($data as $key => $val){
            if($val['address']){
               $v['remarks'] = $val['address'];
               $v['address'] = '';
            }

            $data =  $WeChatUserMode->edit($val['id'],$v);

        }
       if($data){
            echo 'success';die;
       }else{
            echo 'error';die;
       }

    }


   public function sys_randname(){

        $nicheng_tou=array('快乐的','冷静的','醉熏的','潇洒的','糊涂的','积极的','冷酷的','深情的','粗暴的','温柔的','可爱的','愉快的','义气的','认真的','威武的','帅气的','传统的','潇洒的','漂亮的','自然的','专一的','听话的','昏睡的','狂野的','等待的','搞怪的','幽默的','魁梧的','活泼的','开心的','高兴的','超帅的','留胡子的','坦率的','直率的','轻松的','痴情的','完美的','精明的','无聊的','有魅力的','丰富的','繁荣的','饱满的','炙热的','暴躁的','碧蓝的','俊逸的','英勇的','健忘的','故意的','无心的','土豪的','朴实的','兴奋的','幸福的','淡定的','不安的','阔达的','孤独的','独特的','疯狂的','时尚的','落后的','风趣的','忧伤的','大胆的','爱笑的','矮小的','健康的','合适的','玩命的','沉默的','斯文的','香蕉','苹果','鲤鱼','鳗鱼','任性的','细心的','粗心的','大意的','甜甜的','酷酷的','健壮的','英俊的','霸气的','阳光的','默默的','大力的','孝顺的','忧虑的','着急的','紧张的','善良的','凶狠的','害怕的','重要的','危机的','欢喜的','欣慰的','满意的','跳跃的','诚心的','称心的','如意的','怡然的','娇气的','无奈的','无语的','激动的','愤怒的','美好的','感动的','激情的','激昂的','震动的','虚拟的','超级的','寒冷的','精明的','明理的','犹豫的','忧郁的','寂寞的','奋斗的','勤奋的','现代的','过时的','稳重的','热情的','含蓄的','开放的','无辜的','多情的','纯真的','拉长的','热心的','从容的','体贴的','风中的','曾经的','追寻的','儒雅的','优雅的','开朗的','外向的','内向的','清爽的','文艺的','长情的','平常的','单身的','伶俐的','高大的','懦弱的','柔弱的','爱笑的','乐观的','耍酷的','酷炫的','神勇的','年轻的','唠叨的','瘦瘦的','无情的','包容的','顺心的','畅快的','舒适的','靓丽的','负责的','背后的','简单的','谦让的','彩色的','缥缈的','欢呼的','生动的','复杂的','慈祥的','仁爱的','魔幻的','虚幻的','淡然的','受伤的','雪白的','高高的','糟糕的','顺利的','闪闪的','羞涩的','缓慢的','迅速的','优秀的','聪明的','含糊的','俏皮的','淡淡的','坚强的','平淡的','欣喜的','能干的','灵巧的','友好的','机智的','机灵的','正直的','谨慎的','俭朴的','殷勤的','虚心的','辛勤的','自觉的','无私的','无限的','踏实的','老实的','现实的','可靠的','务实的','拼搏的','个性的','粗犷的','活力的','成就的','勤劳的','单纯的','落寞的','朴素的','悲凉的','忧心的','洁净的','清秀的','自由的','小巧的','单薄的','贪玩的','刻苦的','干净的','壮观的','和谐的','文静的','调皮的','害羞的','安详的','自信的','端庄的','坚定的','美满的','舒心的','温暖的','专注的','勤恳的','美丽的','腼腆的','优美的','甜美的','甜蜜的','整齐的','动人的','典雅的','尊敬的','舒服的','妩媚的','秀丽的','喜悦的','甜美的','彪壮的','强健的','大方的','俊秀的','聪慧的','迷人的','陶醉的','悦耳的','动听的','明亮的','结实的','魁梧的','标致的','清脆的','敏感的','光亮的','大气的','老迟到的','知性的','冷傲的','呆萌的','野性的','隐形的','笑点低的','微笑的','笨笨的','难过的','沉静的','火星上的','失眠的','安静的','纯情的','要减肥的','迷路的','烂漫的','哭泣的','贤惠的','苗条的','温婉的','发嗲的','会撒娇的','贪玩的','执着的','眯眯眼的','花痴的','想人陪的','眼睛大的','高贵的','傲娇的','心灵美的','爱撒娇的','细腻的','天真的','怕黑的','感性的','飘逸的','怕孤独的','忐忑的','高挑的','傻傻的','冷艳的','爱听歌的','还单身的','怕孤单的','懵懂的');
        $nicheng_wei=array('嚓茶','凉面','便当','毛豆','花生','可乐','灯泡','哈密瓜','野狼','背包','眼神','缘分','雪碧','人生','牛排','蚂蚁','飞鸟','灰狼','斑马','汉堡','悟空','巨人','绿茶','自行车','保温杯','大碗','墨镜','魔镜','煎饼','月饼','月亮','星星','芝麻','啤酒','玫瑰','大叔','小伙','哈密瓜，数据线','太阳','树叶','芹菜','黄蜂','蜜粉','蜜蜂','信封','西装','外套','裙子','大象','猫咪','母鸡','路灯','蓝天','白云','星月','彩虹','微笑','摩托','板栗','高山','大地','大树','电灯胆','砖头','楼房','水池','鸡翅','蜻蜓','红牛','咖啡','机器猫','枕头','大船','诺言','钢笔','刺猬','天空','飞机','大炮','冬天','洋葱','春天','夏天','秋天','冬日','航空','毛衣','豌豆','黑米','玉米','眼睛','老鼠','白羊','帅哥','美女','季节','鲜花','服饰','裙子','白开水','秀发','大山','火车','汽车','歌曲','舞蹈','老师','导师','方盒','大米','麦片','水杯','水壶','手套','鞋子','自行车','鼠标','手机','电脑','书本','奇迹','身影','香烟','夕阳','台灯','宝贝','未来','皮带','钥匙','心锁','故事','花瓣','滑板','画笔','画板','学姐','店员','电源','饼干','宝马','过客','大白','时光','石头','钻石','河马','犀牛','西牛','绿草','抽屉','柜子','往事','寒风','路人','橘子','耳机','鸵鸟','朋友','苗条','铅笔','钢笔','硬币','热狗','大侠','御姐','萝莉','毛巾','期待','盼望','白昼','黑夜','大门','黑裤','钢铁侠','哑铃','板凳','枫叶','荷花','乌龟','仙人掌','衬衫','大神','草丛','早晨','心情','茉莉','流沙','蜗牛','战斗机','冥王星','猎豹','棒球','篮球','乐曲','电话','网络','世界','中心','鱼','鸡','狗','老虎','鸭子','雨','羽毛','翅膀','外套','火','丝袜','书包','钢笔','冷风','八宝粥','烤鸡','大雁','音响','招牌','胡萝卜','冰棍','帽子','菠萝','蛋挞','香水','泥猴桃','吐司','溪流','黄豆','樱桃','小鸽子','小蝴蝶','爆米花','花卷','小鸭子','小海豚','日记本','小熊猫','小懒猪','小懒虫','荔枝','镜子','曲奇','金针菇','小松鼠','小虾米','酒窝','紫菜','金鱼','柚子','果汁','百褶裙','项链','帆布鞋','火龙果','奇异果','煎蛋','唇彩','小土豆','高跟鞋','戒指','雪糕','睫毛','铃铛','手链','香氛','红酒','月光','酸奶','银耳汤','咖啡豆','小蜜蜂','小蚂蚁','蜡烛','棉花糖','向日葵','水蜜桃','小蝴蝶','小刺猬','小丸子','指甲油','康乃馨','糖豆','薯片','口红','超短裙','乌冬面','冰淇淋','棒棒糖','长颈鹿','豆芽','发箍','发卡','发夹','发带','铃铛','小馒头','小笼包','小甜瓜','冬瓜','香菇','小兔子','含羞草','短靴','睫毛膏','小蘑菇','跳跳糖','小白菜','草莓','柠檬','月饼','百合','纸鹤','小天鹅','云朵','芒果','面包','海燕','小猫咪','龙猫','唇膏','鞋垫','羊','黑猫','白猫','万宝路','金毛','山水','音响');
        $tou_num=rand(0,331);
        $wei_num=rand(0,325);
        $nicheng=$nicheng_tou[$tou_num].$nicheng_wei[$wei_num];
        return $nicheng; //输出生成的昵称

    }



    //图片识别
    public function test(){



//         百万数据测试
//        ini_set('max_execution_time', '0');//设置永不超时，无限执行下去直到结束
//
//        $datas = [];
//        $tel_arr = array(
//            '130','131','132','133','134','135','136','137','138','139','144','147','150','151','152','153','155','156','157','158','159','176','177','178','180','181','182','183','184','185','186','187','188','189',
//        );
//
//        for ($i=50000; $i <1000000 ; $i+=50000) {
//            # code...
//            $j = $i-50000;
//            $data = Db::table('test_user')->where("id>=$j and id<$i ")->select();
//            foreach ($data as $key => $value) {
//                # code...
//                $datas[$key]['user_id'] = mt_rand(999, 9999);
//                $datas[$key]['user_name'] = $this->sys_randname();
//                $datas[$key]['phone'] = $tel_arr[array_rand($tel_arr)] . mt_rand(1000, 9999) . mt_rand(1000, 9999);;
//                $datas[$key]['lan_id'] = mt_rand(999, 9999);
//                $datas[$key]['region_id'] = mt_rand(999, 9999);
//                $datas[$key]['create_time'] = time();
//
//            }
////           print_r($datas);die;
//
//           $arr =  Db::table('test_user')->insertAll($datas);
//
//            print_r($arr);die;
//            unset($datas);//销毁插入的数据数组
//        }




//        $nums = [10000,8231,7777,5212,4999,4102,4000,3200,800,610,522,50,40,30,9,7,5,2,1];
//            $maxSum = $nums[0];
//            $thisSum = $nums[0];
//            for ($i = 1; $i < count($nums); $i++) {
//                $thisSum = $thisSum + $nums[$i] > $nums[$i] ? $thisSum + $nums[$i] : $nums[$i];
//                $maxSum = max($maxSum, $thisSum);
//            }
//            print_r($maxSum) ;die;
//
//        //设置限定值
//        $max = 5000;
//        //订单数组
//        $order = [
//            ['id'=>'7','price' => '500','orderId' => '7'],
//            ['id'=>'8','price' => '200','orderId' => '8'],
//            ['id'=>'1','price' => '210','orderId' => '1',],
//            ['id'=>'2','price' => '200','orderId' => '2'],
//            ['id'=>'3','price' => '240','orderId' => '3'],
//            ['id'=>'4','price' => '250','orderId' => '4'],
//            ['id'=>'5','price' => '500','orderId' => '5'],
//            ['id'=>'6','price' => '200','orderId' => '6'],
//        ];
//        if (count($order) == 0) {
//           print_r('空');
//        }
//        if (count($order) == 1) {
//            print_r($order) ;
//
//        }
//        $arr = array_column($order ,null ,'price');
//        $re = $this->ts($order);




//        print_r($result);die; //打印答案



//    $a = null;
//    $b = array();
//    $c = '0';
//    echo (empty($a) ? 'true' : 'false');die;
//    echo (isset($a) ? 'true' : 'false');die;
//    echo (empty($b) ? 'true' : 'false');die;
//    echo (empty($c) ? 'true' : 'false');die;
//    echo (is_null($b) ? 'true' : 'false');die;

        //        $redisMain = Common::redisMain();
//        $redisMain->del('key1');die;

//        $i = 1;
//        while (true){
//            if($i++ <=5){
//                continue;
//            }else{
//                break;
//            }
//        }
//
//
//        echo $i;die;
//        for($i=1;$i<=1000;$i++){
//            $data[] = $i;
//        }
//
//        $arr = explode('.',count($data)/6 );
//
//        for($i = 1; $i<=$arr[0]; $i++){
//
//            $a = array_slice($data,0,6) ;
//
//            unset($data[0],$data[1],$data[2],$data[3],$data[4],$data[5]);
//
//            $data = array_values($data);
//
//            $num = array_sum($a);
//
//            $data[count($data)+1] = $num;
//
//
//        }
//        print_r($data);die;

// 循环到队列 （模拟事件进入队列）


//        $redisMain = Common::redisMain();
//
//        $len = $redisMain->lLen('maia');
//
//        $a = $len-10;
//
//        for ($i=0;$i<$a;$i++){
//
//            $data = $redisMain->rPop('maia');
//            print_r($data);die;
//            if(!empty($data)){
//                $add_time = time();
//                $dir = ROOT_PATH.'public/analysis_log/'.'analysis/'.date('Y/m/d/',$add_time);
////        print_r($dir);die;
//                is_dir ( $dir ) or  mkdir ( $dir , 0777,true);
//                $name = $dir.date('H',$add_time).'.log';
//                $num = $redisMain->hIncrBy('dian','S',1);
//
//                $header = str_pad($num,10,0,STR_PAD_LEFT );
//
//
////        print_r($data);die;
//
//                file_put_contents($name,$header.$data,8);
////                echo 'nothing' . PHP_EOL; sleep(5);
//            }
////            echo 'nothing' . PHP_EOL; sleep(5);
//
////
//        }
//        while ($a) {
//
//            $data = $redisMain->rPop('maia');
//
//            if(!empty($data)){
//                $add_time = time();
//                $dir = ROOT_PATH.'public/analysis_log/'.'analysis/'.date('Y/m/d/',$add_time);
////        print_r($dir);die;
//                is_dir ( $dir ) or  mkdir ( $dir , 0777,true);
//                $name = $dir.date('H',$add_time).'.log';
//                $num = $redisMain->hIncrBy('dian','S',1);
//
//                $header = str_pad($num,10,0,STR_PAD_LEFT );
//
//
////        print_r($data);die;
//
//                file_put_contents($name,$header.$data,8);
////                echo 'nothing' . PHP_EOL; sleep(5);
//            }
////            echo 'nothing' . PHP_EOL; sleep(5);
////
//        }



//        print_r($len);die;
//        $arr = $redisMain->lPop('dian_j');
//        print_r(json_decode($task));
//        die;


        echo '暂停中';die;

        set_time_limit(0);
        //获取图片列表
        $hostdir = ROOT_PATH.'public/uploads/weixinPhoto/20200709/';
        $filesnames = scandir($hostdir);

//        print_r($filesnames);die;

        foreach($filesnames as $key => $value){
            $kzm = substr(strrchr($value,"."),1);

            if($kzm=="PNG" or $kzm=="jpg" or $kzm=="JPG"  or $kzm=="jpeg") { //文件过滤

                $array[]= '/uploads/weixinPhoto/20200709/'.$value;//把符合条件的文件名存入数组

            }

        }

        if(empty($array)){
            echo '暂时没有数据';die;
        }

//       print_r($array);die;

        // 引入文字识别OCR SDK
        vendor('Ocr.AipOcr');
        $APP_ID = '20449255';
        $API_KEY = 'Ojlx0r8GDbCCIlo9f2bMVGmq';
        $SECRET_KEY = 'LHxjqm5Rd2qmG6KIV0re7sr2IFCmGxON';
        $client  = new \AipOcr($APP_ID, $API_KEY, $SECRET_KEY);

        $redisMain = Common::redisMain();
        $token = unserialize($redisMain->get('access_token'));
        if(empty($token)){
            $token=$this->curl("https://aip.baidubce.com/oauth/2.0/token?grant_type=client_credentials&client_id=$API_KEY&client_secret=$SECRET_KEY");
            $token=json_decode($token,true);
            $token=$token['access_token'];
            $redisMain->set( 'access_token', serialize($token), 86400);

        }
        // 如果有可选参数
        $options = array();
        $options["language_type"] = "CHN_ENG";
        $options["detect_direction"] = "false";
        $options["detect_language"] = "false";
        $options["probability"] = "false";
        $options["access_token"]=$token;            //获取token
        //获取图片列表


        $date = date('Y-m-d',strtotime(date('Y-m-d')));

        $count = count($array);
        $size = ceil($count/100);
        for ($i=1;$i<=$size;$i++) {
            $newarray = array_slice($array,($i-1)*100,100);
            $newData = [];
            foreach ($newarray as $k => $v){

                $img =  file_get_contents(ROOT_PATH.'public'.$v);

                $result= $client->basicGeneral($img, $options);        //调用通用文字识别接口
                $num = $redisMain->incr('ORC::'.$date);

//                $newData = $this->arrayNewOne($result, $v,$num);
                $newData = $this->arrayNew($result, $v,$num);

            }

        }
        $this->redisSet($date);
         if($newData){
             echo "<script>alert('完成')</script>";die;
         }else{
             echo "<script>alert('失败')</script>";die;
         }

    }

    public function redisSet($date){
        $redisMain = Common::redisMain();

        $num = $redisMain->get('ORC::'.$date);

        $redisMain->hSet('ORCQPS', $date, $num );

    }

    public function arrayNewOne($result, $v, $num){

        if($result['words_result']){


            foreach($result['words_result'] as $key => $val){



                if(preg_match("/^1[34578]{1}\d{9}$/",$val['words'])){

                    $arr['iphone'] = $val['words'];
                    $arr['name'] = $val['words'];
                }
                if(preg_match('/(.*?(省|区|市))/',$val['words'])){

                    $arr['address'] = $val['words'];
                }
//                if(strpos($val['words'],'微信号') !== false){
//
//                    $arr['WeChat'] = $val['words'];
//                    $arr['name'] = $val['words'];
//
//                }elseif (strpos($val['words'],'地区') !== false){
//                    $arr['address'] = $val['words'];
//
//                }elseif (strpos($val['words'],'买买买') !== false){
//                    $arr['remarks'] = $val['words'];
//
//                }elseif (strpos($val['words'],'代理') !== false){
//                    $arr['remarks'] = $val['words'];
//
//                }


            }

            $arr['status'] = Config::get('STATE_SHENHE');
            $arr['addtime'] = time();
            $arr['result'] = json_encode($result,JSON_UNESCAPED_UNICODE);
            $arr['photo'] = $v;

            $json = json_encode($arr,JSON_UNESCAPED_UNICODE);

        }else{

            $json = json_encode($result,JSON_UNESCAPED_UNICODE);

        }

        $add_time = time();
        $dir = ROOT_PATH.'public/analysis_log/'.'photo/'.date('Y/m/',$add_time);
        is_dir ( $dir ) or  mkdir ( $dir , 0777,true);
        $name = $dir.date('d',$add_time).'.log';
        $header = str_pad($num,10,0,STR_PAD_LEFT );
        file_put_contents($name,$header.$json,8);

        $WeChatUserMode = new WeChatUserMode();
        $data =  $WeChatUserMode->dataInsert($arr);
        return $data;


    }

    //处理数组  1
    public function arrayNew($result, $v, $num){


        if($result['words_result']){

         foreach($result['words_result'] as $key => $val){


             if(strpos($val['words'],'微信号') !== false){

                 $arr['WeChat'] = $val['words'];
                 $arr['name'] = $val['words'];

             }elseif (strpos($val['words'],'地区') !== false){
                 $arr['address'] = $val['words'];

             }elseif (strpos($val['words'],'买买买') !== false){
                 $arr['remarks'] = $val['words'];

             }elseif (strpos($val['words'],'代理') !== false){
                 $arr['remarks'] = $val['words'];

             }


         }

            $arr['status'] = Config::get('STATE_SHENHE');
            $arr['addtime'] = time();
            $arr['result'] = json_encode($result,JSON_UNESCAPED_UNICODE);
            $arr['photo'] = $v;


            $json = json_encode($arr,JSON_UNESCAPED_UNICODE);

        }else{

            $json = json_encode($result,JSON_UNESCAPED_UNICODE);

        }

        $add_time = time();
        $dir = ROOT_PATH.'public/analysis_log/'.'photo/'.date('Y/m/',$add_time);
        is_dir ( $dir ) or  mkdir ( $dir , 0777,true);
        $name = $dir.date('d',$add_time).'.log';
        $header = str_pad($num,10,0,STR_PAD_LEFT );
        file_put_contents($name,$header.$json,8);

        $WeChatUserMode = new WeChatUserMode();
        $data =  $WeChatUserMode->dataInsert($arr);
        return $data;

    }

    public function curl($url,$postData=[],$headers=[]){
        $ch=curl_init();
        curl_setopt($ch,CURLOPT_URL,$url);      //要访问的地址
        curl_setopt($ch,CURLOPT_HEADER,0);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);     //执行结果是否被返回，0返，1不返
        curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,false);
        curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,false);
        curl_setopt($ch,CURLOPT_HTTPHEADER,$headers);
        if($postData){
            curl_setopt($ch,CURLOPT_TIMEOUT,60);
            curl_setopt($ch,CURLOPT_POST,1);
            curl_setopt($ch,CURLOPT_POSTFIELDS,$postData);
        }
        if(curl_exec($ch)==false){
            $data='';
        }
        else{
            $data=curl_multi_getcontent($ch);
        }
        curl_close($ch);
        return $data;
    }




}
