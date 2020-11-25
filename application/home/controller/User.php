<?php
namespace app\home\controller;

use app\home\model\User as UserModel;
use app\home\controller\Helps\imgHelper as imgHelper;
use app\home\controller\Common;

/**
 * 个人管理控制器
 * @author duxinxin
 * @date 2020/04/26
 */

class User extends Common
{
    /**
     * 个人编辑操作
     *
     * @access edit
     * @author duxinxin
     * @date 2020/04/26
     */
    public function edit(){
//        // 循环到队列 （模拟事件进入队列）
//        for ($i=2000;$i<5000;$i++){
//
//            $data = [
//                'uid'           =>$i,
//                'channel'       =>'qw',
//                'beinvit_uid'   =>'dd',
//                'is_new'        =>1 ,//当天新用户
//                'first_invite'  =>'22',
//                'inviter'       =>2,
//                'reg_time'      =>time(),
//                'ip'            =>'127.0.0.1'
//            ];
//            $redisMain = Common::redisMain();
////////
//            $arr =  $redisMain->lpush('maia', json_encode($data));
//
////           print_r($arr);die;
//        }
//        die;

        if ($this->request->isPost()){

            $id = '1';
            $postData = $this->request->post();
            $file = request()->file('image');
            if($file){
                $url = 'uploads/userPhoto';
                $imgHelper = new imgHelper();
                $photo = $imgHelper->getPhotoUrl($file, $url);
                $Data = [

                    'username' => $postData['username'],

                    'password'    => base64_encode($postData['password']),

                    'tell'    => $postData['tell'],

                    'photo'    => $photo,

                    'email'    => $postData['email'],

                    'card_id'  => $postData['card_id'],

                    'sex'      =>   $postData['sex'],

                    'remark'   => $postData['remark'],

                    'endtime'  => time(),

                ];

            }else{

                $Data = [

                    'username' => $postData['username'],

                    'password'    => base64_encode($postData['password']),

                    'tell'    => $postData['tell'],

                    'photo'    => !empty($postData['photo'])? $postData['photo']:'',

                    'email'    => $postData['email'],

                    'card_id'  => $postData['card_id'],

                    'sex'      =>   $postData['sex'],

                    'remark'   => $postData['remark'],

                    'endtime'  => time(),

                ];
            }

            $UserModel = new UserModel();
            $data =  $UserModel->edit($id, $Data);
            $redisMain = Common::redisMain();

            if($data) {
                return $this->success('修改成功', '');
            }else{
                return $this->error('修改失败', '');
            }

        }else{

            $id = '1';
            $redisVice = Common::redisVice();
            $data = $redisVice->hGetAll('user'.$id);

            if(empty($data)){
                $UserModel = new UserModel();
                $data =  $UserModel->UserFind(['id' => $id]);

                $redisMain = Common::redisMain();
                $redisMain->hMset('user'.$id, $data);

            }
            //添加pv
            $imgHelper = new imgHelper();
            $imgHelper->pvConfig('pv::user', '用户页面');

            $this ->assign([
                'data'       => $data
            ]);
            return $this->fetch();
        }

    }



}
