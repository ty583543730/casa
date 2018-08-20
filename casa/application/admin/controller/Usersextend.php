<?php
/**
 * 用户扩展
 * User: tuyi
 * Date: 2018/8/13
 */

namespace app\admin\controller;

use app\admin\model\Usersextend As U;

class Usersextend extends Base
{
    /**
     * 锁仓币释放列表
     * @return mixed|string
     * @author zhouying
     */
    public function index()
    {
        if (request()->isAjax()) {
            $m = new U();
            $rs = $m->pageQuery();
            return PQReturn($rs);
        } else {
            return $this->fetch('index');
        }


    }

    public function changeScore()
    {
        if (request()->isAjax()) {
            $m = new U();
            return $m->changeScore();
        } else {
            return $this->fetch('usersextend/edit');
        }

    }

    public function checkUser()
    {
        $m = new U();
        return $m->checkUser();
    }


}
