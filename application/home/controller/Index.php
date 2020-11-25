<?php
namespace app\home\controller;

use \think\db;
use think\Config;
use app\home\model\Goods as GoodsModel;
use app\home\controller\Helps\imgHelper as imgHelper;
use app\home\model\Invoice as InvoiceModel;
use app\home\model\Pay as PayModel;


/**
 * 物品管理控制器
 * @author duxinxin
 * @date 2020/04/26
 */

class Index extends Common
{
    protected $hashFunction;

    /**
     * 主页
     *
     * @access index
     * @author duxinxin
     * @date 2020/04/26
     */
    public function index()
    {
        $imgHelper = new imgHelper();
//        //添加pv
        $imgHelper->pvConfig('pv::index', '主页');
        $redisMain = Common::redisMain();
        $data = unserialize($redisMain->get('home::index'));

        if(empty($data)){
            $GoodsModel = new GoodsModel();
            $goodNum = $GoodsModel->counts();
            $goodPrice = $GoodsModel->goodsPrice();

            $InvoiceModel = new InvoiceModel();
            $invoiceNum = $InvoiceModel->counts();
            $invoicePrice = $InvoiceModel->invoicePrice();

            $PayModel = new PayModel();
            $payShou = $PayModel->payPrice(Config::get('PAY_SHOU'));
            $payShouNum = $PayModel->counts(Config::get('PAY_SHOU'));


            $payZhi = $PayModel->payPrice(Config::get('PAY_ZHI'));
            $payZhiNum = $PayModel->counts(Config::get('PAY_ZHI'));



            //柱状图1
            $Histogram = $imgHelper->Histogram($goodPrice, $invoicePrice, $payShou, $payZhi);

            //饼状图
            $HistogramOne = $imgHelper->HistogramOne($goodPrice, $invoicePrice, $payShou, $payZhi);

            //柱状图2
            $HistogramTwo = $imgHelper->HistogramTwo($goodPrice, $invoicePrice, $payShou, $payZhi);

            //收支支付方式金额条形图
            $HistogramThree = $imgHelper->HistogramThree();

            //折线图
            $HistogramFour = $imgHelper->HistogramFour();


            $data = [
                'goodNum'       => $goodNum,
                'goodPrice'  => $goodPrice,
                'invoiceNum'     => $invoiceNum,
                'invoicePrice'   => $invoicePrice,
                'payShou'     => $payShou,
                'payZhi'   => $payZhi,
                'payShouNum'   => $payShouNum,
                'payZhiNum'   => $payZhiNum,
                //柱状图1
                'Histogram'   => json_encode($Histogram['Histogram']),
                'HistogramGroup'   => json_encode($Histogram['HistogramGroup']),
                //柱状图2
                'Histogram_Two'   => json_encode($HistogramTwo['HistogramTwo']),
                'HistogramGroup_Two'   => json_encode($HistogramTwo['HistogramGroupTwo']),
                //饼状图
                'HistogramOne'   => json_encode($HistogramOne['newGoodsGroup']),
                'HistogramTwo'   => json_encode($HistogramOne['newInvoiceGroup']),
                'HistogramThree'   => json_encode($HistogramOne['newShouGroup']),
                'HistogramFour'   => json_encode($HistogramOne['newZhiGroup']),
                //条形图
                'HistogramPay'   => json_encode($HistogramThree),

                'HistogramFourZhi' => json_encode($HistogramFour['zhi']),
                'HistogramFourShou' => json_encode($HistogramFour['shou']),
            ];

            $redisMain->set( 'home::index', serialize($data), 3600);

            //无缓存 把记录刷新到库
            $getPv = $imgHelper->getPvAll();
            $getPv['addtime'] = time();
            Db::table('pv')->insert($getPv);

        }

        //获取所有页面pv
        $data['getPv'] = $imgHelper->getPvAll();

        $this ->assign($data);
        return $this->fetch();
    }


    /**
     * 物品列表
     *
     * @access goods
     * @author duxinxin
     * @date 2020/04/26
     */
    public function goods(){
        $imgHelper = new imgHelper();
        $imgHelper->pvConfig('pv::goodList', '物品列表');
        $condition = $imgHelper->productBuild($this->request->get(), Config::get('STATE_YES'));
        $GoodsModel = new GoodsModel();
        $data =  $GoodsModel->show($condition);
        $classData =  $GoodsModel->classList(Config::get('CLASS_GOOD'));
        $classData = array_combine(array_column($classData, 'id'), $classData);
        $this ->assign(
            [
                'list'       => $data,
                'classData'  => $classData,
                'search'     => $condition['where'],
                'username'   => $condition['username'],
                ]

        );
        return $this->fetch();
    }

    /**
     * 物品添加
     *
     * @access add
     * @author duxinxin
     * @date 2020/04/26
     */
    public function add(){

        if ($this->request->isPost()){

            $postData = $this->request->post();
            $file = request()->file('image');

            if($file){
                $url = 'uploads/goodsPhoto';
                $imgHelper = new imgHelper();
                $postData['photo'] = $imgHelper->getPhotoUrl($file, $url);
            }
            $Data = [

                'username' => $postData['username'],

                'class' => $postData['class'],

                'onlyid' => date('YmdHis').substr(microtime(), 2, 5) . mt_rand(10000,99999),

                'price' => $postData['price'],

                'photo' => !empty($postData['photo'])? $postData['photo']:'',

                'meeting' => strtotime($postData['meeting']),

                'remark' => $postData['remark'],

                'addtime' => time(),

            ];

            $GoodsModel = new GoodsModel();
            $data =  $GoodsModel->insertAdd($Data);

               if($data) {
                   return $this->success('保存成功', '');
               }else{
                   return $this->error('保存失败', '');
               }

        }else{
            $imgHelper = new imgHelper();
            //添加pv
            $imgHelper->pvConfig('pv::goodsAdd', '物品添加页面');
            $GoodsModel = new GoodsModel();
            $classData =  $GoodsModel->classList(Config::get('CLASS_GOOD'));
            $this ->assign([
                    'classData'  => $classData
                ]);
            return $this->fetch();
        }
    }

    /**
     * 物品删除操作
     *
     * @access del
     * @author duxinxin
     * @date 2020/04/26
     */
    public function del(){

        $id = $this->request->get('id');
        $GoodsModel = new GoodsModel();
        $where = [
            'state' => Config::get('STATE_NO')
        ];
        $data = $GoodsModel->edit($id, $where);

        if($data) {
            return $this->success('删除成功', 'home/index/goods');
        }else{
            return $this->error('删除失败', 'home/index/goods');
        }
    }

    /**
     * 物品恢复操作
     *
     * @access recover
     * @author duxinxin
     * @date 2020/04/26
     */
    public function recover(){

        $id = $this->request->get('id');
        $GoodsModel = new GoodsModel();
        $where = ['state' => Config::get('STATE_YES')];
        $data =  $GoodsModel->edit($id, $where);

        if($data) {
            return $this->success('恢复成功', 'home/index/goodsbin');
        }else{
            return $this->error('恢复失败', 'home/index/goodsbin');
        }
    }

    /**
     * 物品编辑操作
     *
     * @access edit
     * @author duxinxin
     * @date 2020/04/26
     */
    public function edit(){

        if ($this->request->isPost()){

            $id = $this->request->get('id');
            $postData = $this->request->post();
            $file = request()->file('image');

            if($file){
                $url = 'uploads/goodsPhoto';
                $imgHelper = new imgHelper();
                $photo = $imgHelper->getPhotoUrl($file, $url);
                $Data = [
                    'username' => $postData['username'],

                    'class'    => $postData['class'],

                    'price'    => $postData['price'],

                    'photo'    => $photo,

                    'meeting'  => strtotime($postData['meeting']),

                    'remark'   => $postData['remark'],

                    'endtime'  => time(),

                ];

            }else{

                $Data = [
                    'username'  => $postData['username'],

                    'class'     => $postData['class'],

                    'price'     => $postData['price'],

                    'photo'     => !empty($postData['photo'])? $postData['photo']:'',

                    'meeting'   => strtotime($postData['meeting']),

                    'remark'     => $postData['remark'],

                    'endtime'    => time(),

                ];
            }

            $GoodsModel = new GoodsModel();
            $data =  $GoodsModel->edit($id, $Data);

            if($data) {
                return $this->success('修改成功', '');
            }else{
                return $this->error('修改失败', '');
            }

        }else{

            $id = $this->request->get('id');
            $GoodsModel = new GoodsModel();
            $classData =  $GoodsModel->classList(Config::get('CLASS_GOOD'));
            $data =  $GoodsModel->goodFind(['id' => $id]);
            //添加pv
            $imgHelper = new imgHelper();
            $imgHelper->pvConfig('pv::goodsEdit', '物品编辑页面');

            $this ->assign([
                'classData'  =>  $classData = array_combine(array_column($classData, 'id'), $classData),
                'data'       => $data
            ]);
            return $this->fetch();
        }

    }


    /**
     * 物品回收站列表
     *
     * @access goodsBin
     * @author duxinxin
     * @date 2020/04/26
     */
    public function goodsBin(){

        $imgHelper = new imgHelper();
        //添加pv
        $imgHelper->pvConfig('pv::goodsBin', '物品回收站页面');
        $condition = $imgHelper->productBuild($this->request->get(), Config::get('STATE_NO'));

        $GoodsModel = new GoodsModel();
        $data =  $GoodsModel->show($condition);
        $classData =  $GoodsModel->classList(Config::get('CLASS_GOOD'));
        $classData = array_combine(array_column($classData, 'id'), $classData);

        $this ->assign(
            [
                'list'       => $data,
                'classData'  => $classData,
                'search'     => $condition['where'],
                'username'   => $condition['username'],
            ]
        );
        return $this->fetch();
    }

    /**
     * 物品图片列表
     *
     * @access photo
     * @author duxinxin
     * @date 2020/04/26
     */
    public function photo(){

        $imgHelper = new imgHelper();
        $imgHelper->pvConfig('pv::goodsPhoto', '物品图片页面');
        $condition = $imgHelper->productBuild($this->request->get(), Config::get('STATE_YES'));

        $GoodsModel = new GoodsModel();
        $data =  $GoodsModel->show($condition);
        $classData =  $GoodsModel->classList(Config::get('CLASS_GOOD'));
        $classData = array_combine(array_column($classData, 'id'), $classData);

        $this ->assign(
            [
                'list'       => $data,
                'classData'  => $classData,
                'search'     => $condition['where'],
                'username'   => $condition['username'],
            ]
        );
        return $this->fetch();
    }



}
