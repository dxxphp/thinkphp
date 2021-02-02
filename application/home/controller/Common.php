<?php
namespace app\home\controller;

use think\Controller;
use think\cache\driver\Redis;

use app\home\model\User as UserModel;

/**
 * [全局继承类]
 * @Author: duxinxin
 * @Date:   2020-03-03 12:30:04
 * @Last Modified by:   duxinxin
 * @Last Modified time: 2020-03-03 12:30:04
 */

class Common extends Controller
{

    const time = 3600;

    protected $redis;
    public function _initialize()
    {

        $redis = self::redisMain();
        $token = md5(session('admin_id'));
        $data = unserialize($redis->get($token));

       if(empty($data)){

           return $this->error('您没有登陆',url('Login/index'));

       }
        //判断有无admin_username这个session，如果没有，跳转到登陆界面
//        if(!session('admin_id')){
//
//            return $this->error('您没有登陆',url('Login/index'));
//
//        }
//
//        if (time() - session('session_start_time') > self::time) {
//            session_destroy();//真正的销毁在这里！
//            $this->redirect(url('Login/index'));
//        }


        $redisVice = self::redisVice();
        $data = $redisVice->hGetAll('user'.session('admin_id'));
        if(empty($data)){
            $UserModel = new UserModel();
            $data =  $UserModel->UserFind(['id' => session('admin_id')]);
        }
        $this ->assign([
            'user'       => $data
        ]);
    }

    /**
     * redis 主服务
     *
     * @access redisMain
     * @author duxinxin
     * @date 2020/04/26
     */
    public function redisMain(){

        $this->redis = new \Redis();
        $this->redis->connect('127.0.0.1', 6379);
        return $this->redis;

    }

    /**
     * redis 从服务1
     *
     * @access redisVice
     * @author duxinxin
     * @date 2020/04/26
     */
    public function redisVice(){

        $this->redis = new \Redis();
        $this->redis->connect('127.0.0.1', 6379);
        return $this->redis;

    }

    /**
     * redis 从服务2
     *
     * @access redisFrom
     * @author duxinxin
     * @date 2020/04/26
     */
    public function redisFrom(){

        $this->redis = new \Redis();
        $this->redis->connect('127.0.0.1', 6379);
        return $this->redis;

    }





}

