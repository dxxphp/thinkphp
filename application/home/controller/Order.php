<?php
namespace app\home\controller;

use think\Config;
use \think\db;
use app\home\model\Goods as GoodsModel;

use app\home\model\Product as ProductMode;
use app\home\model\Order as OrderMode;
use app\home\model\Shop as ShopMode;

/**
 * 订单控制器
 * @author duxinxin
 * @date 2020/04/26
 */

class Order extends Common
{

    /**
     * 生成订单
     * @author orderAdd
     * @date 2020/04/26
     */
    public function orderAdd(){

        $supshop = $this->request->post('supshop');
        $iphone = $this->request->post('iphone');
        $username = $this->request->post('username');
        $user_iphone = $this->request->post('user_iphone');
        $address = $this->request->post('address');
        $total_purchase = $this->request->post('total_purchase');
        $new_total_price = $this->request->post('new_total_price');
        $profit = $this->request->post('profit');
        $array = $this->request->post('array');
        $arr = substr_replace($array ,"",-1);

        $Data = [

            'supshop' => $supshop,

            'iphone'    => (int)$iphone,

            'username' => $username,

            'user_iphone' => (int)$user_iphone,

            'address' => $address,

            'shop_id' => $arr,

            'total_purchase'    => $total_purchase,

            'new_total_price'    => $new_total_price,

            'profit'    => $profit,

            'state'    => Config::get('STATE_YES'),

            'creat_time'  => time(),

        ];

        $OrderMode = new OrderMode();
        $data =  $OrderMode->insertAdd($Data);

        if($data) {

            $res = explode(',',$arr);
            $ShopMode = new ShopMode();
            foreach($res as $key => $val){

                $ShopMode->edit($val, ['state' => Config::get('STATE_SIGN')]);

            }

            return json("生成成功！");
        }else{
            return json("操作失败！");
        }


    }

    /**
     * 订单删除操作
     *
     * @access del
     * @author duxinxin
     * @date 2020/04/26
     */
    public function del(){

        $id = $this->request->get('id');
        $OrderMode = new OrderMode();

        $where = [
            'state' => Config::get('STATE_NO')
        ];
        $data =  $OrderMode->edit($id, $where);

        if($data) {
            return json("删除成功！");
        }else{
            return json("操作失败！");
        }
    }

    /**
     * 订单基本信息修改操作
     *
     * @access del
     * @author duxinxin
     * @date 2020/04/26
     */
    public function update(){

        $id = $this->request->post('id');
        $purchase = $this->request->post('purchase');
        $flas = $this->request->post('flas');


        if($flas == 1){
            $where = ['username' => $purchase];
        } if($flas == 2){
            $where = ['user_iphone' => $purchase];
        } if($flas == 3){
            $where = ['address' => $purchase];
        } if($flas == 4){
            $where = ['supshop' => $purchase];
        }if($flas == 5){
            $where = ['iphone' => $purchase];
        }if($flas == 6){
            $where = ['ems' => $purchase];
        }

        $OrderMode = new OrderMode();
        $data =  $OrderMode->edit($id,$where);
        if($data) {
            return json("修改成功！");
        }else{
            return json("操作失败！");
        }
    }

    /**
     * 订单展示
     * @author orderAdd
     * @date 2020/04/26
     */
     public function show(){


         $OrderMode = new OrderMode();


         $condition = $this->productBuild($this->request->get(), Config::get('STATE_YES'));

         $data =  $OrderMode->show($condition);

         $this ->assign(
             [
                 'list'       => $data,
                 'search'     => $condition['where'],
                 'supshop'   => $condition['supshop'],
             ]
         );
         return $this->fetch();
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
        if (!empty($params['supshop'])) {
            $supshop = trim($params['supshop']);
        }
        if (!empty($params['iphone'])) {
            $condition['iphone'] = trim($params['iphone']);
        }


        $condition['state'] = $state;

        return $params =  [
            'where'    => $condition,
            'supshop'=>  !empty($supshop)? $supshop:'',
        ];
    }


    /**
     *  订单查看
     *
     *
     * @method getMode
     */

    public function edit(){
        $id = $this->request->get('id');

        $GoodsModel = new GoodsModel();

        $classData =  $GoodsModel->classList(Config::get('CLASS_SHOP'));
        $class = array_combine(array_column($classData, 'id'), $classData);

        $OrderMode = new OrderMode();
        $data =  $OrderMode->find(['id' => $id,'state' => Config::get('STATE_YES')]);

        if($data){

            $ShopMode = new ShopMode();
            $condition['id']= ['in',$data['shop_id']];
            $condition['state'] =  Config::get('STATE_SIGN');

            $shop =  $ShopMode->selectFind($condition);


        }
//        print_r($shop);die;
        $this ->assign(
            [
                'data'       => $data,
                'shop'     => $shop,
                'classData'   => $class
            ]
        );
        return $this->fetch();
    }

}
