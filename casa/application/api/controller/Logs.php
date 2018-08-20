<?php
/**
 * 流水日志
 * User: yt
 * Date: 2018/8/8
 * Time: 下午 2:37
 */

namespace app\api\controller;

use app\api\model\Logs as L;
class Logs extends Base
{
    public function _initialize()
    {
        ISLogin();
    }

    /**
     * 币流水记录
     * @author：yt 2018/6/1
     */
    public function coin ($param) {
        $m = new L();
        $result = $m->coin($param);
        return $result;
    }

    /**
     * 币记录详情
     * @author：yt 2018/6/1
     */
    public function coininfo ($param) {
        $m = new L();
        $result = $m->coininfo($param);
        return $result;
    }

    /**
     * 积分流水记录
     * @author：yt 2018/6/1
     */
    public function score ($param) {
        $m = new L();
        $result = $m->score($param);
        return $result;
    }

    /**
     * 积分流水记录
     * @author：yt 2018/6/1
     */
    public function scoreinfo ($param) {
        $m = new L();
        $result = $m->scoreinfo($param);
        return $result;
    }

}