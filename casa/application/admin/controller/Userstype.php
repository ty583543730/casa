<?php
/**
 * 用户类型
 * User: tuyi
 * Date: 2018/8/9
 */

namespace app\admin\controller;

use app\admin\model\UsersType As U;

class Userstype extends Base
{
    /**
     * 用户类型列表
     * @return mixed|string
     * @author tuyi
     */
    public function index()
    {
        if (request()->isAjax()) {
            $u = new U();
            $data = $u->getAllTypes();
            return PQReturn($data);
        }
        return $this->fetch("index");
    }

    /**
     * 获取单条信息
     */
    public function get()
    {
        $m = new U();
        $rs = $m->getOne();
        $this->assign('data', $rs);
        return $this->fetch('userstype/edit');
    }

    /**
     * 跳去新增/编辑页面
     */
    public function edit()
    {

        $m = new U();
        $res = $m->edit();
        return $res;
    }

    /**
     * 新增
     */
    public function add()
    {
        $m = new U();
        $rs = $m->add();
        return $rs;
    }


    /**
     * 删除
     */
    public function del()
    {
        $m = new U();
        $rs = $m->del();
        return $rs;
    }


}