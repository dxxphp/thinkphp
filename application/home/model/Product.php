<?php
namespace app\home\model;

use think\Model;
use \think\db;
use \think\config;


/**
 * 产品管理model
 * @author duxinxin
 * @date 2020/04/26
 */

class Product extends Model
{
    //自定义初始化
    protected function initialize()
    {
        //需要调用`Model`的`initialize`方法
        parent::initialize();

     }

    /**
     * 产品添加操作
     *
     * @access insertAdd
     * @author duxinxin
     * @date 2020/04/26
     */
    public function  insertAdd($data){

        return Db::table('WC_product')->insert($data);

    }

    /**
     * 查询一条产品数据
     *
     * @access productFind
     * @author duxinxin
     * @date 2020/04/26
     */
     public function productFind($condition){

         return   Db::table('WC_product')->field(['id','product_name','purchase','price','class_id','new_purchase','new_price'])->where($condition)->find();

     }

    /**
     * 产品修改数据操作
     *
     * @access edit
     * @author duxinxin
     * @date 2020/04/26
     */
    public function edit($id, $condition){

        return    Db::table('WC_product')->where('id', $id)->update($condition);

    }

    /**
     * 清空现售价和现批价 数据操作
     *
     * @access clean
     * @author duxinxin
     * @date 2020/04/26
     */
    public function clean( $where){

        return Db::table('WC_product')->where('state', Config::get('STATE_YES'))->update($where);

    }

    /**
     * 产品查询列表操作
     *
     * @access show
     * @author duxinxin
     * @date 2020/04/26
     */
     public function  show(){

         return Db::table('WC_product')
             ->field(['id','product_name','purchase','price','class_id','new_purchase','new_price'])
             ->where(['state' => Config::get('STATE_YES')])
             ->order('id DESC')
             ->select();
     }



}