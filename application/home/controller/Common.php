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

    protected $redis;
    public function _initialize()
    {

        $redisVice = self::redisVice();
        $data = $redisVice->hGetAll('user'.'1');
        if(empty($data)){
            $UserModel = new UserModel();
            $data =  $UserModel->UserFind(['id' => '1']);
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

