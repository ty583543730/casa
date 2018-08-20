<?php
/**
 * 挖矿算法
 * User: tuyi
 * Date: 2018/8/3
 * Time: 17:00
 */

namespace app\common\command;

use think\console\Command;
use think\console\Input;
use think\console\Output;
use think\Db;
use think\Exception;
use think\Log;

class Miner extends Command
{

    protected function configure()
    {
        $this->setName('Miner')->setDescription('挖矿');
    }

    protected function execute(Input $input, Output $output)
    {
        $users = Db::name('users_extend')->field('userId,computingPower as cp')->select();
        //总算力
        $totalCP = Db::name('users_extend')->sum('computingPower');
        //每天最大释放
        $coinMaxAday = bcdiv(SysConfig('circulationCoinYear') * 0.5,365,0);

        $num = ceil(count($users) / 500);
        for ($i = 1; $i <= $num; $i++) {
            $data = array_splice($users, 0, 500);
            foreach ($data as $v) {
                $num = bcdiv($coinMaxAday * $v['cp'], $totalCP, 4);
                if ($num > 0) {
                    $userId = $v['userId'];
                    $coin = Db::name('users_coin')->where(['userId' => $userId, 'coin' => 'CASA'])->field('black,locker,forzen')->find();

                    $LogCoin[] = [
                        'userId' => $userId,
                        'orderNo' => SKorderSn('w'),
                        'type' => 10,
                        'preCoin' => $coin['black'],
                        'preCoinLock' => $coin['locker'],
                        'preCoinFrozen' => $coin['forzen'],
                        'coinBlack' => $num,
                        'coinLock' => 0,
                        'coinFrozen' => 0,
                        'remark' => '挖矿奖励',
                        'createTime' => date("Y-m-d H:i:s")
                    ];
                    $CoinData[] = [$userId, 'CASA', bcadd($coin['black'], $num, 4)];
                }
            }
            Db::startTrans();
            try {
                if (!empty($LogCoin)) {
                    Db::name('log_casa')->insertAll($LogCoin);
                    BatchUpdate('sk_users_coin', ['userId', 'coin', 'black'], $CoinData);
                }
                Db::commit();
            } catch (Exception $e) {
                Db::rollback();
                Log::logger('error', 'Miner Exe error:' . $e->getMessage(), 'shell');
                $output->writeln('Miner Exe error:' . $e->getMessage());
            }
        }
        $output->writeln('Miner Exe success');
    }
}