<?php
namespace app\home\model;

use think\Model;
use \think\db;
use \think\config;


/**
 * 微信客户model
 * @author duxinxin
 * @date 2020/04/26
 */

class WeChatUser extends Model
{
    //自定义初始化
    protected function initialize()
    {
        //需要调用`Model`的`initialize`方法
        parent::initialize();

     }

    /**
     * 微信图片识别添加
     *
     * @access insertAdd
     * @author duxinxin
     * @date 2020/04/26
     */
    public function  dataInsert($data){

        return Db::table('WeChatUser')->insert($data);


    }

    public function  counts(){

        $condition['status'] = Config::get('STATE_YES');
        return   Db::table('WeChatUser')->where($condition)->count('id'); //查询集合总数

    }
    /**
     * 微信客户批量添加操作
     *
     * @access insertAdd
     * @author duxinxin
     * @date 2020/04/26
     */
    public function  insertAddAll($data){
       return  Db::table('WeChatUser')->insertAll($data);

    }



    /**
     * 微信客户带条件 查询一条操作
     *
     * @access goodFind
     * @author duxinxin
     * @date 2020/04/26
     */
     public function WeChatUserFind($condition){

         return   Db::table('WeChatUser')->field(['id','iphone','name','WeChat','sex','remarks','status','address','addtime','result','photo'])->where($condition)->find();

     }

    /**
     * 微信修改操作
     *
     * @access edit
     * @author duxinxin
     * @date 2020/04/26
     */
    public function edit($id, $condition){

        return   Db::table('WeChatUser')->where('id', $id)->update($condition); //修改操作

    }

    /**
 * 查询微信客户列表操作
 *
 * @access show
 * @author duxinxin
 * @date 2020/04/26
 */
    public function  show($condition){
        //查询集合并分页
        $news = Db::table('WeChatUser')
            ->field(['id','iphone','name','WeChat','sex','remarks','status','address','addtime','result','photo'])
            ->where($condition['where'])
            ->order('id DESC')->paginate(Config::get('WE_PAGE_ONE'),false,['query'=>request()->param()]);

        //查询数量
        $num = Db::table('WeChatUser')
            ->where($condition['where'])
            ->count('id');

        return $show = [
            'page'=>$news->render(),
            'data'=>$news->all(),
            'num' =>$num,
        ];
    }

    /**
     * 查询微信客户列表操作
     *
     * @access show
     * @author duxinxin
     * @date 2020/04/26
     */
    public function  showExcel($where){

        return Db::table('WeChatUser')->field(['id','iphone','name','WeChat','sex','remarks','address','addtime'])->where($where)->select();


    }

    /**
     * 查询微信客户列表操作
     *
     * @access show
     * @author duxinxin
     * @date 2020/04/26
     */
    public function  showSync(){
        //查询集合并分页
        return Db::table('w_customer')->field(['id','name','contact','phone','fax','address','desc','add_time'])->select();


    }

    public function showSyncA(){
        $where['id'] = ['gt', 900];
        $where['address'] = ['neq', ''];
       return Db::table('WeChatUser')->field(['id','remarks','address'])
            ->where($where)
            ->select();

    }





}