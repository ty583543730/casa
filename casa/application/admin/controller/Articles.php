<?php

namespace app\admin\controller;

use app\admin\model\Articles as M;
use app\admin\model\ArticleCats as Ac;

/**
 * 文章控制器
 */
class Articles extends Base
{
    public function index()
    {
        $Ac = new Ac();
        $dataCat = $Ac->getAllArtCats();
        $this->assign('data', $dataCat);
        return $this->fetch("index");
    }

    /**
     * 获取分页
     */
    public function pageQuery()
    {
        $m = new M();
        return PQReturn( $m->pageQuery());
    }

    /**
     * 获取文章
     */
    public function get()
    {
        $m = new M();
        $rs = $m->get(Input("post.id/d", 0));
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
        $Ac = new Ac();
        $this->assign('articlecatList', $Ac->getAllArtCats());
        return $this->fetch("edit");
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
