<?php
namespace app\home\controller;

use think\Config;
use think\cache\driver\Redis;
use app\home\model\Goods as GoodsModel;
use app\home\model\Invoice as InvoiceModel;
use app\home\controller\Helps\imgHelper as imgHelper;


/**
 * 小票管理控制器
 * @author duxinxin
 * @date 2020/04/26
 */

class Invoice extends Common
{


    /**
     * 小票列表
     *
     * @access index
     * @author duxinxin
     * @date 2020/04/26
     */
    public function index(){

        $imgHelper = new imgHelper();
        $condition = $imgHelper->productBuild($this->request->get(), Config::get('STATE_YES'));

        $GoodsModel = new GoodsModel();
        $InvoiceModel = new InvoiceModel();
        $data =  $InvoiceModel->show($condition);
        $classData =  $GoodsModel->classList(Config::get('CLASS_SMALL'));
        $classData = array_combine(array_column($classData, 'id'), $classData);

        //添加pv
        $imgHelper->pvConfig('pv::invoiceList', '小票列表页面');
        $this ->assign(
            [
                'list'       => $data,
                'classData'  => $classData,
                'search'     => $condition['where'],
                'username'   => $condition['username'],
            ]
        );
        return $this->fetch();
    }

    /**
     * 小票数据添加
     *
     * @access add
     * @author duxinxin
     * @date 2020/04/26
     */
    public function add(){

        if ($this->request->isPost()){

            $postData = $this->request->post();
            $file = request()->file('image');

            if($file){
                $url = 'uploads/invoicePhoto';
                $imgHelper = new imgHelper();
                $postData['photo'] = $imgHelper->getPhotoUrl($file, $url);
            }
            $Data = [

                'username' => $postData['username'],

                'class'    => $postData['class'],

                'price'    => $postData['price'],

                'number'   => $postData['number'],

                'photo'    => !empty($postData['photo'])? $postData['photo']:'',

                'usetime'  => strtotime($postData['usetime']),

                'address'  => $postData['address'],

                'addtime'  => time(),

            ];

            $InvoiceModel = new InvoiceModel();
            $data =  $InvoiceModel->insertAdd($Data);

            if($data) {
                return $this->success('保存成功', '');
            }else{
                return $this->error('保存失败', '');
            }

        }else{

            $GoodsModel = new GoodsModel();
            $classData =  $GoodsModel->classList(Config::get('CLASS_SMALL'));
            //添加pv
            $imgHelper = new imgHelper();
            $imgHelper->pvConfig('pv::invoiceAdd', '小票添加页面');
            $this ->assign([
                'classData'  => $classData
            ]);
            return $this->fetch();
        }

    }

    /**
     * 小票删除操作
     *
     * @access del
     * @author duxinxin
     * @date 2020/04/26
     */
    public function del(){

        $id = $this->request->get('id');
        $InvoiceModel = new InvoiceModel();

        $where = [
            'state' => Config::get('STATE_NO')
        ];
        $data =  $InvoiceModel->edit($id, $where);

        if($data) {
            return $this->success('删除成功', 'home/invoice/index');
        }else{
            return $this->error('删除失败', 'home/index/index');
        }
    }

    /**
     * 小票恢复操作
     *
     * @access recover
     * @author duxinxin
     * @date 2020/04/26
     */
    public function recover(){

        $id = $this->request->get('id');
        $InvoiceModel = new InvoiceModel();

        $where = ['state' => Config::get('STATE_YES')];
        $data =  $InvoiceModel->edit($id, $where);

        if($data) {
            return $this->success('恢复成功', 'home/invoice/invoiceBin');
        }else{
            return $this->error('恢复失败', 'home/invoice/invoiceBin');
        }
    }

    /**
     * 小票编辑操作
     *
     * @access edit
     * @author duxinxin
     * @date 2020/04/26
     */
    public function edit(){

        if ($this->request->isPost()){

            $id = $this->request->get('id');
            $postData = $this->request->post();
            $file = request()->file('image');
            if($file){

                $url = 'uploads/invoicePhoto';
                $imgHelper = new imgHelper();
                $photo = $imgHelper->getPhotoUrl($file, $url);

                $Data = [

                    'username' => $postData['username'],

                    'class'    => $postData['class'],

                    'price'    => $postData['price'],

                    'number'   => $postData['number'],

                    'photo'    => $photo,

                    'usetime'  => strtotime($postData['usetime']),

                    'address'  => $postData['address'],

                    'endtime'  => time(),

                ];

            }else{

                $Data = [

                    'username' => $postData['username'],

                    'class'   => $postData['class'],

                    'price'   => $postData['price'],

                    'number'  => $postData['number'],

                    'photo'   => !empty($postData['photo'])? $postData['photo']:'',

                    'usetime' => strtotime($postData['usetime']),

                    'address' => $postData['address'],

                    'endtime' => time(),

                ];
            }
            $InvoiceModel = new InvoiceModel();
            $data =  $InvoiceModel->edit($id, $Data);

            if($data) {
                return $this->success('修改成功', '');
            }else{
                return $this->error('修改失败', '');
            }

        }else{

            $id = $this->request->get('id');
            $GoodsModel = new GoodsModel();
            $classData =  $GoodsModel->classList(Config::get('CLASS_SMALL'));
            $InvoiceModel = new InvoiceModel();
            $data =  $InvoiceModel->goodFind(['id' => $id]);
            //添加pv
            $imgHelper = new imgHelper();
            $imgHelper->pvConfig('pv::invoiceEdit', '小票编辑页面');

            $this ->assign([
                'classData'  =>  $classData = array_combine(array_column($classData, 'id'), $classData),
                'data'       => $data
            ]);
            return $this->fetch();
        }

    }

    /**
     * 小票回收站列表
     *
     * @access invoiceBin
     * @author duxinxin
     * @date 2020/04/26
     */
    public function invoiceBin(){

        $imgHelper = new imgHelper();
        $condition = $imgHelper->productBuild($this->request->get(), Config::get('STATE_NO'));

        $GoodsModel = new GoodsModel();
        $InvoiceModel = new InvoiceModel();
        $data =  $InvoiceModel->show($condition);
        $classData =  $GoodsModel->classList(Config::get('CLASS_SMALL'));
        $classData = array_combine(array_column($classData, 'id'), $classData);
        //添加pv
        $imgHelper->pvConfig('pv::invoiceBin', '小票回收站页面');
        $this ->assign(
            [
                'list'       => $data,
                'classData'  => $classData,
                'search'     => $condition['where'],
                'username'   => $condition['username'],
            ]
        );
        return $this->fetch();
    }

    /**
     * 小票图片列表
     *
     * @access photo
     * @author duxinxin
     * @date 2020/04/26
     */
    public function photo(){

        $imgHelper = new imgHelper();
        $condition = $imgHelper->productBuild($this->request->get(), Config::get('STATE_YES'));

        $GoodsModel = new GoodsModel();
        $InvoiceModel = new InvoiceModel();
        $data =  $InvoiceModel->show($condition);
        $classData =  $GoodsModel->classList(Config::get('CLASS_SMALL'));
        $classData = array_combine(array_column($classData, 'id'), $classData);
        //添加pv
        $imgHelper->pvConfig('pv::invoicePhoto', '小票图片页面');
        $this ->assign(
            [
                'list'       => $data,
                'classData'  => $classData,
                'search'     => $condition['where'],
                'username'   => $condition['username'],
            ]
        );
        return $this->fetch();
    }




}
