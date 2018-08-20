<?php
/**
 * 用户升级
 */

namespace app\admin\controller;

use app\admin\model\UsersUpgrade as U;

class Usersupgrade extends Base
{
    /*列表*/
    public function index()
    {
        return $this->fetch('index');
    }

    /*获取分页*/
    public function pageQuery()
    {
        $u = new U();
        return PQReturn($u->pageQuery());
    }

}