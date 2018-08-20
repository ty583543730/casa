<?php
/**
 * 数字货币资产类
 * User: tiger
 * Date: 2018/5/10
 */


namespace app\admin\controller;

use app\admin\model\Assets as A;

class Assets extends Base
{
    /**
     * 数字货币资产交易流水
     * @return array
     * @author tiger
     * @time 2018/05/10
     */
    public function logCoin()
    {
        if (request()->isAjax()) {
            $a = new A();
            $rs = $a->detailPageQuery();
            return PQReturn($rs);
        } else {
            $logCoin = getLogTradeList("log_coin",false);
            $this->assign('logCoin',$logCoin);
            $this->assign('coinList', SKBtcCoin());
            return $this->fetch('assets/index');
        }
    }

    /**
     * 资金流水详情
     * @return array
     * @author tiger
     * @time 2018/05/10
     */
    public function logInfo(){
        $coin = input('coin');
        $orderNo = input('orderNo');
        if (empty($coin) || empty($orderNo)) {
            echo '参数不全！';
            die;
        }
        $a = new A();
        $info = $a->getLogInfo($coin,$orderNo);
        $this->assign('info', $info);
        return $this->fetch('logInfo');
    }

    /**
     * 虚拟币转入页面
     * @return array
     * @author tiger
     * @time 2018/03/01
     */
    public function changeIn()
    {
        if (request()->isAjax()) {
            $a = new A();
            $rs = $a->changeInPageQuery();
            return PQReturn($rs);
        } else {
            $this->assign('coinList', SKBtcCoin());
            return $this->fetch('assets/changein');
        }
    }

    /**
     * 钱包地址余额变动记录页面
     * @return array
     * @author tiger
     * @time 2018/04/13
     */
    public function balanceList()
    {
        if (request()->isAjax()) {
            $a = new A();
            $rs = $a->balancePageQuery();
            return PQReturn($rs);
        } else {
            $coinBalance = getLogTradeList("coin_balance",false);
            $this->assign('coinBalance',$coinBalance);
            $this->assign('coinList', SKBtcCoin());
            return $this->fetch('assets/balancelist');
        }
    }

    /**
     * 钱包地址余额变动记录页面
     * @return array
     * @author tiger
     * @time 2018/04/13
     */
    public function turnoutList()
    {
        if (request()->isAjax()) {
            $a = new A();
            $rs = $a->turnoutPageQuery();
            return PQReturn($rs);
        } else {
            $coinTurnout = getLogTradeList("coin_turnout",false);
            $this->assign('coinTurnout',$coinTurnout);
            $this->assign('coinList', SKBtcCoin());
            return $this->fetch('assets/turnoutlist');
        }
    }

    /**
     * 系统钱包交易流水
     * @return array
     * @author tiger
     * @time 2018/05/10
     */
    public function logSysCoin()
    {
        if (request()->isAjax()) {
            $a = new A();
            $rs = $a->detailSysPageQuery();
            return PQReturn($rs);
        } else {
            $logCoin = getLogTradeList("log_coin_system",false);
            $this->assign('coinList', SKBtcCoin());
            $this->assign('logCoin',$logCoin);
            return $this->fetch('assets/indexsys');
        }
    }

}