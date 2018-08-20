<?php
/**
 * 积分流水
 * User: tuyi
 * Date: 2018/8/2
 */

namespace app\admin\controller;

use app\admin\model\LogScore As L;

class Logscore extends Base
{
    /**
     * 锁仓币释放列表
     * @return mixed|string
     * @author zhouying
     */
    public function index()
    {
        if (request()->isAjax()) {
            $m = new L();
            $rs = $m->pageQuery();
            return PQReturn($rs);
        } else {
            return $this->fetch('index');
        }

    }


}