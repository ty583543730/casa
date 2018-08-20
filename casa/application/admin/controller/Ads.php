<?php

namespace app\admin\controller;

use app\admin\model\Ads as M;
use app\admin\model\AdPositions as AP;

/**
 * 广告控制器
 */
class Ads extends Base
{

    public function index()
    {
        //获取所有广告位
        $ap = new AP();
        $this->assign('data', $ap->getAllAdPositions());
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
     * 跳去编辑页面
     */
    public function toEdit()
    {
        //获取所有广告位
        $ap = new AP();
        $this->assign('adPositions', $ap->getAllAdPositions());
        $m = new M();
        $object = $m->getById(Input("id/d", 0));
        $this->assign('object', $object);
        return $this->fetch("edit");
    }

    /*
    * 获取数据
    */
    public function get()
    {
        $m = new M();
        return $m->getById(Input("id/d", 0));
    }

    /**
     * 新增
     */
    public function add()
    {
        $m = new M();
        return $m->add();
    }

    /**
     * 修改
     */
    public function edit()
    {
        $m = new M();
        return $m->edit();
    }

    /**
     * 删除
     */
    public function del()
    {
        $m = new M();
        return $m->del();
    }

    /**
     * 修改广告排序
     */
    public function changeSort()
    {
        $m = new M();
        return $m->changeSort();
    }
}
