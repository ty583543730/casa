<?php

namespace app\admin\controller;

use app\admin\model\Roles as M;

/**
 * 角色控制器
 */
class Roles extends Base
{

    public function index()
    {
        return $this->fetch("index");
    }

    /**
     * 获取分页
     */
    public function pageQuery()
    {
        $m = new M();
        return PQReturn($m->pageQuery());
    }

    /**
     * 获取菜单
     */
    public function get()
    {
        $m = new M();
        return $m->get((int)Input("post.id"));
    }

    /**
     * 新增编辑页面
     */
    public function toEdit()
    {
        $m = new M();
        $info = $m->getById((int)Input("get.id"));
        $this->assign("info", $info);
        return $this->fetch("edit");
    }

    /**
     * 新增菜单
     */
    public function add()
    {
        $m = new M();
        return $m->add();
    }

    /**
     * 编辑菜单
     */
    public function edit()
    {
        $m = new M();
        return $m->edit();
    }

    /**
     * 删除菜单
     */
    public function del()
    {
        $m = new M();
        return $m->del();
    }
}
