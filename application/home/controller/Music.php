<?php
namespace app\home\controller;

use app\home\model\User as UserModel;


/**
 * 音乐控制器
 * @author duxinxin
 * @date 2020/04/26
 */

class Music extends Common
{

    const rows = 15;
    const page = 1;

    // 音乐默认值
    const for_one = 1;  //列表循环播放 默认
    const for_two = 2;  //单曲播放
    const for_three = 3;  //随机播放

    const syns_no = 1;  //关闭音乐同步 默认
    const syns_yes = 2;  //开启音乐同步

    /**
     *  音乐列表
     *
     *
     * @method getMode
     */

    public function show(){

        $curpage = input('page') ? input('page') : self::page;//当前第x页，

        $UserModel = new UserModel();

        $data =  $UserModel->UserFind(['id' => session('admin_id')]);

        if($data['syns'] == self::syns_yes){

            $this->syns();

        }

        $UserModel = new UserModel();

        $music =  $UserModel->musicPage();

        $musicAll =  $UserModel->musicAll();


        $this->assign('fors', $data['fores']);
        $this->assign('syns', $data['syns']);
        $this->assign('plist', $music);

        $this->assign('page', $curpage);

        $this->assign('json',  json_encode($musicAll,JSON_UNESCAPED_SLASHES));//json 格式化

        return $this->fetch();

    }

    //修改播放模式和同步开关
    public function status(){

        $type = $this->request->post('type');

        $UserModel = new UserModel();

        if($type == 1){

            $fores = $this->request->post('fores');

            $music =  $UserModel->edit(session('admin_id'),['fores' => $fores]);


        }else{

            $syns = $this->request->post('syns');

            $music =  $UserModel->edit(session('admin_id'),['syns' => $syns]);

        }

        if($music){

            return json("200");
        }

    }


    //从文件夹中同步音乐到 数据库
    public function syns(){

        $UserModel = new UserModel();

        $file_path="uploads/music";

        $data = $this->folder_list($file_path);//遍历当前目录


        foreach($data as $key => $val){

           $artist = current(explode('.',$val['artist']));


            $find = $UserModel->musicFind(['artist' => $artist]);

            if(!$find){

                $val['artist'] = $artist;

                $UserModel->music_Insert($val);

            }

        }

    }


    //遍历出目录
    public function folder_list($dir){
        $dir .= substr($dir, -1) == '/' ? '' : '/';
        $dirInfo = array();
        foreach (glob($dir.'*') as $v) {
            $vs = explode("/",$v);

            $music = explode("-",$vs[2]);

            $dirInfo[] = ['title' => $music[0],'artist' =>$music[1] ,'mp3' => 'http://'.$_SERVER['SERVER_NAME'] .'/'.$v ,'poster' =>'' ];
            if(is_dir($v)){
                $dirInfo = array_merge($dirInfo, $this->folder_list($v));
            }
        }
        return $dirInfo;
    }

}




