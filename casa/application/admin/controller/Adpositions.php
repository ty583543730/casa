<?php
namespace app\admin\controller;
use app\admin\model\AdPositions as M;
/**

 * 广告位置控制器
 */
class Adpositions extends Base{
	
    public function index(){
    	return $this->fetch("index");
    }
    /**
     * 获取分页
     */
    public function pageQuery(){
        $m = new M();
        return PQReturn($m->pageQuery());
    }
    /**
     * 跳去编辑页面
     */
    public function toEdit(){
        $m = new M();
        $assign = ['info'=>$m->getById(Input("get.id/d",0))];
        return $this->fetch("edit",$assign);
    }
    /*
    * 获取数据
    */
    public function get(){
        $m = new M();
        return $m->getById(Input("id/d",0));
    }
    /**
     * 新增
     */
    public function add(){
        $m = new M();
        return $m->add();
    }
    /**
    * 修改
    */
    public function edit(){
        $m = new M();
        return $m->edit();
    }
    /**
     * 删除
     */
    public function del(){
        $m = new M();
        return $m->del();
    }
}
