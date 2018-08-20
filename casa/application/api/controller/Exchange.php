<?php
/**
 * 交易所相关
 * User: yt
 * Date: 2018/5/31 0031
 * Time: 上午 9:22
 */

namespace app\api\controller;

use app\api\model\Exchange As EC;

class Exchange extends Base
{
    /**
     * 绑定交易账户
     */
    public function bind($param)
    {
        $result = optBing("ExchangeBind", userId(), 0);
        if (empty($result)) {
            return SKReturn("业务正在处理中，请稍后");
        }
        $ec=new EC();
        $res = $ec->bindAccount($param);
        optBing("ExchangeBind", userId(), 1);
        return $res;
    }

    /**
     *  查询用户交易所账户余额
     */
    public function account()
    {
        $result = optBing("ExchangeAccount", userId(), 0);
        if (empty($result)) {
            return SKReturn("业务正在处理中，请稍后");
        }
        $ec=new EC();
        $res = $ec->getUserAccount();
        optBing("ExchangeAccount", userId(), 1);
        return $res;
    }

    /**
     *  交易所资金划拨
     */
    public function transfer($param)
    {
        $result = optBing("ExchangeTransfer", userId(), 0);
        if (empty($result)) {
            return SKReturn("业务正在处理中，请稍后");
        }
        $ec=new EC();
        $res = $ec->transferCoin($param);
        optBing("ExchangeTransfer", userId(), 1);
        return $res;
    }
}