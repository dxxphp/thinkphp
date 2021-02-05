<?php
namespace app\home\model;

use think\Model;
use \think\db;
use \think\config;


/**
 * 用户管理model
 * @author duxinxin
 * @date 2020/04/26
 */

class User extends Model
{
    //自定义初始化
    protected function initialize()
    {
        //需要调用`Model`的`initialize`方法
        parent::initialize();

     }

    /**
     * 用户查询一条数据
     *
     * @access goodFind
     * @author duxinxin
     * @date 2020/04/26
     */
     public function UserFind($condition){

         return   Db::table('user')->field(['id','username','photo','password','tell','sex','email','fores','syns','state','card_id','remark'])->where($condition)->find();

     }

    /**
     * 用户修改数据操作
     *
     * @access edit
     * @author duxinxin
     * @date 2020/04/26
     */
    public function edit($id, $condition){

        return    Db::table('user')->where('id', $id)->update($condition);


    }

    /**
     * 音乐列表添加操作
     *
     * @access music_Insert
     * @author duxinxin
     * @date 2020/04/26
     */
    public function music_Insert($data){

        return    Db::table('music')->insert($data);

    }

    /**
     * 音乐查询一条
     *
     * @access musicFind
     * @author duxinxin
     * @date 2020/04/26
     */
    public function musicFind($condition){


        return   Db::table('music')->field(['id','artist'])->where($condition)->find();

    }

    /**
     * 查询音乐列表集合
     *
     * @access classPage
     * @author duxinxin
     * @date 2020/04/26
     */
    public function  musicPage(){

        //查询集合并分页
        $news = Db::table('music')
            ->field(['id','title','artist','mp3','poster'])
//            ->whereLike('classname',"%".$condition['classname']."%")
//            ->where($condition['where'])
            ->order('id DESC')->paginate(20,false,['query'=>request()->param()]);

        //查询集合数量
        $num = Db::table('music')
//            ->whereLike('classname',"%".$condition['classname']."%")
//            ->where($condition['where'])
            ->count('id');

        return $show = [
            'page'=>$news->render(),
            'data'=>$news->all(),
            'num' =>$num,
        ];
    }

    /**
     * 查询音乐全部集合
     *
     * @access classPage
     * @author duxinxin
     * @date 2020/04/26
     */
    public function  musicAll($condition = ''){

        //查询集合
        $arr =  Db::table('music')
            ->field(['title','artist','mp3','poster'])
            ->where($condition)
            ->order('id DESC')
            ->select();

        return $arr;
    }


}