<?php
/**
 * 用户转账付款
 * User: Administrator
 * Date: 2018/5/30 0030
 * Time: 下午 4:27
 */

namespace app\admin\controller;
use app\admin\model\Transfers as M;

class Transfers extends Base
{
    /*列表*/
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
        $id =input('get.id/d',0);
        if($id ==0){
            echo '没有id！';
        }
        $m = new M();
        $info =$m->getInfo($id);
        $this->assign('info',$info);
        return $this->fetch('info');
    }
}