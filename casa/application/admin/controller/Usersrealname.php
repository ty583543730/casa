<?php
/**
 * 用户实名认证.
 * User: wjj
 * Date: 2018/5/30
 * Time: 16:18
 */

namespace app\admin\controller;

use app\admin\model\UsersRealname as U;

class Usersrealname extends Base
{
    /*人工认证列表*/
    public function index()
    {
        return $this->fetch('index');
    }

    /*获取人工认证分页*/
    public function pageQuery()
    {
        $u = new U();
        return PQReturn($u->pageQuery());
    }

    /*审核处理页面*/
    public function handle()
    {
        $id = input('get.id');
        $u = new U();
        $info = $u->getById($id);
        $this->assign("info", $info);
        return $this->fetch("handle");
    }

    /*人工认证审核*/
    public function toHandle()
    {
        $param = input('post.');
        $u = new U();
        return $u->toHandle($param);
    }
}