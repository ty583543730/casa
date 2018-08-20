<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/5/31 0031
 * Time: 上午 9:22
 */

namespace app\api\controller;

use think\Db;
use app\api\model\Draw as U;

class Draw extends Base
{

    public function _initialize()
    {
        ISLogin();
    }

    /**
     * 提现界面
     * @author tiger 2018/5/9
     * @param $coin 要提现币的英文简称
     */
    public function draw($param)
    {
        $u = new U();
        $userId = userId();
        if (!empty($param['coin'])) {
            $users_coin = Db::name("users_coin")->field("coin,black,drawType")->where(['coin' => $param['coin'], 'userId' => $userId])->find();
            if (empty($users_coin)) {
                return SKReturn("不存在对应的资产账户");
            }
            if ($users_coin['drawType'] == -1) {
                return SKReturn("该用户暂不能做提现操作");
            }
            $zcFee = SysConfig('Fee');
            $address = $u->lists(1, $userId, $param['coin']);
        } else {
            return SKReturn("币种类型不能为空");
        }
        $data = [
            "coin" => $users_coin['coin'],
            "black" => $users_coin['black'],
            "zcFee" => $zcFee ? $zcFee : 0,
        ];
        $res = [];
        $res['data'] = $data;
        $res['address'] = $address;
        return SKReturn("查询成功",1,$res);
    }

    /**
     * 虚拟币提现
     * @author tiger 2018/05/9
     * @param $coin 币种
     * @param $num 数量
     * @param $addr 提现地址
     * @param $payPwd 交易密码
     * @param $userId 用户Id
     */
    public function drawSubmit($param)
    {
        $result = optBing("drawSubmit", userId(), 0);
        if (empty($result)) {
            return SKReturn("业务正在处理中，请稍后");
        }
        $u = new U();
        $res = $u->draw($param);
        optBing("drawSubmit", userId(), 1);
        return $res;
    }
}