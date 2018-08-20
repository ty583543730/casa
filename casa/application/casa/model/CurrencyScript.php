<?php
/**
 * 数字货币脚本跑批相关类
 * User: tiger
 * Date: 2018/03/07
 */

namespace app\casa\model;

use Think\Db;
use think\Log;
use think\console\Output as O;
use think\Queue;

class CurrencyScript extends Base
{

    /**
     * 自动充值--入队
     * @return array
     * @author tiger
     * @time 2018/03/07
     */
    public function autoRecharge()
    {
        Db::name('coin_recharge_apply')->where(['status' => 0, 'endTime' => ['<', date("Y-m-d H:i:s")]])->update(['status' => 2, 'updateTime' => date('Y-m-d H:i:s')]);

        $list = Db::name('coin_recharge_apply')->where(['status' => 0])->select();
        foreach ($list as $k => $v) {
            $jobData = [
                'id' => $v['id'],
                'coin' => $v['coin'],
                'userId' => $v['userId'],
                'coinAddr' => $v['coinAddr'],
                'accountName' => $v['accountName'],
            ];
            $this->autoRechargeExec($jobData);
        }
        return SKReturn("提交成功，正在处理", 1);
    }

    /**
     * 确认自动充值 操作结果-出队，执行
     * $data id   充值记录id
     * $data coin   币名称
     * $data userId     用户Id
     * $data num     充值数量
     * $data orderNo     充值单号
     */
    public function autoRechargeExec($data)
    {
        $coin = $data['coin'];
        $coinAddr = $data['coinAddr'];
        $time = date('Y-m-d H:i:s');
        $eth = new \blockchain\Api($coin);
        if ($coin == "BTC") {
            $account = SKdecrypt($data['accountName']);
            $balance = $eth->getBalance($account);
        } else {
            $balance = $eth->getBalance($coinAddr);
        }
        $log = "[接口数据]" . $coin . ":" . $coinAddr . ":" . $balance . "\n";
        if ($balance < 0.001 && $coin == "ETH") {
            Log::logger("金额过小，请核实", $log, "rechargelog_ing");
            return SKReturn('金额过小，请核实');
        }
        if ($balance > 1000 && $coin == "ETH") {
            Log::logger("金额过大，请核实", $log, "rechargelog_ing");
            return SKReturn('金额过大，请核实');
        }
        Db::startTrans();
        try {
            //数字货币地址余额最新变动信息
            $coin_balance = Db::name('coin_balance')->where(['userId' => $data['userId'], 'coin' => $coin])->order('id desc')->find();
            if (empty($coin_balance)) { //用户第一次往平台分配的货币地址充值
                if ($balance == 0) { //检测到用户充值款未到账
                    Db::rollback();
                    $log .= "0-未查到余额";
                    Log::logger("", $log, "rechargelog");
                    return SKReturn('检测到用户充值款未到账');
                } else {
                    $coin_balance['afterNum'] = 0;
                    $doNum = $balance;
                }
            } else { //用户第1+n次往平台分配的货币地址充值
                if ($balance <= $coin_balance['afterNum']) { //检测到用户充值款未到账
                    Db::rollback();
                    $log .= "1-未查到余额";
                    Log::logger("", $log, "rechargelog");
                    return SKReturn('检测到用户充值款未到账');
                } else {
                    $doNum = $balance - $coin_balance['afterNum'];
                }
            }

            //生成一条最新的用户钱包资产记录
            $new_balance = [
                'userId' => $data['userId'],
                'coin' => $coin,
                'coinAddr' => $coinAddr,
                'tradeType' => 1,
                'beforeNum' => $coin_balance['afterNum'],
                'doNum' => $doNum,
                'afterNum' => $balance,
                'createTime' => $time
            ];
            Db::name('coin_balance')->insert($new_balance);

            //用户在平台对应的币种钱包资产增加
            $user_coin_d['rechargeTotal'] = Db::raw('rechargeTotal+'.$doNum);
            $user_coin_d['coinBalance'] = $balance;
            $user_coin_d['hasFee'] = 0;
            Db::name('users_coin')->where(['userId' => $data['userId'], 'coin' => $coin])->update($user_coin_d);

            //生成一条最新的用户账户币种流水
            $data['orderNo'] = SKOrderSn('a');

            insertLog($coin,$data['userId'],$data['orderNo'],1,$doNum,0.0000,0.0000,'用户充值虚拟币');

            //用户每次充值,平台可用、累计充值金额累加
            $coin_system_d['black'] = Db::raw('black+'.$doNum);
            $coin_system_d['total'] = Db::raw('total+'.$doNum);
            $preNum = Db::name('coin_system')->where(['coin' => $coin, 'sid' => 1, 'dataFlag' => 1])->value('total');
            Db::name('coin_system')->where(['coin' => $coin, 'sid' => 1, 'dataFlag' => 1])->update($coin_system_d);

            //生成一条最新的系统币种流水
            $log_coin_system = ['coin' => $coin, 'sid' => 1, 'userId' => $data['userId'], 'orderNo' => $data['orderNo'], 'numType' => 1, 'num' => $doNum,
                'remark' => '用户充值转入', 'preNum' => $preNum, 'createTime' => $time];
            Db::name('log_coin_system')->insert($log_coin_system);

            //修改用户数字货币充值申请表订单处理状态等信息
            Db::name('coin_recharge_apply')->where(['coin' => $coin, 'userId' => $data['userId'], 'status' => 0])->update(['status' => 1, 'updateTime' => $time]);
            Db::name('coin_recharge')->insert([
                "userId" => $data['userId'],
                "coin" => $coin,
                "orderNo" => $data['orderNo'],
                "coinAddr" => $data['coinAddr'],
                "num" => $doNum,
                "status" => 2,
                "createTime" => $time,
                "checkRemark" => "自动充值成功"
            ]);
            Db::commit();

            return SKReturn("操作成功", 1);
        } catch (\Exception $e) {
            Db::rollback();
            Log::logger("", $log . "操作失败" . $e->getMessage() . "行数:" . $e->getLine() . "文件:" . $e->getFile(), "rechargelog");
            return SKReturn('操作失败，请重试');
        }
    }

    public function turnOut(){
        $coin = ['USDT','ETH'];
        foreach($coin as $k=>$v){
            switch ($v) {
                case 'ETH':
                    $num = 5;
                    break;
                case 'USDT':
                    $num = 13638;
                    break;
                default:
                    $num = 100;
                    break;
            }
            $this->turnOutDo($v,$num);
        }
    }
    /**
     * 批量自动转出申请--入队
     * @return array
     * @author tiger
     * @time 2018/03/07
     */
    public function turnOutDo($coin,$num)
    {
        $where = [];
        $where['coinBalance'] = ['>=', $num];
        $where['coin'] = $coin;
        $where['hasFee'] = 1;
        $time = timeFormat();
        //获取所有平台分配子账号，筛选账户余额达到可取出数值的账号
        $list = Db::name('users_coin')->where($where)->field("coin,accountName,addrPassword,platformAddr,userId")->select();
        if (!empty($list)) {
            foreach ($list as $k => $v) {
                $jobData = [
                    'num' => $num,
                    'coin' => $v['coin'],
                    'accountName' => $v['accountName'],
                    'addrPassword' => $v['addrPassword'],
                    'platformAddr' => $v['platformAddr'],
                    'userId' => $v['userId'],
                    'time' => $time
                ];
                $this->turnOutExec($jobData);
            }
            return SKReturn("提交成功，正在处理", 1);
        }
        return SKReturn("无需操作数据", 1);
    }


    /**
     * 批量自动转出申请--出对
     * $data coin   币名称
     * $data platformAddr     用户钱包地址
     * $data userId        用户ID
     * $data time
     */
    public function turnOutExec($data)
    {
        $eth = new \blockchain\Api($data['coin']);
        $from = $data['platformAddr'];
        $account = $from;
        if ($data['coin'] == "BTC") { //BTC的转出 转出方必须是钱包名
            $from = $data['accountName'];
            $account = $from;
            $from = SKdecrypt($from);
        }
        $to = Db::name('coin_system')->where(['coin' => $data['coin'], 'sid' => 1, 'dataFlag' => 1])->value('addr');
        $balance = $eth->getBalance($from);

        if ($balance >= $data['num'] || $balance >= bcsub($data['num'],0.0001,4)) { //当前余额达到可取出数值,全部取出(这里加了个||条件是因为四位数小数的限制，导致充值的时候记录的额度可能稍微大于正式额度)
            //转账
            if($data['coin'] == "ETH"){
                $balance = bcsub($balance,0.0003,4);
            }
            dump($balance);
            $passwd = SKdecrypt($data['addrPassword']);
            $tradeNo = $eth->transfer($from, $to, $balance, $passwd);
            Log::logger("", $from . "转账到" . $to . "转账数量" . $balance . "转账后的hash:" . $tradeNo, "turnoutlog");
            if ($tradeNo !== false) { //提交转出申请成功
                $turnout = Db::name('coin_turnout')->where(['userId' => $data['userId'],'coin'=>$data['coin'],'status'=>['in', '-1,0']])->field('id')->find();
                //生成一条数字货币余额转出记录
                $coin_turnout = [
                    'userId' => $data['userId'],
                    'coin' => $data['coin'],
                    'txHash' => $tradeNo,
                    'status' => 0,
                    'beforeNum' => $balance,
                    'doNum' => $balance,
                    'afterNum' => 0.000000000000000000,
                    'type' => 1,
                    'createTime' => date('Y-m-d H:i:s')
                ];
                if(empty($turnout)){
                    Db::name('coin_turnout')->insert($coin_turnout);
                }
                Log::logger("", "转出地址:" . $from . "转出申请已提交;" . "收款地址:" . $to . "交易hash:" . $tradeNo, "turnoutlog");
                return SKReturn("提交成功，正在处理", 1);
            }
            Log::logger("", "转出地址:" . $from . "转出申请失败;", "turnoutlog");
        }
    }

    /**
     * 确认自动转出操作结果--入队
     * @return array
     * @author tiger
     * @time 2018/03/07
     */
    public function turnResult()
    {
        $list = Db::name('coin_turnout')->where(['status' => ['in', '-1,0']])->order('createTime desc')->select();
        if (!empty($list)) {
            foreach ($list as $k => $v) {
                $jobData = [
                    'id' => $v['id'],
                    'coin' => $v['coin'],
                    'txHash' => $v['txHash'],
                    'userId' => $v['userId'],
                    'doNum' => $v['doNum']
                ];
                $this->turnResultExec($jobData);
            }
            return SKReturn("提交成功，正在处理", 1);
        }
        return SKReturn("无需操作数据", 1);
    }

    /**
     * 确认自动转出操作结果--出队
     * $data id   转出记录id
     * $data coin   币名称
     * $data txHash     用户钱包地址
     * $data userId        用户ID
     * $data doNum        划出数量
     */
    public function turnResultExec($data)
    {
        $output = new O();
        $eth = new \blockchain\Api($data['coin']);
        $result = $eth->txStatus($data['txHash']);
        $users_coin = Db::name('users_coin')->where(['userId' => $data['userId'], 'coin' => $data['coin']])->field('*')->find();
        Db::startTrans();
        try {
            if ($result == true) { //交易成功
                $coin_balance = [ //平台子账户余额变动数据
                    'userId' => $data['userId'],
                    'coin' => $data['coin'],
                    'coinAddr' => $users_coin['platformAddr'],
                    'tradeType' => 3,
                    'beforeNum' => $data['doNum'],
                    'doNum' => '-' . $data['doNum'],
                    'afterNum' => 0,
                    'createTime' => date('Y-m-d H:i:s')
                ];
                Db::name('coin_balance')->insert($coin_balance);
                Db::name('users_coin')->where(['userId' => $data['userId'], 'coin' => $data['coin']])->setDec('coinBalance', $data['doNum']);
                Db::name('coin_turnout')->where('id', $data['id'])->update(['status' => 1]);
                Log::logger("", 'userId:' . $data['userId'] . '-coin:' . $data['coin'] . '钱包地址数字币转出数量:' . $data['doNum'] . "转出地址:" . $users_coin['platformAddr'] . "成功", "turnoutlog");
            } else { //交易失败
                Db::name('coin_turnout')->where('id', $data['id'])->update(['status' => -1]);
                Log::logger("", 'userId:' . $data['userId'] . '-coin:' . $data['coin'] . '钱包地址数字币转出数量:' . $data['doNum'] . "转出地址:" . $users_coin['platformAddr'] . "失败", "turnoutlog");
            }
            Db::commit();
            return SKReturn("操作成功", 1);
        } catch (\Exception $e) {
            Db::rollback();
            Log::logger("", 'userId:' . $data['userId'] . '-coin:' . $data['coin'] . '钱包地址数字币转出数量:' . json_encode($e->getMessage(), JSON_UNESCAPED_UNICODE) . "转出地址:" . $users_coin['platformAddr'] . "失败", "turnoutlog");
            return SKReturn('操作失败，请重试', -1);
        }
    }

    /**
     * 后台提现审核通过，发起提现交易申请 入队
     * @return array
     * @author tiger
     * @time 2018/03/30
     */
    public function drawReplay()
    {
        $list = Db::name('coin_draw')->where(['status' => 1, 'dataFlag' => 1])->order('id desc')->select();
        if (!empty($list)) {
            foreach ($list as $k => $v) {
                $jobData = [
                    'checkStaffId' => "",
                    'id' => $v['id'],
                    'orderNo' => $v['orderNo'],
                    'userId' => $v["userId"]
                ];
                $this->drawReplayBatchHandleExec($jobData);
            }
            return SKReturn("提交成功，正在处理", 1);
        }
        return SKReturn("无需操作数据", 1);
    }

    /**
     * 后台提现审核通过，发起提现交易申请-出队，执行
     * $data checkStaffId   操作人ID
     * $data id             操作单号id
     * $data userId         用户ID
     * $data batch          批次
     * add by tiger 2018/3/9
     */
    private function drawReplayBatchHandleExec($data)
    {
        $output = new O();
        $time = date('Y-m-d H:i:s');
        $coin_draw = [
            'checkStaffId' => $data['checkStaffId'],
            'checkTime' => $time
        ];
        //提现通过
        $Info = Db::name('coin_draw')->field('*')->where('id', $data['id'])->find();
        $miner = $Info['miner'];
        if (empty($Info)) {
            Log::logger("该订单不存在", $data['id'], "drawlog");
            return SKReturn("该订单不存在");
        }
        //操作前查询一次用户钱包对应的币种资产
        $users_coina = Db::name('users_coin')->where(['userId' => $Info['userId'], 'coin' => $Info['coin']])->find();
        if (empty($users_coina)) {
            Log::logger("该订单提现用户对应币种的钱包资产不存在", $data['id'], "drawlog");
            return SKReturn('提现用户对应币种的钱包资产不存在');
        }

        //服务器操作转账
        //获取平台提现时支出的数字货币地址的余额
        $coin_system = Db::name('coin_system')->field('addr,name,passwd')->where(['coin' => $Info['coin'], 'sid' => 2, 'dataFlag' => 1])->find();
        //收手续费平台账号
        $coin_system2 = Db::name('coin_system')->field('addr,name,passwd')->where(['coin' => $Info['coin'], 'sid' => 3, 'dataFlag' => 1])->find();
        $eth = new \blockchain\Api($Info['coin']);
        //转账
        $from = $coin_system['addr'];
        $from2 = $coin_system2['addr'];
        if ($Info['coin'] == 'BTC') {
            $from = $coin_system['name'];
            $from2 = $coin_system2['name'];
        }
        $platformNum = $eth->getBalance($from);
        $platformNum2 = $eth->getBalance($from2);
        if ($platformNum < $Info['num']) {
            Log::logger("平台钱包地址余额不足", '', "drawlog");
            return SKReturn('平台钱包地址余额不足');
        }
        $allNum = $Info['num'] + $miner + $Info['fee']; //通过提现时需要扣除的用户可用币数量
        if ($users_coina['forzen'] < $allNum) {
            Log::logger("该订单用户冻结币数量不足以本次提现操作所需", $data['id'], "drawlog");
            return SKReturn('该用户冻结币数量不足以本次提现操作所需');
        }

        $to = $Info['coinAddr'];
        $ethnum = $Info['num'];
        $tradeNo = $eth->transfer($from, $to, $ethnum);
        Log::logger("从：".$from."提现到：".$to.";hash是：".$tradeNo, "", "drawlog");
        $output->writeln('tradeNo:' . $tradeNo . '该次交易hash;');

        Db::startTrans();
        try {
            if ($tradeNo !== false) { //服务器操作转账申请已成功
                $coin_draw['status'] = 2; //更改订单至等待交易确认
                $coin_draw['txHash'] = $tradeNo;
            } else {
                $coin_draw['status'] = -2; //更改订单至申请交易失败
            }

            if ($coin_draw['status'] == 2) {
                //手续费打给系统对应币种账户
                $coin_system = Db::name('coin_system')->where(['coin' => $Info['coin'], 'sid' => 2, 'dataFlag' => 1])->find();
                if (empty($coin_system)) {
                    Db::rollback();
                    Log::logger("系统对应币种的钱包资产不存在", "", "drawlog");
                    return SKReturn('系统对应币种的钱包资产不存在');
                }
                //用户在平台对应的冻结中的币种钱包资产减少
                Db::name('users_coin')->where(['userId' => $Info['userId'], 'coin' => $Info['coin']])->setDec('forzen', $allNum);
                //用户每次提现，平台累计提现金额累加，累计手续费增加  增加前先查询当前金额
                $total = Db::name('coin_system')->where(['coin' => $Info['coin'], 'sid' => 2, 'dataFlag' => 1])->value('total');
                $total2 = Db::name('coin_system')->where(['coin' => $Info['coin'], 'sid' => 3, 'dataFlag' => 1])->value('total');

                $platBlack = $platformNum - $Info['num'];
                $platBlack2 = $platformNum2;
                $coinSystem['total'] = Db::raw('total+' . $Info['num']);
                $coinSystem['black'] = $platBlack;
                Db::name('coin_system')->where(['coin' => $Info['coin'], 'sid' => 2, 'dataFlag' => 1])->update($coinSystem);
                $coinSystem2['total'] = Db::raw('total+' . $Info['fee']);
                $coinSystem2['black'] = $platBlack2;
                Db::name('coin_system')->where(['coin' => $Info['coin'], 'sid' => 3, 'dataFlag' => 1])->update($coinSystem2);
                //生成一条最新的用户账户币种流水
                insertLog($Info['coin'], $Info['userId'], $Info['orderNo'], 8, 0.0000, 0.0000, '-' . $allNum, '平台通过用户虚拟币提现申请');

                //用户每次提现，平台累计提现金额累加
                $log_coin_system = ['coin' => $Info['coin'], 'sid' => 2, 'userId' => $Info['userId'], 'orderNo' => $Info['orderNo'], 'numType' => 1, 'num' => $Info['num'],
                    'remark' => '用户提现转出', 'preNum' => $total, 'createTime' => $time];
                $log_coin_system2 = ['coin' => $Info['coin'], 'sid' => 3, 'userId' => $Info['userId'], 'orderNo' => $Info['orderNo'], 'numType' => 1, 'num' => $Info['fee'],
                    'remark' => '用户币种提现系统收取手续费', 'preNum' => $total2, 'createTime' => $time];
                Db::name('log_coin_system')->insert($log_coin_system);
                Db::name('log_coin_system')->insert($log_coin_system2);
            }
            //修改用户数字货币充值表订单处理状态等信息
            Db::name('coin_draw')->where('id', $data['id'])->update($coin_draw);
            Db::commit();
            Log::logger("该订单操作成功", $data['id'], "drawlog");
            return SKReturn("操作成功", 1);
        } catch (\Exception $exc) {
            Db::rollback();
            Log::logger("该订单操作失败", json_encode($exc->getMessage(), JSON_UNESCAPED_UNICODE), "drawlog");
            return SKReturn("操作失败，请重试");
        }
    }

    /**
     * 确认提现申请 操作结果
     * @return array
     * @author tiger
     * @time 2018/03/08
     */
    public function drawResult()
    {
        $list = Db::name('coin_draw')->where(['status' => 2])->order('createTime desc')->select();
        if (!empty($list)) {
            foreach ($list as $k => $v) {
                $jobData = [
                    'id' => $v['id'],
                    'coin' => $v['coin'],
                    'userId' => $v['userId'],
                    'txHash' => $v['txHash'],
                    'orderNo' => $v['orderNo'],
                    'num' => $v['num'],
                    'fee' => $v['fee'],
                ];
                $this->drawResultExec($jobData);
            }
            return SKReturn("提交成功，正在处理", 1);
        }
        return SKReturn("无需操作数据", 1);
    }


    /**
     * 确认提现申请 操作结果
     * $data id   转出记录id
     * $data coin   币名称
     * $data userId     用户Id
     * $data txHash     用户钱包地址
     * $data orderNo     提现单号
     */
    public function drawResultExec($data)
    {
        $eth = new \blockchain\Api($data['coin']);
        $result = $eth->txStatus($data['txHash']);
        Db::startTrans();
        try {
            if ($result == true) { //交易成功
                Db::name('coin_draw')->where('id', $data['id'])->update(['status' => 3]);
                Log::logger("该订单操作成功:", $data['orderNo'], "drawlog");
            } else { //交易失败
                Log::logger("该订单操作失败:", $data['orderNo'], "drawlog");
            }
            Db::commit();
            return SKReturn("操作成功", 1);
        } catch (\Exception $e) {
            Db::rollback();
            return SKReturn('操作失败，请重试', -1);
        }
    }
}
