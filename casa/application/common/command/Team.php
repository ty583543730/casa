<?php
/**
 * 团队业绩奖励
 * User: tiger
 * Date: 2018/8/7
 * Time: 9:31
 */

namespace app\common\command;


use think\console\Command;
use think\console\Input;
use think\console\Output;
use think\Db;
use think\Exception;
use think\Log;
use redis\Native;

class Team extends Command
{

    protected function configure()
    {
        $this->setName('Team')->setDescription('Team');
    }

    protected function execute(Input $input, Output $output)
    {
        //获取交易所所有用户对应的当天消费额 获取得到数组集合
        $redis = new Native();
        $key = "exchangeTurnover:" . date("Ymd", strtotime("-1 day"));
        $redis->hset('aaaaaa', '1000', '592');
        $data = $redis->hgetall('aaaaaa');

        if (empty($data)) {
            $output->writeln('data is empty');
            die;
        }
        //获取所有用户类型及团队业绩参数配置
        $config = Db::name('users_type')->select();
        $userType = [];
        foreach ($config as $k => $v) {
            $userType[$v['id']] = $v;
        }
        //获取所有用户数据，统计每个用户对应的团队业绩奖励
        $users = Db::name('users')->select();
        foreach ($users as $v) {
            $userId = $v['userId'];

            //计算当前用户个人持币数
            $account = Db::name('users_coin')->where(['userId' => $userId, 'coin' => 'CASA'])->field('black,locker,forzen')->find();
            $ownCoin = bcadd(bcadd($account['black'], $account['locker'], 4), $account['forzen'], 4);
            //统计当前用户 -------- 直推团队一级人数
            $sonUserIdOne = Db::name('users')->where(['parentId' => $userId])->column('userId');
            //统计当前用户-------- 团队二级人数
            $sonUserIdTwo = Db::name('users')->where(['parentId' => ['in', $sonUserIdOne]])->column('userId');
            //统计当前用户 -------- 团队三级人数
            $sonUserIdThree = Db::name('users')->where(['parentId' => ['in', $sonUserIdTwo]])->column('userId');
            //得到当前团队人数的userId
            $allUserId = array_merge($sonUserIdOne, $sonUserIdTwo, $sonUserIdThree);
            //统计当前用户团队持币量
            $teamCount = Db::name('users_coin')->field('sum(black) as black,sum(locker) as locker,sum(forzen) as forzen')->where(['userId' => ['in', $allUserId], 'coin' => 'CASA'])->find();
            $teamCoinNum = bcadd(bcadd($teamCount['black'], $teamCount['locker'], 0), $teamCount['forzen'], 0);
            //计算当前用户对应的当天的团队业绩奖励系数
            //当前用户对应的当天的团队消费额
            if (!empty($data[$v['exchangeId']])) {
                $Consumption = $data[$v['exchangeId']];
            } else {
                continue;
            }
            $rate = floor(bcdiv($Consumption, 10000, 1)); //计算额外增加的奖励系数分数

            //计算额外奖励系数
            $addRatio = bcmul($rate, $userType[$v['userType']]['addRatio'], 2);
            //保证额外奖励系数不超过封顶值
            if ($userType[$v['userType']][$v['addTop']] <= $addRatio) {
                $addRatio = $userType[$v['userType']][$v['addTop']];
            }
            //计算基础奖励系数 + 额外奖励系数
            $coefficient = bcadd($userType[$v['userType']][$v['baseRatio']], $addRatio, 2);
            $coefficients = bcdiv($coefficient, 100, 4);

            //计算团队持币量*奖励系数
            $reward = bcmul($teamCoinNum, $coefficients, 4);
            //计算团队持币量*奖励系数 >= 个人持币量
            if ($ownCoin <= $reward) {
                $reward = $ownCoin;
            }

            $rewards[] = [
                'type' => 2,
                'orderNo' => SKOrderSn('t'),
                'userId' => $userId,
                'total' => $teamCoinNum,
                'ratio' => $coefficient,
                'num' => $reward,
            ];
        }

        if (!empty($rewards)) {
            Db::startTrans();
            try {
                $res = BatchRewards($rewards);
                if ($res) {
                    Db::commit();
                    $output->writeln('Team Exe success');
                }
            } catch (Exception $e) {
                Db::rollback();
                Log::logger('error', 'Team Exe error:' . $e->getMessage(), 'shell');
                $output->writeln('Team Exe error:' . $e->getMessage());
            }
        } else {
            $output->writeln('Team rewards is empty');
        }
    }
}