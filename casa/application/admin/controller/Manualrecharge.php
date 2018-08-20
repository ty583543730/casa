<?php
/**
 * 数字币手动充值
 * User: tiger
 * Date: 2018/5/17 0023
 * Time: 下午 2:08
 */

namespace app\admin\controller;

use app\admin\model\ManualReharge As C;
use think\Db;

class Manualrecharge extends Base
{
    /**
     * 数字币手动充值
     * User: tiger
     * Date: 2018/7/02
     */
    public function manualRecharge()
    {
        if (request()->isAjax()) {
            $m = new C();
            return $m->manualRecharge();
        } else {
            $res = Db::name('coin')->field('coin')->select();
            $this->assign('coin', $res);
            return $this->fetch('manualrecharge/manualrecharge');
        }
    }

    /**
     * 检测用户是否存在
     * User: tiger
     */
    public function checkUser()
    {
        $m = new C();
        return $m->checkUser();
    }
}