<?php
namespace app\home\controller\Helps;

use think\Config;
use app\home\model\Goods as GoodsModel;
use app\home\model\Invoice as InvoiceModel;
use app\home\model\Pay as PayModel;
use app\home\controller\Common;


class imgHelper
{


    /**
     *  pv统计公共方法
     *
     * @method pv
     */
    public function pvConfig($key,$msg = ''){

        $redisMain = new Common();
        $redisMain = $redisMain->redisMain();   //连接redis
        $redisMain->zadd($key, 1, time() );
    }


    /**
     *  获取 pv统计公共方法
     *
     * @method pv
     */
    public function getPv($key){

        $redisVice = new Common();
        $redisVice = $redisVice->redisVice();   //连接从redis
        return $redisVice->zCard($key);

    }

    /**
     *  主页获取页面pv公共方法
     *
     * @method pv
     */
    public function getPvAll(){

        return [
            'user' => $this->getPv('pv::user'),
            'index' => $this->getPv('pv::index'),
            'goodList' => $this->getPv('pv::goodList'),
            'goodsAdd' => $this->getPv('pv::goodsAdd'),
            'goodsEdit' => $this->getPv('pv::goodsEdit'),
            'goodsBin' => $this->getPv('pv::goodsBin'),
            'goodsPhoto' => $this->getPv('pv::goodsPhoto'),
            'payList' => $this->getPv('pv::payList'),
            'payAdd' => $this->getPv('pv::payAdd'),
            'payEdit' => $this->getPv('pv::payEdit'),
            'payBin' => $this->getPv('pv::payBin'),
            'invoiceList' => $this->getPv('pv::invoiceList'),
            'invoiceAdd' => $this->getPv('pv::invoiceAdd'),
            'invoiceEdit' => $this->getPv('pv::invoiceEdit'),
            'invoiceBin' => $this->getPv('pv::invoiceBin'),
            'invoicePhoto' => $this->getPv('pv::invoicePhoto'),
            'cateList' => $this->getPv('pv::cateList'),
            'cateAdd' => $this->getPv('pv::cateAdd'),
            'cateEdit' => $this->getPv('pv::cateEdit'),
            'cateBin' => $this->getPv('pv::cateBin'),
        ];

    }


    /**
     *  公共图片上传方法
     *
     * @method imgHelper
     */
    public function getPhotoUrl($file, $url)
    {
        $info = $file->validate(['ext'=>"jpg,jpeg,png,gif"])->move(ROOT_PATH . 'public' . DS . $url);

        if($info){
            $vphotograph = $info->getSaveName();
            //压缩
            $image = \think\Image::open($info);
            $image -> thumb(800,800)->save($url.'/'.$vphotograph);
        }
        // 拼接url传递到数据库
        $photo = DS . $url . DS . $info->getSaveName();
        return $photo;
    }

    /**
     *  公共返回模块方法 (分类列表搜索用)
     *
     *  应该分类模块
     * @method getParam
     */

    public function getParam(){

       return $classData =  [

            Config::get('CLASS_GOOD') => [
                'id' => Config::get('CLASS_GOOD'),
                'name' => '物品模块'
            ],

            Config::get('CLASS_SMALL') => [
                'id' => Config::get('CLASS_SMALL'),
                'name' => '小票模块'
            ],

            Config::get('CLASS_MONEY') => [
                'id' => Config::get('CLASS_MONEY'),
                'name' => '财务模块'
            ],
           Config::get('CLASS_SHOP') => [
                'id' => Config::get('CLASS_SHOP'),
                'name' => '代购产品品牌'
            ],

        ];
    }

    /**
     *  公共收支参数方法 (收支管理列表搜索用)
     *
     *  应该收支模块
     * @method getPay
     */

    public function getPay(){

        return $payData =  [

            Config::get('PAY_ZHI') => [
                'id' => Config::get('PAY_ZHI'),
                'pay_name' => '支出'
            ],

            Config::get('PAY_SHOU') => [
                'id' => Config::get('PAY_SHOU'),
                'pay_name' => '收入'
            ],


        ];
    }

    /**
     *  公共收支渠道方法 (收支管理列表搜索用)
     *
     *  应该收支模块
     * @method getMode
     */

    public function getMode(){

        return $getMode =  [

            Config::get('WECHAT') => [
                'id' => Config::get('WECHAT'),
                'pay_mode' => '微信'
            ],

            Config::get('PAY') => [
                'id' => Config::get('PAY'),
                'pay_mode' => '支付宝'
            ],

            Config::get('CARD') => [
                'id' => Config::get('CARD'),
                'pay_mode' => '信用卡'
            ],

        ];
    }


    /**
     *  搜索查询构建器 （所以搜索用）
     *
     *
     * @method getMode
     */
    public static function productBuild($params, $state)
    {

        $condition = [];
        if (!empty($params['username'])) {
            $username = trim($params['username']);
        }
        if (!empty($params['classname'])) {
            $classname = trim($params['classname']);
        }
        if (!empty($params['diff'])) {
            $condition['diff'] = trim($params['diff']);
        }
        if (!empty($params['cate'])) {
            $condition['pay_cate'] = trim($params['cate']);
        }
        if (!empty($params['mode'])) {
            $condition['pay_mode'] = trim($params['mode']);
        }
        if (!empty($params['class'])) {
            $condition['class'] = trim($params['class']);
        }
        if (!empty($params['addtime']) and !empty($params['endtime'])) {
            $addtime = strtotime($params['addtime']);
            $endtime = strtotime($params['endtime']);
            $condition['addtime'] = $addtime;
            $condition['endtime'] = $endtime;
        }

        $condition['state'] = $state;


        return $params =  [
            'where'    => $condition,
            'classname'=>  !empty($classname)? $classname:'',
            'username' => !empty($username)? $username:'',
        ];
    }


    /**
     *  主页 （柱状图用）
     *
     *  应该主页统计模块
     * @method Histogram
     */

    public function Histogram($goodPrice, $invoicePrice, $payShou, $payZhi){

        $total = $goodPrice + $invoicePrice + $payShou + $payZhi;

        $list = [
            ['name' => '物品模块','y' => round($goodPrice/$total*100),'drilldown' => '物品模块'],
            ['name' => '小票模块','y' => round($invoicePrice/$total*100),'drilldown' => '小票模块'],
            ['name' => '收入模块','y' => round($payShou/$total*100),'drilldown' => '收入模块'],
            ['name' => '支出模块','y' => round($payZhi/$total*100),'drilldown' => '支出模块'],
        ];

        //物品处理
        $GoodsModel = new GoodsModel();
        //物品以分类分组查询
        $goodsPriceGroup = $GoodsModel->goodsPriceGroup();
        //合并分类名称
        $newGoodsGroup = $this->loop($goodsPriceGroup, $goodPrice, Config::get('CLASS_GOOD'));

        //小票处理
        $InvoiceModel = new InvoiceModel();
        //小票以分类分组查询
        $invoicePriceGroup =  $InvoiceModel->invoicePriceGroup();
        //合并分类名称
        $newInvoiceGroup = $this->loop($invoicePriceGroup, $invoicePrice, Config::get('CLASS_SMALL'));

        //收入处理
        $PayModell = new PayModel();
        //收入以分类分组查询
        $shouPriceGroup =  $PayModell->payPriceGroup(Config::get('PAY_SHOU'));
        //合并分类名称
        $newShouGroup = $this->loop($shouPriceGroup, $payShou, Config::get('CLASS_MONEY'));

        //支出处理
        //支出以分类分组查询
        $zhiPriceGroup =  $PayModell->payPriceGroup(Config::get('PAY_ZHI'));
        //合并分类名称
        $newZhiGroup = $this->loop($zhiPriceGroup, $payZhi, Config::get('CLASS_MONEY'));

        $data = [
            [
                'name' => '物品模块',
                'id'   => '物品模块',
                'data' => $newGoodsGroup
            ],
            [
                'name' => '小票模块',
                'id'   => '小票模块',
                'data' => $newInvoiceGroup
            ],
            [
                'name' => '收入模块',
                'id'   => '收入模块',
                'data' => $newShouGroup
            ],
            [
                'name' => '支出模块',
                'id'   => '支出模块',
                'data' => $newZhiGroup
            ],

         ];

        return $newDate = [
            'Histogram'      => $list,
            'HistogramGroup' => $data
        ];
    }

    /**
     *  处理柱状图方法（Histogram）
     *
     *
     * @method loop
     */
    public   function loop($data, $price, $params){

        //分类
        $GoodsModel = new GoodsModel();
        $classData =  $GoodsModel->classList($params);
        $classData = array_combine(array_column($classData, 'id'), $classData);

        $newData = [];

        foreach ($data as $key => $value){

            $newData[] = [
                'name' => $classData[$value['class']]['classname'],
                'y'=> round($value['price']/$price*100)

            ];

       }
       //根据比例排序
        foreach ($newData as $key => $row) {
            $volume[]  = $row['y'];
        }
        array_multisort($volume, SORT_DESC, $newData);
        return $newData;
    }


    /**
     *  饼状图（HistogramOne）
     *
     * 公共方法
     * @method HistogramOne
     */
    public   function HistogramOne($goodPrice, $invoicePrice, $payShou, $payZhi){
        //物品处理
        $GoodsModel = new GoodsModel();
        //物品以分类分组查询
        $goodsPriceGroup = $GoodsModel->goodsPriceGroup();
        //合并分类名称
        $newGoodsGroup = $this->loop($goodsPriceGroup, $goodPrice, Config::get('CLASS_GOOD'));

        //小票处理
        $InvoiceModel = new InvoiceModel();
        //小票以分类分组查询
        $invoicePriceGroup =  $InvoiceModel->invoicePriceGroup();
        //合并分类名称
        $newInvoiceGroup = $this->loop($invoicePriceGroup, $invoicePrice, Config::get('CLASS_SMALL'));

        //收入处理
        $PayModell = new PayModel();
        //收入以分类分组查询
        $shouPriceGroup =  $PayModell->payPriceGroup(Config::get('PAY_SHOU'));
        //合并分类名称
        $newShouGroup = $this->loop($shouPriceGroup, $payShou, Config::get('CLASS_MONEY'));

        //支出处理
        //支出以分类分组查询
        $zhiPriceGroup =  $PayModell->payPriceGroup(Config::get('PAY_ZHI'));
        //合并分类名称
        $newZhiGroup = $this->loop($zhiPriceGroup, $payZhi, Config::get('CLASS_MONEY'));

        return $arr = [
            'newGoodsGroup' => $newGoodsGroup,
            'newInvoiceGroup' => $newInvoiceGroup,
            'newShouGroup' => $newShouGroup,
            'newZhiGroup' => $newZhiGroup,
        ];
    }

    /**
     *  柱状图2（HistogramTwo）
     *
     * 公共方法
     * @method HistogramTwo
     */
    public   function HistogramTwo($goodPrice, $invoicePrice, $payShou, $payZhi){


        $list = [
            ['name' => '物品模块','y' => $goodPrice,'drilldown' => '物品模块'],
            ['name' => '小票模块','y' => $invoicePrice,'drilldown' => '小票模块'],
            ['name' => '收入模块','y' => $payShou,'drilldown' => '收入模块'],
            ['name' => '支出模块','y' => $payZhi,'drilldown' => '支出模块'],
        ];

        //物品处理
        $GoodsModel = new GoodsModel();
        //物品以分类分组查询
        $goodsPriceGroup = $GoodsModel->goodsPriceGroup();
        //合并分类名称
        $newGoodsGroup = $this->loopTwo($goodsPriceGroup, Config::get('CLASS_GOOD'));

        //小票处理
        $InvoiceModel = new InvoiceModel();
        //小票以分类分组查询
        $invoicePriceGroup =  $InvoiceModel->invoicePriceGroup();
        //合并分类名称
        $newInvoiceGroup = $this->loopTwo($invoicePriceGroup, Config::get('CLASS_SMALL'));

        //收入处理
        $PayModell = new PayModel();
        //收入以分类分组查询
        $shouPriceGroup =  $PayModell->payPriceGroup(Config::get('PAY_SHOU'));
        //合并分类名称
        $newShouGroup = $this->loopTwo($shouPriceGroup, Config::get('CLASS_MONEY'));

        //支出处理
        //支出以分类分组查询
        $zhiPriceGroup =  $PayModell->payPriceGroup(Config::get('PAY_ZHI'));
        //合并分类名称
        $newZhiGroup = $this->loopTwo($zhiPriceGroup, Config::get('CLASS_MONEY'));

        $data = [
            [
                'name' => '物品模块',
                'id'   => '物品模块',
                'data' => $newGoodsGroup
            ],
            [
                'name' => '小票模块',
                'id'   => '小票模块',
                'data' => $newInvoiceGroup
            ],
            [
                'name' => '收入模块',
                'id'   => '收入模块',
                'data' => $newShouGroup
            ],
            [
                'name' => '支出模块',
                'id'   => '支出模块',
                'data' => $newZhiGroup
            ],

        ];

        return $newDate = [
            'HistogramTwo'      => $list,
            'HistogramGroupTwo' => $data
        ];
    }

    /**
     *  处理柱状图2方法（HistogramTwo）
     *
     *
     * @method loop
     */
    public   function loopTwo($data, $params){

        //分类
        $GoodsModel = new GoodsModel();
        $classData =  $GoodsModel->classList($params);
        $classData = array_combine(array_column($classData, 'id'), $classData);

        $newData = [];

        foreach ($data as $key => $value){

            $newData[] = [
                'name' => $classData[$value['class']]['classname'],
                'y'=> (int)$value['price']

            ];

        }
        //根据比例排序
        foreach ($newData as $key => $row) {
            $volume[]  = $row['y'];
        }
        array_multisort($volume, SORT_DESC, $newData);
        return $newData;
    }


    /**
     *  条形图（HistogramThree）
     *
     *  公共方法
     * @method HistogramThree
     */
    public   function HistogramThree(){

        //收入处理
        $PayModell = new PayModel();
        //微信
        $shouPriceW =  $PayModell->payPriceCate(Config::get('PAY_SHOU'), Config::get('WECHAT'));
        //支付宝
        $shouPriceP =  $PayModell->payPriceCate(Config::get('PAY_SHOU'), Config::get('PAY'));
        //信用卡
        $shouPriceC =  $PayModell->payPriceCate(Config::get('PAY_SHOU'), Config::get('CARD'));

        //支出处理
        //微信
        $zhiPriceW =  $PayModell->payPriceCate(Config::get('PAY_ZHI'), Config::get('WECHAT'));
        //支付宝
        $zhiPriceP =  $PayModell->payPriceCate(Config::get('PAY_ZHI'), Config::get('PAY'));
        //信用卡
        $zhiPriceC =  $PayModell->payPriceCate(Config::get('PAY_ZHI'), Config::get('CARD'));

        return  $data = [
            [
                'name' => '微信',
                'data' => [$shouPriceW,$zhiPriceW]
            ],
            [
                'name' => '支付宝',
                'data' => [$shouPriceP,$zhiPriceP]
            ],
            [
                'name' => '信用卡',
                'data' => [$shouPriceC,$zhiPriceC]
            ],
        ];

    }

    /**
     *  折线图（HistogramFour）
     *
     *  公共方法
     * @method HistogramFour
     */
    public function HistogramFour(){
      // 获取当前年份
      $nian = date("Y");
      $PayModell = new PayModel();

      //支出数据集合
      $zhi = [];
      for($i = 1; $i<=12;$i++){
          $arr =  $this->getShiJianChuo($nian,$i);
          $payNum = $PayModell->payNum($arr,Config::get('PAY_ZHI'));
          $zhi[] = $payNum;
      }

      //收入数据集合
      $shou = [];
      for($i = 1; $i<=12;$i++){
            $arr =  $this->getShiJianChuo($nian,$i);
            $payNum = $PayModell->payNum($arr,Config::get('PAY_SHOU'));
            $shou[] = $payNum;
        }
        return  $data = [
            'zhi' => $zhi,
            'shou' => $shou,
        ];

    }

    //返回当年 各月 起始日期
    public function getShiJianChuo($nian=0,$yue=0){

        if(empty($nian) || empty($yue)){
            $now = time();
            $nian = date("Y",$now);
            $yue =  date("m",$now);
        }
        $time['begin'] = mktime(0,0,0,$yue,1,$nian);
        $time['end'] = mktime(23,59,59,($yue+1),0,$nian);
        return $time;
    }



}
