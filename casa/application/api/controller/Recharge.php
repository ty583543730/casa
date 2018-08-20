<?php
/**
 * app充值相关
 * User: Administrator
 * Date: 2018/5/31 0031
 * Time: 上午 9:22
 */

namespace app\api\controller;

use app\api\model\Recharge as U;

class Recharge extends Base
{
    public function _initialize()
    {
        ISLogin();
    }

    /**
     * 获取充值地址
     * User: tiger
     * Date: 2018/5/8
     */
    public function getRechangeUrl($param)
    {
        $userId = userId();
        $u = new U();
        $res = $u->cRechange($userId, $param['coin']);
        return $res;
    }
}