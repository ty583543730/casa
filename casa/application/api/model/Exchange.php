<?php
/**
 * 交易所相关功能
 * User: lj
 * Date: 2018/6/21
 * Time: 上午 9:25
 */

namespace app\api\model;

use think\Db;

class Exchange extends Base
{
    private $privateKey = EXCHANGE_KEY;//私钥
    private $url = EXCHANGE_DOMAIN;

    /**
     * 绑定交易所用户
     * @param $userId               当前用户Id
     * @param $exchangeName         交易所用户名
     * @param $code                 短信验证码
     * @author lj 2018/06/22
     */
    public function bindAccount($param)
    {
        $must = ["exchangeName", "loginPwd", "payPwd"];
        $mustParams = mustParams($must, trimArray($param));
        if (!$mustParams) {
            return SKReturn("必填参数都不能为空");
        }
        $userId = userId();
        //校验用户信息
        /*$exchangeId = Db::name('users')->where('userId', $userId)->value('exchangeId');
        if ($exchangeId)
            return SKReturn('已绑定交易所账户');*/

        $param = ["exchangeName" => $param['exchangeName'], "loginPwd" => $param['loginPwd'], "payPwd" => $param['payPwd']];
        $param_encrypt = SKencrypt(json_encode($param), $this->privateKey);//加密
        $url = $this->url . "/api/Bpay/bindAccount";
        $rs = json_decode(curl_request($url, [], $param_encrypt), true);

        if ($rs['status'] == 1) {
            $rs = Db::name('users')->where('userId', $userId) ->update([ 'exchangeName' =>  $rs['data']['userName'],'exchangeId' => $rs['data']['userId']]);;
            if ($rs !== false)
                return SKReturn('绑定成功', 1);
            return SKReturn('绑定失败');
        } else {
            return $rs;
        }
    }

    /**
     * 获取用户在Bitapex交易所持有数字币信息
     * @param int $userId 当前用户id
     * @author lj
     */
    public function getUserAccount()
    {
        $userId = userId();
        //校验用户信息
        $exchangeId = Db::name('users')->where('userId', $userId)->value('exchangeId');
        if (!$exchangeId)
            return SKReturn('请先绑定交易所账户', -12);
        $account['black'] = Db::name('users_coin')->where(['userId' => $userId, 'coin' => 'WKB'])->value('black');

        $param = ['exchangeId' => $exchangeId];
        $param_encrypt = SKencrypt(json_encode($param), $this->privateKey);
        $url = $this->url . '/api/Bpay/acountCoin';
        $rs = json_decode(curl_request($url, [], $param_encrypt), true);
        if ($rs['status'] == 1)
            $account['available'] = isset($rs['data']['available']) ? $rs['data']['available'] : 0;
        return SKReturn('获取成功', 1, $account);
    }

    /**
     * 用户划拨数字币&交易所
     * @param int $userId 当前用户id
     * @param int $type 1：从当前账户划出到交易所 2：从交易所划出到当前账户
     * @param float $amount 划拨金额
     * @param string $payPwd 支付密码
     * @author lj
     */
    public function transferCoin($param)
    {
        $must = ["type", "amount", "payPwd"];
        $mustParams = mustParams($must, trimArray($param));
        if (!$mustParams) {
            return SKReturn("必填参数都不能为空");
        }
        $userId = userId();
        $type = $param['type'];
        $amount = $param['amount'];
        //校验用户信息
        $exchangeId = Db::name('users')->where('userId', $userId)->value('exchangeId');
        if (!$exchangeId)
            return SKReturn('请先绑定交易所账户', -12);
        $checkPay = checkPwd($userId, $param['payPwd'], 'payPwd');
        if ($checkPay['status'] != 1)
            return SKReturn($checkPay['msg'], $checkPay['status']);

        $users_coin = Db::name('users_coin')->where(['userId' => $userId, 'coin' => 'WKBT'])->find();
        if ($type == 1 && $users_coin['black'] < $amount)
            return SKReturn('可用余额不足', -13);
        //开始划拨操作
        $param = ['exchangeId' => $exchangeId, 'type' => $type, 'num' => $amount];
        $param_encrypt = SKencrypt(json_encode($param), $this->privateKey);
        $url = $this->url . '/api/Bpay/transferCoin';
        $rs = json_decode(curl_request($url, [], $param_encrypt), true);

        //划拨成功 开始处理业务数据
        if ($rs['status'] == 1) {
            $orderNo = SKOrderSn('e');
            $createTime = date('Y-m-d H:i:s');

            $transfer = [
                'coin' => 'CASA',
                'type' => $type,
                'transferNo' => $orderNo,
                'userId' => $userId,
                'total' => $amount,
                'fee' => 0,
                'num' => $amount,
                'ip' => request()->ip(),
                'remark' => '交易所资金划拨',
                'createTime' => $createTime,
            ];
            Db::startTrans();
            try {
                //转账业务表
                Db::name('users_transfer')->insert($transfer);
                if ($type === 1) { //1：从账户划拨到交易所
                    insertLog('CASA', $userId, $orderNo, '12', '-' . $amount, 0, 0, '从账户划拨到交易所');
                    Db::name('users_coin')->where(['userId' => $userId, 'coin' => 'CASA'])->setDec('black', $amount);

                } else { //2：从交易所划拨到账户
                    insertLog('CASA', $userId, $orderNo, '11', $amount, 0, 0, '从交易所划拨到账户');
                    Db::name('users_coin')->where(['userId' => $userId, 'coin' => 'CASA'])->setInc('black', $amount);
                }
                Db::commit();
                return SKReturn('划拨成功', 1);
            } catch (\Exception $e) {
                Db::rollback();
                //接口返回处理成功，此处处理异常则添加记录到异常表
                Db::name('log_error')->insert([
                    'type' => 'CASA_Bitapex',
                    'userId' => $userId,
                    'data' => json_encode(['orderNo' => $orderNo, 'type' => $type, 'amount' => $amount]),
                    'remark' => $e->getMessage(),
                    'addTime' => $createTime
                ]);
                return SKReturn('划拨失败');
            }
        } else {
            return SKReturn($rs['msg']);
        }
    }
}