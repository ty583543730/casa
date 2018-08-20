<?php

namespace app\admin\controller;

use app\admin\model\CoinDraws as M;
use think\Db;
/**
 * 用户提现控制器
 */
class Coindraws extends Base
{
    /*
    * 提币列表
    */
    public function index()
    {
        if (request()->isAjax()) {
            $a = new M();
            $rs = $a->pageQuery();
            return PQReturn($rs);
        } else {
            $this->assign('coinList', SKBtcCoin());
            return $this->fetch('index');
        }
    }

    /**
     * 虚拟币审核业务处理
     * @return array
     * @author tiger
     * @time 2018/03/09
     */
    public function drawSetStatus()
    {
        $m = new M();
        return $m->drawSetStatus();
    }
}
