<?php
namespace app\home\model;

use think\Model;
use \think\db;
use \think\config;


/**
 * 物品model 和 分类model
 * @author duxinxin
 * @date 2020/04/26
 */

class Goods extends Model
{
    //自定义初始化
    protected function initialize()
    {
        //需要调用`Model`的`initialize`方法
        parent::initialize();

     }

    /**
     * 物品添加操作
     *
     * @access insertAdd
     * @author duxinxin
     * @date 2020/04/26
     */
    public function  insertAdd($data){

        return Db::table('goods')->insert($data);

    }

    /**
     * 物品查询总数操作
     *
     * @access counts
     * @author duxinxin
     * @date 2020/04/26
     */
     public function  counts($condition){

         $condition['state'] = Config::get('STATE_YES');
         return   Db::table('goods')->where($condition)->count('id'); //查询集合总数

     }

    /**
     * 物品分组查询
     *
     * @access goodsPriceGroup
     * @author duxinxin
     * @date 2020/04/26
     */
    public function  goodsPriceGroup(){
        $condition = [
            'state' => Config::get('STATE_YES'),
            'user_id' => session('admin_id'),
        ];
        return Db::table('goods') ->field("sum(price) as price,class")
            ->where($condition)
            ->group('class')
            ->select(); //分组查询


    }

    /**
     * 物品查询总金额操作
     *
     * @access counts
     * @author duxinxin
     * @date 2020/04/26
     */
    public function  goodsPrice($condition){

        $condition['state'] = Config::get('STATE_YES');
        return   Db::table('goods')->where($condition)->sum('price'); //查询集合总数

    }

    /**
     * 物品带条件 查询一条操作
     *
     * @access goodFind
     * @author duxinxin
     * @date 2020/04/26
     */
     public function goodFind($condition){

         return   Db::table('goods')->field(['id','username','class','price','meeting','photo','remark'])->where($condition)->find();

     }

    /**
     * 物品修改操作
     *
     * @access edit
     * @author duxinxin
     * @date 2020/04/26
     */
    public function edit($id, $condition){

        return   Db::table('goods')->where('id', $id)->update($condition); //修改操作

    }

    /**
     * 查询物品列表操作
     *
     * @access show
     * @author duxinxin
     * @date 2020/04/26
     */
     public function  show($condition){

         //查询集合并分页
         $news = Db::table('goods')
             ->field(['id','username','class','onlyid','price','meeting','photo','remark','addtime','state','remark'])
             ->whereLike('username',"%".$condition['username']."%")
             ->where($condition['where'])
             ->order('id DESC')->paginate(Config::get('PAGE_ONE'),false,['query'=>request()->param()]);

         //查询数量
         $num = Db::table('goods')
             ->whereLike('username',"%".$condition['username']."%")
             ->where($condition['where'])
             ->count('id');

         return $show = [
             'page'=>$news->render(),
             'data'=>$news->all(),
             'num' =>$num,
           ];
     }


    /**
     * 查询分类集合
     *
     * @access classList
     * @author duxinxin
     * @date 2020/04/26
     */
    public function  classList($diff){

        return   Db::table('class')
                    ->field(['id','classname','addtime'])
                    ->where(['diff'    =>  $diff, 'state'=> Config::get('STATE_YES')])
                    ->order('id desc')
                    ->select();

    }

    /**
     * 查询分类列表集合
     *
     * @access classPage
     * @author duxinxin
     * @date 2020/04/26
     */
    public function  classPage($condition){

        //查询集合并分页
        $news = Db::table('class')
            ->field(['id','classname','diff','state','addtime','update_date'])
            ->whereLike('classname',"%".$condition['classname']."%")
            ->where($condition['where'])
            ->order('id DESC')->paginate(Config::get('PAGE_ONE'),false,['query'=>request()->param()]);

        //查询集合数量
        $num = Db::table('class')
            ->whereLike('classname',"%".$condition['classname']."%")
            ->where($condition['where'])
            ->count('id');

        return $show = [
            'page'=>$news->render(),
            'data'=>$news->all(),
            'num' =>$num,
        ];
    }

    /**
     * 添加分类操作
     *
     * @access insertClass
     * @author duxinxin
     * @date 2020/04/26
     */
    public function  insertClass($data){

        return Db::table('class')->insert($data);

    }

    /**
     * 修改分类操作
     *
     * @access editClass
     * @author duxinxin
     * @date 2020/04/26
     */
    public function editClass($id, $condition){

        return   Db::table('class')->where('id', $id)->update($condition);

    }

    /**
     * 查询一条分类操作
     *
     * @access classFind
     * @author duxinxin
     * @date 2020/04/26
     */
    public function classFind($condition){

        return   Db::table('class')->field(['id','classname','diff'])->where($condition)->find();

    }



}