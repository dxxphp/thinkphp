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

         return   Db::table('user')->field(['id','username','photo','password','tell','sex','email','state','card_id','remark'])->where($condition)->find();

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

}