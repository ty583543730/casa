<?php

namespace app\admin\controller;

use app\admin\model\Menus as M;

/**
 * 菜单控制器
 */
class Menus extends Base
{
    /**
     * 后台菜单列表 yt
     */
    public function index()
    {
        $m = new M();
        $menus = $m->getAllMenus();
        //dump(json_encode($menus));
        $this->assign('menus', json_encode($menus));
        return $this->fetch("index");
    }

    /*新增编辑页面*/
    public function info()
    {
        $ischild = (int)input('ischild', 0);
        $menuid = (int)input('menuid', 0);
        $m = new M();
        if ($ischild == 1) {
            $info = $m->getById(0);
            $this->assign('menuid', $menuid);
        } else {
            $info = $m->getById($menuid);
        }
        $this->assign('info', $info);
        $this->assign('ischild', $ischild);
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
