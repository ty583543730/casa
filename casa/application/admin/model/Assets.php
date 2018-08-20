<?php

namespace app\admin\model;

use think\Db;

/**
 * 数字货币资产类
 * User: tiger
 * Date: 2018/5/10
 */
class Assets extends Base
{
    protected $rechargeType = [-1 => '审核拒绝', 0 => '审核中', 1 => '审核通过', 2 => '自动审核通过'];
    protected $turnoutType = [1 => '虚拟币划出'];
    /**
     * 数字货币资产交易流水
     * @return array
     * @author tiger
     * @time 2018/05/10
     */
    public function detailPageQuery()
    {
        $where = [];
        $where['l.dataFlag'] = 1;
        if (input('userPhone')) {
            $where['u.userPhone'] = ['like', input('userPhone') . '%'];
        }
        $coin = 'casa';
        if(input('coin')){
            $coin = strtolower(input('coin'));
        }
        if (input('type') && input('type') != -1) {
            $where['l.type'] = input('type');
        }
        //起止时间
        if (input('startTime') || input('endTime')) {
            $where['l.createTime'] = timeTerm(input('startTime'), input('endTime'), 2);
        }
        $page = Db::name('log_' . "$coin")->alias('l')
            ->join('users u', 'u.userId=l.userId', 'left')
            ->field('u.userPhone,orderNo,type,preCoin,preCoinLock,coinBlack,coinLock,remark,l.createTime')
            ->where($where)
            ->order('l.id', 'desc')
            ->paginate(input('limit/d'))
            ->toArray();
        if (count($page['data']) > 0) {
            foreach ($page['data'] as $k => $v) {
                $logCoin = getLogTradeList("log_coin", true);
                $page['data'][$k]['type'] = $logCoin[$v['type']];
                $page['data'][$k]['coin'] = strtoupper($coin);
                $page['data'][$k]['prenum'] = $page['data'][$k]['preCoin'] + $page['data'][$k]['preCoinLock'];
                $page['data'][$k]['donum'] = $page['data'][$k]['coinBlack'] + $page['data'][$k]['coinLock'];
            }
        }
        return $page;
    }
    /**
     * 系统钱包交易流水
     * @return array
     * @author tiger
     * @time 2018/05/10
     */
    public function detailSysPageQuery()
    {
        $where = [];
        if (input('sid') && input('sid') != -1) {
            $where['sid'] = input('sid');
        }
        if (input('coin')) {
            $where['coin'] = input('coin');
        }
        //起止时间
        if (input('startTime') || input('endTime')) {
            $where['createTime'] = timeTerm(input('startTime'), input('endTime'), 2);
        }
        $page = Db::name('log_coin_system')
            ->field('id,dataFlag',true)
            ->where($where)
            ->order('id', 'desc')
            ->paginate(input('limit/d'))
            ->toArray();
        if (count($page['data']) > 0) {
            foreach ($page['data'] as $k => $v) {
                $logCoin = getLogTradeList("log_coin_system", true);
                $page['data'][$k]['numType'] = "减少";
                if($v['numType'] == 1){
                    $page['data'][$k]['numType'] = "增加";
                }
                $page['data'][$k]['sid'] = $logCoin[$v['sid']];
            }
        }
        return $page;
    }


    /**
     * 虚拟币转入分页
     * @return array
     * @author tiger
     * @time 2018/03/01
     */
    public function changeInPageQuery()
    {
        $where = [];
        $where['l.dataFlag'] = 1;
        if (input('userPhone')) {
            $where['u.userPhone'] = ['like', input('userPhone') . '%'];
        }
        if (input('coin') && input('coin') != -1) {
            $where['l.coin'] = input('coin');
        }
        //起止时间
        if (input('startTime') || input('endTime')) {
            $where['l.createTime'] = timeTerm(input('startTime'), input('endTime'), 2);
        }
        $page = Db::name('coin_recharge')->alias('l')
            ->join('users u', 'u.userId=l.userId', 'left')
            ->field('u.userPhone,l.orderNo,l.coin,l.num,l.status,l.createTime,l.checkRemark')
            ->where($where)
            ->order('l.id', 'desc')
            ->paginate(input('limit/d'))
            ->toArray();
        if (count($page['data']) > 0) {
            foreach ($page['data'] as $k => $v) {
                $page['data'][$k]['status'] = $this->rechargeType[$v['status']];
            }
        }
        return $page;
    }

    /**
     * 钱包地址余额变动记录分页
     * @return array
     * @author tiger
     * @time 2018/04/13
     */
    public function balancePageQuery()
    {
        $where = [];
        if (input('userPhone')) {
            $where['u.userPhone'] = ['like', input('userPhone') . '%'];
        }
        if (input('coin')) {
            $where['l.coin'] = input('coin');
        }
        if (input('type')) {
            $where['l.tradeType'] = input('type');
        }
        //起止时间
        if (input('startTime') || input('endTime')) {
            $where['l.createTime'] = timeTerm(input('startTime'), input('endTime'), 2);
        }
        $page = Db::name('coin_balance')->alias('l')
            ->join('users u', 'u.userId=l.userId' ,'left')
            ->field('u.userPhone,l.*')
            ->where($where)
            ->order('l.id', 'desc')
            ->paginate(input('limit/d'))
            ->toArray();
        if (count($page['data']) > 0) {
            foreach ($page['data'] as $k => $v) {
                $coinBalance = getLogTradeList("coin_balance", true);
                $page['data'][$k]['tradeType'] = $coinBalance[$v['tradeType']];
            }
        }
        return $page;
    }

    /**
     * 钱包地址余额变动记录分页
     * @return array
     * @author tiger
     * @time 2018/04/13
     */
    public function turnoutPageQuery()
    {
        $where = [];
        if (input('userPhone')) {
            $where['u.userPhone'] = ['like', input('userPhone') . '%'];
        }
        if (input('coin')) {
            $where['l.coin'] = input('coin');
        }
        if (input('type')) {
            $where['l.status'] = input('type');
        }
        //起止时间
        if (input('startTime') || input('endTime')) {
            $where['l.createTime'] = timeTerm(input('startTime'), input('endTime'), 2);
        }
        $page = Db::name('coin_turnout')->alias('l')
            ->join('users u', 'u.userId=l.userId' ,'left')
            ->field('u.userPhone,l.*')
            ->where($where)
            ->order('l.id', 'desc')
            ->paginate(input('limit/d'))
            ->toArray();
        if (count($page['data']) > 0) {
            foreach ($page['data'] as $k => $v) {
                $coinBalance = getLogTradeList("coin_turnout", true);
                $page['data'][$k]['status'] = $coinBalance[$v['status']];
                $page['data'][$k]['type'] = $this->turnoutType[$v['type']];
            }
        }
        return $page;
    }

    /**
     * 资金流水详情
     * @return array
     * @author tiger
     * @time 2018/05/10
     */
    public function getLogInfo($coin,$orderNo){
        $res = Db::name('log_' . "$coin")->alias('l')
            ->join('users u', 'u.userId=l.userId', 'left')
            ->field('u.userPhone,u.userName,orderNo,type,preCoin,preCoinFrozen,preCoinLock,coinBlack,coinFrozen,coinLock,remark,l.createTime')
            ->where(['orderNo'=>$orderNo])
            ->find();

        $logCoin = getLogTradeList("log_coin", true);
        $res['type'] = $logCoin[$res['type']];
        $res['coin'] = $coin;
        return $res;
    }
}
