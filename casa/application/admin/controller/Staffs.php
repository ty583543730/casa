<?php
namespace app\admin\controller;
use app\admin\model\Staffs as M;
use app\admin\model\Roles as R;
/**
 * 职员控制器
 */
class Staffs extends Base{
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
     * 获取
     */
    public function get(){
    	$m = new M();
    	return $m->get((int)Input("post.id"));
    }
    /**
     * 跳去编辑页面
     */
    public function toEdit(){
    	$id = (int)Input("get.id",0);
    	$m = new M();
    	$info = $m->getById($id);
    	$this->assign("info",$info);
    	$m = new R();
    	$this->assign("roles",$m->listQuery());
    	return $this->fetch("edit");
    }
    /**
     * 新增
     */
    public function add(){
    	$m = new M();
    	return $m->add();
    }
    /**
     * 编辑菜单
     */
    public function edit(){
    	$m = new M();
    	return $m->edit();
    }
    /**
     * 删除菜单
     */
    public function del(){
    	$m = new M();
    	return $m->del();
    }
    /**
     * 检测账号是否重复
     */
    public function checkLoginKey(){
    	$m = new M();
    	return $m->checkLoginKey(input('post.key'));
    }

    /*修改密码*/
    public function password()
    {
        if (request()->isAjax()) {
            $data=input('post.');

        } else {
            return $this->fetch('password');
        }
    }
    /**
     * 编辑自己密码
     */
    public function editMyPass(){
        if (request()->isAjax()) {
            $m = new M();
            return $m->editMyPass((int)session('sk_staff.staffId'));
        } else {
            return $this->fetch('password');
        }
    }
}
