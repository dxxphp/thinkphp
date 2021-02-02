<?php
namespace app\home\controller;

use think\Config;
use think\cache\driver\Redis;
use app\home\model\Goods as GoodsModel;
use app\home\model\Pay as PayModel;
use app\home\controller\Helps\imgHelper as imgHelper;


/**
 * 收支管理控制器
 * @author duxinxin
 * @date 2020/04/26
 */

class Pay extends Common
{

    /**
     * 收支列表
     *
     * @access index
     * @author duxinxin
     * @date 2020/04/26
     */
    public function index()
    {
        $imgHelper = new imgHelper();
        $condition = $imgHelper->productBuild($this->request->get(), Config::get('STATE_YES'));

        $PayModel = new PayModel();
        $data =  $PayModel->show($condition);

        $GoodsModel = new GoodsModel();
        $classData =  $GoodsModel->classList(Config::get('CLASS_MONEY'));
        $classData = array_combine(array_column($classData, 'id'), $classData);

        $payData = $imgHelper->getPay();
        $getMode = $imgHelper->getMode();
        //添加pv
        $imgHelper->pvConfig('pv::payList', '收支列表页面');
        $this ->assign(
            [
                'payMode'   => $getMode,
                'list'       => $data,
                'classData'  => $classData,
                'search'     => $condition['where'],
                'username'     => $condition['username'],
                'payData'     => $payData
            ]
        );
        return $this->fetch();
    }

    /**
     * 收支数据添加
     *
     * @access add
     * @author duxinxin
     * @date 2020/04/26
     */
    public function add(){

        if ($this->request->isPost()){

            $postData = $this->request->post();

            $Data = [

                'username' => $postData['username'],

                'class'    => $postData['class'],

                'price'    => $postData['price'],

                'pay_cate' => $postData['pay_cate'],

                'pay_mode' => $postData['pay_mode'],

                'number'   => $postData['number'],

                'remark'   => $postData['remark'],

                'usetime'  => strtotime($postData['usetime']),

                'addtime'  => time(),

                'user_id'  => session('admin_id'),

            ];

            $PayModel = new PayModel();
            $data =  $PayModel->insertAdd($Data);

            if($data) {
                return $this->success('保存成功', '');
            }else{
                return $this->error('保存失败', '');
            }

        }else{

            $GoodsModel = new GoodsModel();
            $classData =  $GoodsModel->classList(Config::get('CLASS_MONEY'));
            $imgHelper = new imgHelper();

            $payData = $imgHelper->getPay();
            $getMode = $imgHelper->getMode();
            //添加pv
            $imgHelper->pvConfig('pv::payAdd', '收支添加页面');

            $this ->assign([
                'payMode'   => $getMode,
                'payData'   => $payData,
                'classData'  => $classData
            ]);
            return $this->fetch();
        }
    }

    /**
     * 收支删除操作
     *
     * @access del
     * @author duxinxin
     * @date 2020/04/26
     */
    public function del(){

        $id = $this->request->get('id');
        $PayModel = new PayModel();
        $where = [
            'state' => Config::get('STATE_NO')
        ];
        $data =  $PayModel->edit($id, $where);

        if($data) {
            return $this->success('删除成功', 'home/pay/index');
        }else{
            return $this->error('删除失败', 'home/pay/index');
        }
    }

    /**
     * 收支恢复操作
     *
     * @access recover
     * @author duxinxin
     * @date 2020/04/26
     */
    public function recover(){

        $id = $this->request->get('id');
        $PayModel = new PayModel();
        $where = [
            'state' => Config::get('STATE_YES')
        ];
        $data =  $PayModel->edit($id, $where);

        if($data) {
            return $this->success('恢复成功', 'home/pay/payBin');
        }else{
            return $this->error('恢复失败', 'home/pay/payBin');
        }
    }

    /**
     * 收支编辑操作
     *
     * @access edit
     * @author duxinxin
     * @date 2020/04/26
     */
    public function edit(){

        if ($this->request->isPost()){

            $id = $this->request->get('id');
            $postData = $this->request->post();

            $Data = [

                'username' => $postData['username'],

                'class'    => $postData['class'],

                'price'    => $postData['price'],

                'pay_cate' => $postData['pay_cate'],

                'pay_mode' => $postData['pay_mode'],

                'number'   => $postData['number'],

                'remark'   => $postData['remark'],

                'usetime'  => strtotime($postData['usetime']),

                'endtime'  => time(),

            ];

            $PayModel = new PayModel();
            $data =  $PayModel->edit($id, $Data);

            if($data) {
                return $this->success('修改成功', '');
            }else{
                return $this->error('修改失败', '');
            }

        }else{

            $id = $this->request->get('id');
            $imgHelper = new imgHelper();
            $GoodsModel = new GoodsModel();
            $classData =  $GoodsModel->classList(Config::get('CLASS_MONEY'));
            $classData = array_combine(array_column($classData, 'id'), $classData);

            $PayModel = new PayModel();
            $data =  $PayModel->payFind(['id' => $id]);
            $payData = $imgHelper->getPay();
            $getMode = $imgHelper->getMode();
            //添加pv
            $imgHelper->pvConfig('pv::payEdit', '收支编辑页面');
            $this ->assign([
                'classData'  =>  $classData,
                'data'       => $data,
                'payMode'   => $getMode,
                'payData'   => $payData,
            ]);
            return $this->fetch();
        }

    }

    /**
     * 收支回收站列表
     *
     * @access payBin
     * @author duxinxin
     * @date 2020/04/26
     */
    public function payBin(){

        $imgHelper = new imgHelper();
        $condition = $imgHelper->productBuild($this->request->get(), Config::get('STATE_NO'));

        $PayModel = new PayModel();
        $data =  $PayModel->show($condition);
        $GoodsModel = new GoodsModel();
        $classData =  $GoodsModel->classList(Config::get('CLASS_MONEY'));
        $classData = array_combine(array_column($classData, 'id'), $classData);

        $payData = $imgHelper->getPay();
        $getMode = $imgHelper->getMode();
        //添加pv
        $imgHelper->pvConfig('pv::payBin', '收支回收站页面');
        $this ->assign(
            [
                'payMode'   => $getMode,
                'list'       => $data,
                'classData'  => $classData,
                'search'     => $condition['where'],
                'username'     => $condition['username'],
                'payData'     => $payData
            ]
        );
        return $this->fetch();
    }

}
