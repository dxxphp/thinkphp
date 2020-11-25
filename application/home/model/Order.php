<?php
namespace app\home\model;

use think\Model;
use \think\db;
use \think\config;


/**
 * 订单管理model
 * @author duxinxin
 * @date 2020/04/26
 */

class Order extends Model
{
    //自定义初始化
    protected function initialize()
    {
        //需要调用`Model`的`initialize`方法
        parent::initialize();

     }

    /**
     * 订单添加操作
     *
     * @access insertAdd
     * @author duxinxin
     * @date 2020/04/26
     */
    public function  insertAdd($data){

        return Db::table('WC_order')->insert($data);

    }

    /**
     * 订单修改数据操作
     *
     * @access edit
     * @author duxinxin
     * @date 2020/04/26
     */
    public function edit($id, $condition){

        return    Db::table('WC_order')->where('id', $id)->update($condition);

    }

    /**
     * 订单查看一条数据
     *
     * @access find
     * @author duxinxin
     * @date 2020/04/26
     */
    public function find($condition){

        return   Db::table('WC_order')->field(['id','supshop','iphone','shop_id','total_purchase','new_total_price','profit','username','user_iphone','address','creat_time'])->where($condition)->find();

    }

    /**
     * 订单查询列表操作
     *
     * @access show
     * @author duxinxin
     * @date 2020/04/26
     */
    public function  show($condition){

        //查询集合并分页
        $news = Db::table('WC_order')
            ->field(['id','supshop','iphone','shop_id','total_purchase','new_total_price','profit','username','user_iphone','address','creat_time'])
            ->whereLike('supshop',"%".$condition['supshop']."%")
            ->where($condition['where'])
            ->order('id DESC')->paginate(Config::get('PAGE_ONE'),false,['query'=>request()->param()]);

        //查询数量
        $num = Db::table('WC_order')
            ->whereLike('supshop',"%".$condition['supshop']."%")
            ->where($condition['where'])
            ->count('id');

        return $show = [
            'page'=>$news->render(),
            'data'=>$news->all(),
            'num' =>$num,
        ];
    }




}