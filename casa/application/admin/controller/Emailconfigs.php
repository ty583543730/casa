<?php
/**
 * 邮件配置
 * User: sky
 * Date: 2018/6/20
 * Time: 14:23
 */

namespace app\admin\controller;

use app\admin\model\EmailConfigs as M;

class Emailconfigs extends Base
{
    /**
     * 列表
     */
    public function index()
    {
        return $this->fetch("list");
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
     * 跳去新增/编辑页面
     */
    public function toEdit()
    {
        $id     = Input("get.id/d", 0);
        $m      = new M();
        $object = $m->getById($id);
        $this->assign('object', $object);
        return $this->fetch("edit");
    }

    /**
     * 新增
     */
    public function add()
    {
        $m  = new M();
        $rs = $m->add();
        return $rs;
    }

    /**
     * 编辑
     */
    public function edit()
    {
        $m  = new M();
        $rs = $m->edit();
        return $rs;
    }

    /**
     * 删除
     */
    public function del()
    {
        $m  = new M();
        $rs = $m->del();
        return $rs;
    }
}