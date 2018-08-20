<?php

namespace app\admin\controller;

use app\admin\model\ArticleCats as M;

/**
 * 文章分类控制器
 */
class ArticleCats extends Base
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
     * 获取列表
     */
    public function listQuery()
    {
        $m = new M();
        $rs = $m->listQuery(input('parentId/d', 0));
        return $rs;
    }

    /**
     * 设置是否显示/隐藏
     */
    public function editiIsShow()
    {
        $m = new M();
        $rs = $m->editiIsShow();
        return $rs;
    }

    /**
     * 获取文章分类
     */
    public function get()
    {
        $m = new M();
        $rs = $m->getById((int)Input("post.id"));
        return $rs;
    }

    /**
     * 获取文章分类列表
     */
    public function getAllArtCats()
    {
        $m = new M();
        $rs = $m->getAllArtCats();
        return $rs;
    }

    /**
     * 跳去新增/编辑页面
     */
    public function toEdit()
    {
        $id = Input("get.id/d", 0);
        $m = new M();
        $object = $m->getById($id);
        $this->assign('object', $object);
        $Ac = new M();
        $this->assign('articlecatList', $Ac->getAllArtCats());
        return $this->fetch("articlecats/edit");
    }

    /**
     * 新增
     */
    public function add()
    {
        $m = new M();
        $rs = $m->add();
        return $rs;
    }

    /**
     * 编辑
     */
    public function edit()
    {
        $m = new M();
        $rs = $m->edit();
        return $rs;
    }

    /**
     * 删除
     */
    public function del()
    {
        $m = new M();
        $rs = $m->del();
        return $rs;
    }
}
