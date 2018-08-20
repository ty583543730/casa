<?php
/**
 * 系统消息
 * User: yt
 * Date: 2018/5/29 0029
 * Time: 上午 10:07
 */

namespace app\admin\controller;

use app\admin\model\SysMsgs as M;

class Sysmsgs extends Base
{
    /**
     * 列表
     */
    public function index()
    {
        return $this->fetch('index');
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
     * 详情
     */
    public function info()
    {
        $id = input('get.id/d', 0);
        if ($id == 0) {
            echo '没有id！';
        }
        $m = new M();
        $info = $m->getInfo($id);
        $this->assign('info', $info);
        return $this->fetch('info');
    }

    /**
     * 删除
     */
    public function del()
    {
        $m = new M();
        return $m->del();
    }
}