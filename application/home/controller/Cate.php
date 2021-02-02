<?php
namespace app\home\controller;

use think\Config;
use think\cache\driver\Redis;
use app\home\model\Goods as GoodsModel;
use app\home\controller\Helps\imgHelper as imgHelper;


/**
 * 分类管理控制器
 * @author duxinxin
 * @date 2020/04/26
 */

class Cate extends Common
{

    /**
     * 分类列表
     *
     * @access index
     * @author duxinxin
     * @date 2020/04/26
     */
    public function index()
    {

        $imgHelper = new imgHelper();
        $condition = $imgHelper->productBuild_cate($this->request->get(), Config::get('STATE_YES'));
        $GoodsModel = new GoodsModel();
        $data =  $GoodsModel->classPage($condition);

        $classData = $imgHelper->getParam();

        //添加pv
        $imgHelper->pvConfig('pv::cateList', '分类列表');

        $this ->assign(
            [
                'list'       => $data,
                'classData'  => $classData,
                'search'     => $condition['where'],
                'classname'   => $condition['classname'],
            ]
        );
        return $this->fetch();
    }

    /**
     * 分类数据添加
     *
     * @access add
     * @author duxinxin
     * @date 2020/04/26
     */
    public function add(){

        if ($this->request->isPost()){

            $postData = $this->request->post();

            $Data = [

                'classname' => $postData['classname'],

                'diff'      => $postData['class'],

                'addtime'   => time(),

            ];

            $GoodsModel = new GoodsModel();
            $data =  $GoodsModel->insertClass($Data);

            if($data) {
                return $this->success('保存成功', '');
            }else{
                return $this->error('保存失败', '');
            }

        }else{

            $imgHelper = new imgHelper();
            $classData = $imgHelper->getParam();

            //添加pv
            $imgHelper->pvConfig('pv::cateAdd', '分类添加页面');
            $this ->assign([
                'classData'  => $classData
            ]);
            return $this->fetch();
        }
    }

    /**
     * 分类删除操作
     *
     * @access del
     * @author duxinxin
     * @date 2020/04/26
     */
    public function del(){

        $id = $this->request->get('id');
        $GoodsModel = new GoodsModel();
        $where = [
            'state' => Config::get('STATE_NO')
        ];
        $data =  $GoodsModel->editClass($id, $where);

        if($data) {
            return $this->success('删除成功', 'home/cate/index');
        }else{
            return $this->error('删除失败', 'home/cate/index');
        }
    }

    /**
     * 分类恢复操作
     *
     * @access recover
     * @author duxinxin
     * @date 2020/04/26
     */
    public function recover(){

        $id = $this->request->get('id');
        $GoodsModel = new GoodsModel();
        $where = [
            'state' => Config::get('STATE_YES')
        ];
        $data =  $GoodsModel->editClass($id, $where);

        if($data) {
            return $this->success('恢复成功', 'home/cate/cateBin');
        }else{
            return $this->error('恢复失败', 'home/cate/cateBin');
        }
    }

    /**
     * 分类编辑操作
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

                    'classname'   => $postData['classname'],

                    'diff'        => $postData['diff'],

                    'update_date' => time(),

                ];

            $GoodsModel = new GoodsModel();
            $data =  $GoodsModel->editClass($id, $Data);

            if($data) {
                return $this->success('修改成功', '');
            }else{
                return $this->error('修改失败', '');
            }

        }else{

            $id = $this->request->get('id');
            $imgHelper = new imgHelper();
            $classData = $imgHelper->getParam();
            $GoodsModel = new GoodsModel();
            $data =  $GoodsModel->classFind(['id' => $id]);
            //添加pv
            $imgHelper->pvConfig('pv::cateEdit', '分类编辑页面');
            $this ->assign([
                'classData'  =>  $classData,
                'data'       => $data
            ]);
            return $this->fetch();
        }

    }

    /**
     * 分类回收站列表
     *
     * @access cateBin
     * @author duxinxin
     * @date 2020/04/26
     */
    public function cateBin(){

        $imgHelper = new imgHelper();
        $condition = $imgHelper->productBuild_cate($this->request->get(), Config::get('STATE_NO'));

        $GoodsModel = new GoodsModel();
        $data =  $GoodsModel->classPage($condition);

        $classData = $imgHelper->getParam();
        //添加pv
        $imgHelper->pvConfig('pv::cateBin', '分类回收站页面');
        $this ->assign(
            [
                'list'       => $data,
                'classData'  => $classData,
                'search'     => $condition['where'],
                'classname'   => $condition['classname'],
            ]
        );
        return $this->fetch();
    }

}
