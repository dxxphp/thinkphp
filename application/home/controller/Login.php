<?php
namespace app\home\controller;

use think\Config;
use think\Controller;

use think\cache\driver\Redis;
use think\Request;
use app\home\model\User as UserModel;


/**
 * 登陆管理控制器
 * @author duxinxin
 * @date 2020/04/26
 */

class Login extends Controller
{

    protected $redis;

    /**
     * 登陆页面
     *
     * @access index
     * @author duxinxin
     * @date 2020/04/26
     */
    public function index()
    {


        return $this->fetch();
    }

    public function login(){

            $username = $this->request->post('username');
            $password = $this->request->post('password');

            if(empty($username) || empty($password)){

                return json("账号或者密码不能为空！");

            }

            $UserModel = new UserModel();
            $data =  $UserModel->UserFind(['username' => $username]);

            if($data){

                if($data['password'] != base64_encode($password)){

                    return json("密码错误！");

                }else{

                    $token = md5($data['id']);
                    $redis = self::redisServer();
                    $redis->set( $token, serialize($data), 3600*24);
                    session('admin_id', $data['id']);
                    return json("200");

                }


            }else{
                return json("用户不存在");
            }



    }

    //推出登陆
    public function logout(){
//        session(null);//退出清空session

        $redis = $this->redisServer();
        $token = md5(session('admin_id'));
        $redis->del( $token);

        return $this->success('退出成功',url('Login/index'));//跳转到登录页面
    }

    public function redisServer(){

        $this->redis = new \Redis();
        $this->redis->connect('127.0.0.1', 6379);
        return $this->redis;

    }


}
