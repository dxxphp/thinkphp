<?php
namespace app\home\controller;

use think\Config;
use \think\db;
use think\paginator\driver\Bootstrap;
use \think\Request;

/**
 * 音乐控制器
 * @author duxinxin
 * @date 2020/04/26
 */

class Music extends Common
{

    const rows = 15;
    const page = 1;

    /**
     *  音乐列表
     *
     *
     * @method getMode
     */

    public function show(){

        $curpage = input('page') ? input('page') : self::page;//当前第x页，

        $p = $this->file_url($curpage);

        $this->assign('page', $curpage);

        $this->assign('plist', $p);

        $this->assign('plistpage', $p->render());

        $search = $this->request->get('seach');

        if($search){

            $file_path="uploads/music";

            $data = $this->folder_list($file_path);//遍历当前目录

            $arr = [];
            foreach ($data as $key => $val){

                if($val['artist'] == $search){
                    $arr[$key]['title'] = $val['title'];
                    $arr[$key]['artist'] = $val['artist'];
                    $arr[$key]['mp3'] = $val['mp3'];
                    $arr[$key]['poster'] = $val['poster'];

                }
            }


            $res = [
                'data'=>array_values($arr)
            ];

            $this->assign('json',  json_encode(array_merge($res),JSON_UNESCAPED_SLASHES));//json 格式化

        }else{
            $this->assign('json',  json_encode($p,JSON_UNESCAPED_SLASHES));//json 格式化

        }


        return $this->fetch();

    }


    //处理音乐目录
    public function file_url($curpage){

        $file_path="uploads/music";

        $data = $this->folder_list($file_path);//遍历当前目录


        $rows = self::rows;//每页显示几条记录

        $dataTo = array_chunk($data,$rows);

        if($dataTo){
            $showdata = $dataTo[$curpage-1];
        }else{
            $showdata = null;
        }

        $p = Bootstrap::make($showdata, $rows, $curpage, count($data), false, [
            'var_page' => 'page',
            'path'     => url('show'),//这里根据需要修改url
            'query'    => [],
            'fragment' => '',
        ]);

        return $p;

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




