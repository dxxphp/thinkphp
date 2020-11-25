<?php
namespace app\home\model;

use think\Model;
use \think\db;
use \think\config;


/**
 * 小票管理model
 * @author duxinxin
 * @date 2020/04/26
 */

class Invoice extends Model
{
    //自定义初始化
    protected function initialize()
    {
        //需要调用`Model`的`initialize`方法
        parent::initialize();

     }

    /**
     * 小票添加操作
     *
     * @access insertAdd
     * @author duxinxin
     * @date 2020/04/26
     */
    public function  insertAdd($data){

        return Db::table('invoice')->insert($data);

    }

    /**
     * 小票查询总数
     *
     * @access counts
     * @author duxinxin
     * @date 2020/04/26
     */
     public function  counts(){

         $condition['state'] = Config::get('STATE_YES');
         return   Db::table('invoice')->where($condition)->count('id'); //查询集合总数

     }

    /**
     * 小票分组查询
     *
     * @access invoicePriceGroup
     * @author duxinxin
     * @date 2020/04/26
     */
    public function  invoicePriceGroup(){
        $condition = [
            'state' => Config::get('STATE_YES'),
        ];
        return Db::table('invoice') ->field("sum(price) as price,class")
            ->where($condition)
            ->group('class')
            ->select(); //分组查询


    }


    /**
     * 小票查询总金额
     *
     * @access counts
     * @author duxinxin
     * @date 2020/04/26
     */
    public function  invoicePrice(){

        $condition['state'] = Config::get('STATE_YES');
        return   Db::table('invoice')->where($condition)->sum('price'); //查询集合总数

    }

    /**
     * 小票查询一条数据
     *
     * @access goodFind
     * @author duxinxin
     * @date 2020/04/26
     */
     public function goodFind($condition){

         return   Db::table('invoice')->field(['id','username','class','number','address','price','photo','usetime','addtime','state'])->where($condition)->find();

     }

    /**
     * 小票修改数据操作
     *
     * @access edit
     * @author duxinxin
     * @date 2020/04/26
     */
    public function edit($id, $condition){

        return    Db::table('invoice')->where('id', $id)->update($condition);

    }

    /**
     * 小票查询列表操作
     *
     * @access show
     * @author duxinxin
     * @date 2020/04/26
     */
     public function  show($condition){

         //查询集合并分页
         $news = Db::table('invoice')
             ->field(['id','username','class','number','address','price','photo','usetime','addtime','state'])
             ->whereLike('username',"%".$condition['username']."%")
             ->where($condition['where'])
             ->order('id DESC')->paginate(Config::get('PAGE_ONE'),false,['query'=>request()->param()]);

         //查询数量
         $num = Db::table('invoice')
             ->whereLike('username',"%".$condition['username']."%")
             ->where($condition['where'])
             ->count('id');

         return $show = [
             'page'=>$news->render(),
             'data'=>$news->all(),
             'num' =>$num,
         ];
     }



}