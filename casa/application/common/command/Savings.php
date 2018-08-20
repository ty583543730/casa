<?php
/**
 * 静态复投
 * User: tiger
 * Date: 2018/8/7
 * Time: 9:31
 */

namespace app\common\command;

use think\console\Command;
use think\console\Input;
use think\console\Output;
use think\Db;
use think\Log;
use think\Exception;

class Savings extends Command
{
    protected function configure()
    {
        $this->setName('Savings')->setDescription('Savings');
    }

    protected function execute(Input $input, Output $output)
    {
        $where = [];
        $betweenTime = getDateTime('yesterday');
        $where['endTime'] = ['between time', $betweenTime];
        $where['status'] = ['in', '1,2'];
        $where['dataFlag'] = 1;
        $savingsReturnRatio = SysConfig('savingsReturnRatio'); //复投奖励归还比例

        $LogCoin = [];
        $savings = [];
        //查询静态复投到期的数据进行处理
        $data = Db::name('savings')->where($where)->select();
        if (empty($data)) {
            $output->writeln('Savings rewards is empty');
            die;
        }
        foreach ($data as $v) {
            $orderNo = $v['orderNo'];
            $userId = $v['userId'];

            ////归还本金
            if ($v['status'] == 1) {
                $coin = Db::name('users_coin')->where(['userId' => $userId, 'coin' => 'CASA'])->field('black,locker,forzen')->find();
                if (is_null($coin['black'])) {
                    Log::logger('error', '用户' . $userId . '用户账户不存在', 'rewards');
                    continue;
                }

                $LogCoin[] = [
                    'userId' => $userId,
                    'orderNo' => $orderNo,
                    'type' => 12,
                    'preCoin' => $coin['black'],
                    'preCoinLock' => $coin['locker'],
                    'preCoinFrozen' => $coin['forzen'],
                    'coinBlack' => $v['num'],
                    'coinLock' => 0,
                    'coinFrozen' => 0,
                    'remark' => '定储本金退返',
                    'createTime' => date("Y-m-d H:i:s")
                ];
                $CoinData[] = [$userId, 'CASA', bcadd($coin['black'], $v['num'], 4)];
            }

            //计算奖励积分
            $total = bcmul(bcmul($v['num'], $v['ratio'], 2), $v['marketValue'], 2);

            //最后一次奖励剩余笔记小于等于 系统设置比例。 组织复投表数据
            if ($savingsReturnRatio >= $v['surplusRatio']) {
                $savingsReturnRatio = $v['surplusRatio'];
                $savings[] = [$v['id'], 3, 0];
            } else {
                $savings[] = [$v['id'], 2, bcsub($v['surplusRatio'], $savingsReturnRatio, 2)];
            }

            $num = bcmul($total, $savingsReturnRatio, 4);
            if ($num > 0) {
                $rewards[] = [
                    'type' => 4,
                    'orderNo' => SKOrderSn('f'),
                    'userId' => $userId,
                    'total' => $total,
                    'ratio' => $savingsReturnRatio,
                    'num' => $num,
                ];
            }
        }
        Db::startTrans();
        try {
            if (empty($savings)) {
                $output->writeln('Savings rewards is empty');
                die;
            }
            //更新复投表数据
            BatchUpdate('sk_savings', ['id', 'status', 'surplusRatio'], $savings);

            if (!empty($LogCoin)) {
                Db::name('log_casa')->insertAll($LogCoin);
                BatchUpdate('sk_users_coin', ['userId', 'coin', 'black'], $CoinData);
            }
            if (!empty($rewards)) {
                $res = BatchRewards($rewards);
                if ($res) {
                    $output->writeln('Savings Exe success');
                }
            } else {
                $output->writeln('Savings rewards is empty');
            }
            Db::commit();
        } catch (Exception $e) {
            Db::rollback();
            Log::logger('error', 'Savings Exe error:' . $e->getMessage(), 'shell');
            $output->writeln('Savings Exe error:' . $e->getMessage());
        }

    }
}