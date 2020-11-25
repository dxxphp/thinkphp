<?php
namespace app\home\model;

use think\Model;
use \think\db;
use \think\config;


/**
 * 收支管理model
 * @author duxinxin
 * @date 2020/04/26
 */

class Pay extends Model
{
    //自定义初始化
    protected function initialize()
    {
        //需要调用`Model`的`initialize`方法
        parent::initialize();

     }

    /**
     * 收支添加操作
     *
     * @access insertAdd
     * @author duxinxin
     * @date 2020/04/26
     */
    public function  insertAdd($data){

        return Db::table('pay')->insert($data);

    }

    /**
     * 收支查询总数
     *
     * @access counts
     * @author duxinxin
     * @date 2020/04/26
     */
     public function  counts($params){

         $condition = [
             'state' => Config::get('STATE_YES'),
             'pay_cate' => $params,
         ];         return   Db::table('pay')->where($condition)->count('id'); //查询集合总数

     }

    /**
     * 收支各月查询
     *
     * @access counts
     * @author duxinxin
     * @date 2020/04/26
     */
    public function  payNum($params, $pay_cate){


            $condition = [
                'state' => Config::get('STATE_YES'),
                'pay_cate' => $pay_cate,
                'usetime' => ['between', [$params['begin'], $params['end']]]
            ];
            return  Db::table('pay')->where($condition)->sum('price'); //查询集合总钱数


        }

    /**
     * 收支查询总金额
     *
     * @access payPrice
     * @author duxinxin
     * @date 2020/04/26
     */
    public function  payPrice($params){

        $condition = [
            'state' => Config::get('STATE_YES'),
            'pay_cate' => $params,
        ];
        return  Db::table('pay')->where($condition)->sum('price'); //查询集合总数


    }

    /**
     * 收支分组查询
     *
     * @access payPriceGroup
     * @author duxinxin
     * @date 2020/04/26
     */
    public function  payPriceGroup($params){
        $condition = [
            'state' => Config::get('STATE_YES'),
            'pay_cate' => $params,
        ];
        return Db::table('pay') ->field("sum(price) as price,class")
            ->where($condition)
            ->group('class')
            ->select(); //分组查询


    }

    /**
     * 收支不同支付方式 总金额 查询
     *
     * @access payPriceGroup
     * @author duxinxin
     * @date 2020/04/26
     */
    public function  payPriceCate($params, $mode){
        $condition = [
            'state' => Config::get('STATE_YES'),
            'pay_cate' => $params,
            'pay_mode' => $mode
        ];
        return  Db::table('pay')->where($condition)->sum('price');


    }


    /**
     * 收支查询一条数据
     *
     * @access goodFind
     * @author duxinxin
     * @date 2020/04/26
     */
     public function payFind($condition){

         return   Db::table('pay')->field(['id','username','class','number','pay_cate','price','remark','addtime','state','usetime','pay_mode'])->where($condition)->find();

     }

    /**
     * 收支修改数据操作
     *
     * @access edit
     * @author duxinxin
     * @date 2020/04/26
     */
    public function edit($id, $condition){

        return    Db::table('pay')->where('id', $id)->update($condition);

    }

    /**
     * 收支查询列表操作
     *
     * @access show
     * @author duxinxin
     * @date 2020/04/26
     */
     public function  show($condition){


         if(!empty($condition['where']['addtime']) and !empty($condition['where']['endtime']) ){
             $condition['where'] += [
                 'usetime' => ['between', [$condition['where']['addtime'], $condition['where']['endtime']]]
             ];

             unset($condition['where']['addtime']);
             unset($condition['where']['endtime']);
         }
         //查询集合并分页
         $news = Db::table('pay')
             ->field(['id','username','class','number','pay_cate','price','remark','addtime','state','usetime','pay_mode'])
             ->whereLike('username',"%".$condition['username']."%")
             ->where($condition['where'])
             ->order('id DESC')->paginate(Config::get('PAGE_ONE'),false,['query'=>request()->param()]);

         //查询数量
         $num = Db::table('pay')
             ->whereLike('username',"%".$condition['username']."%")
             ->where($condition['where'])
             ->count('*'); //查询集合总数
         //查询钱数
         $price = Db::table('pay')
             ->whereLike('username',"%".$condition['username']."%")
             ->where($condition['where'])
             ->sum('price'); //查询集合总数
         return $show = [
             'page'=>$news->render(),
             'data'=>$news->all(),
             'num' =>$num,
             'price' =>$price,
         ];
     }



}