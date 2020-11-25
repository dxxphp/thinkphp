<?php
namespace app\home\model;

use think\Model;
use \think\db;
use \think\config;


/**
 * 购物车管理model
 * @author duxinxin
 * @date 2020/04/26
 */

class Shop extends Model
{
    //自定义初始化
    protected function initialize()
    {
        //需要调用`Model`的`initialize`方法
        parent::initialize();

     }

    /**
     * 加入购物车操作
     *
     * @access insertAdd
     * @author duxinxin
     * @date 2020/04/26
     */
    public function  insertAdd($data){

        return Db::table('WC_shop')->insert($data);

    }

    /**
     * 查询购物车一条数据
     *
     * @access shopFind
     * @author duxinxin
     * @date 2020/04/26
     */
     public function shopFind($condition){

         return   Db::table('WC_shop')->field(['id','name','purchase','price','class_id','new_purchase','new_price'])->where($condition)->find();

     }

    /**
     * 查询购物车数据
     *
     * @access selectFind
     * @author duxinxin
     * @date 2020/04/26
     */
    public function selectFind($condition){

        return    Db::table('WC_shop')->field(['id','name','purchase','price','class_id','new_purchase','new_price','num','creat_time'])->where($condition)->order('id DESC')->select();


    }

    /**
     * 购物车修改数据操作
     *
     * @access edit
     * @author duxinxin
     * @date 2020/04/26
     */
    public function edit($id, $condition){

        return    Db::table('WC_shop')->where('id', $id)->update($condition);

    }

    /**
     * 购物车查询列表操作
     *
     * @access show
     * @author duxinxin
     * @date 2020/04/26
     */
     public function  show(){

         return Db::table('WC_shop')
             ->field(['id','name','purchase','price','class_id','new_purchase','new_price','num','creat_time'])
             ->where(['state' => Config::get('STATE_YES')])
             ->order('id DESC')
             ->select();
     }



}