<?php
/**
 * 分佣奖励.
 * User: wjj
 * Date: 2018/6/1
 * Time: 9:09
 */

namespace app\admin\controller;

use app\admin\model\Rewards as R;

class Rewards extends Base
{
    /*分佣奖励列表*/
    public function index()
    {
        return $this->fetch('index');
    }

    /**
     * 分佣奖励分页
     */
    public function pageQuery()
    {
        $r = new R();
        return PQReturn($r->pageQuery());
    }

    /*奖励详情*/
    public function info()
    {
        $id = input('get.id/d', 0);
        $r = new R();
        $info = $r->getInfo($id);
        $this->assign('info', $info);
        return $this->fetch('info');
    }
}