<?php
namespace app\index\controller;

use think\Controller;
use think\View;

class Index extends Controller
{
    public function Index()
    {
//        echo 1;die;
        $this->redirect('home/index/index');
        return $this->fetch();
    }


}
