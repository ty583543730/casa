<?php
/**
 * 储蓄
 * User: yt
 * Date: 2018/8/1
 * Time: 17:37
 */

namespace app\api\controller;

use app\api\model\Savings as M;

class Savings extends Base
{
    /*列表*/
    public function pageQuery($param)
    {
        $m = new M();
        return $m->pageQuery($param);
    }

    /*详情*/
    public function info($param)
    {
        $m = new M();
        return $m->info($param);
    }

    /*进入储蓄页面取参*/
    public function getSavings($param)
    {
        $m = new M();
        return $m->getSavings();
    }

    /*储蓄提交*/
    public function submit($param)
    {
        $result = optBing("SavingsSubmit", userId(), 0);
        if (empty($result)) {
            return SKReturn("业务正在处理中，请稍后");
        }
        $u = new M();
        $res = $u->submit($param);
        optBing("SavingsSubmit", userId(), 1);
        return $res;
    }
}