<?php

namespace app\admin\controller;

use app\admin\model\Privileges as M;

/**
 * 权限控制器
 */
class Privileges extends Base
{
    /*新增修改页面*/
    public function index()
    {
        $id = (int)input("id", 0);
        $menuid = (int)input("menuid", 0);
        if ($menuid == 0 && $id == 0) {
            return 'menuid或id不能为0';
        }
        $m = new M();
        $info = $m->getById($id);
        $this->assign('info', $info);
        $this->assign('menuid', $menuid);
        return $this->fetch('edit');
    }

    /**
     * 获取权限列表
     */
    public function listQuery()
    {
        $m = new M();
        return PQReturn(['data' => $m->listQuery((int)Input("id"))]);
    }

    /**
     * 获取权限
     */
    public function get()
    {
        $m = new M();
        return $m->getById((int)Input("id"));
    }

    /**
     * 新增权限
     */
    public function add()
    {
        $m = new M();
        return $m->add();
    }

    /**
     * 编辑权限
     */
    public function edit()
    {
        $m = new M();
        return $m->edit();
    }

    /**
     * 删除权限
     */
    public function del()
    {
        $m = new M();
        return $m->del();
    }

    /**
     * 检测权限代码是否存在
     */
    public function checkPrivilegeCode()
    {
        $m = new M();
        return $m->checkPrivilegeCode();
    }

    /**
     * 获取角色的权限
     */
    public function listQueryByRole()
    {
        $m = new M();
        return $m->listQueryByRole((int)Input("id"));
    }
}
