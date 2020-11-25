<?php
namespace app\home\controller;

use think\Config;
use \think\db;
use app\home\model\Goods as GoodsModel;

use app\home\model\Product as ProductMode;
use app\home\model\Shop as ShopMode;




/**
 * 购物车控制器
 * @author duxinxin
 * @date 2020/04/26
 */

class Shop extends Common
{


    const NUM = 5;

    /**
     * 加入购物车
     *
     * @access shopAdd
     * @author duxinxin
     * @date 2020/04/26
     */
    public function shopAdd(){

        $id = $this->request->get('id');

        $ProductMode = new ProductMode();
        $ShopMode = new ShopMode();

        $postData =  $ProductMode->productFind(['id' => $id]);
        $shopFind =  $ShopMode->shopFind(['name' => $postData['product_name'],'state' => Config::get('STATE_YES')]);

        if(!empty($shopFind)){

            $where =  ['num' =>  Db::raw('num+1')];
            $data =  $ShopMode->edit($shopFind['id'],$where);

        }else{

            $Data = [

                'name' => $postData['product_name'],

                'purchase'    => $postData['purchase'],

                'new_purchase'    => $postData['new_purchase'],

                'price'    => $postData['price'],

                'new_price'    => $postData['new_price'],

                'class_id'   => $postData['class_id'],

                'state'    => Config::get('STATE_YES'),

                'creat_time'  => time(),

            ];

            $data =  $ShopMode->insertAdd($Data);
        }

        if($data) {
            return json("加入成功！");
        }else{
            return json("加入失败！");
        }

    }

    /**
     * 修改购物车数据
     *
     * @access update
     * @author duxinxin
     * @date 2020/04/26
     */

    public function update(){

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
                $where = ['num' => $purchase];
            }

        }else{

            $id = $this->request->get('id');
            $state = $this->request->get('status');
            $where = ['state' => $state];

        }
        $ShopMode = new ShopMode();
        $data =  $ShopMode->edit($id,$where);

        if($data) {
            return json("删除成功！");
        }else{
            return json("操作失败！");
        }

    }



    /**
     * 购物车数据展示
     *
     * @access shop
     * @author duxinxin
     * @date 2020/04/26
     */
    public function  shop(){

        $ShopMode = new ShopMode();
        $GoodsModel = new GoodsModel();

        $data =  $ShopMode->show();
        $classData =  $GoodsModel->classList(Config::get('CLASS_SHOP'));

        $class = array_combine(array_column($classData, 'id'), $classData);

        foreach($data as $key => $val){

                if($val['num'] < shop::NUM){

                        if($val['purchase'] == 0){

                            $data[$key]['total_purchase'] = '采购单价暂无';
                            $data[$key]['new_total_purchase'] = '';
                        }else{

                            $data[$key]['total_purchase'] = '<span>按采购单价计算：</span><br>'.$val['purchase']. ' * ' .$val['num'].' = '.$val['purchase'] * $val['num'];
                            $data[$key]['new_total_purchase'] = $val['purchase'] * $val['num'];
                        }

                        if($val['price'] == 0){

                            $data[$key]['total_price'] = '销售单价暂无';
                            $data[$key]['new_total_price'] = '';

                        }else{

                            $data[$key]['total_price'] = '<span>按售价计算：</span><br>'.  $val['price'] .' * '. $val['num'].' = '.$val['price'] * $val['num'];
                            $data[$key]['new_total_price'] = $val['price'] * $val['num'];

                        }
                }else{
                        if($val['new_purchase'] == 0){

                            $data[$key]['total_purchase'] = '采购批发价暂无';
                            $data[$key]['new_total_purchase'] = '';

                        }else{

                            $data[$key]['total_purchase'] = '<span>按采购批发价计算：</span><br>'. $val['new_purchase'].' * '.$val['num'] . ' = '.$val['new_purchase'] * $val['num'];
                            $data[$key]['new_total_purchase'] = $val['new_purchase'] * $val['num'];


                        }

                        if($val['new_price'] == 0){

                            $data[$key]['total_price'] = '销售批发价暂无';
                            $data[$key]['new_total_price'] = '';

                        }else{

                            $data[$key]['total_price'] = '<span>按销售批发价计算：</span><br>'. $val['new_price'].' * '. $val['num'] .' = '.  $val['new_price'] * $val['num'];
                            $data[$key]['new_total_price'] = $val['new_price'] * $val['num'];


                        }
                }

        }


        //计算采购总计 销售总计 利润总计
        $total_purchase = 0;
        $total_price = 0;

        foreach($data as $k => $v){

                $data[$k]['profit'] = (int)$v['new_total_price'] - (int)$v['new_total_purchase'];
                $total_purchase +=  (int)$v['new_total_purchase'];
                $total_price += (int) $v['new_total_price'];

        }

        $this ->assign(
            [
                'total_purchase' => $total_purchase,

                'new_total_price' => $total_price,

                'classData'  => $class,

                'data' => $data,

            ]
        );
        return $this->fetch();

    }










}
