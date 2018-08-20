<?php
/**
 * 交易奖励
 * User: ty
 * Date: 2018/8/7
 * Time: 15:42
 */


namespace app\common\command;

use think\console\Command;
use think\console\Input;
use think\console\Output;
use think\Db;
use think\Log;
use think\Exception;
use redis\Native;

class Trade extends Command
{

    protected function configure()
    {
        $this->setName('Trade')->setDescription('交易奖励');
    }

    protected function execute(Input $input, Output $output)
    {
        //获取交易所所有用户对应的当天消费额 获取得到数组集合
        $redis = new Native();
        $key = "exchangeTurnover:" . date("Ymd",strtotime("-1 day"));
        $data_redis = $redis->hgetall($key);

        $turnoverRatio = SysConfig('turnoverRatio');

        //通过交易所id找到钱包内的用户id
        $users = Db::name('users')->field('userId,exchangeId')->where('exchangeId !=0')->select();
        $exchangeId_userId = array_column($users, 'userId', 'exchangeId');

        foreach ($data_redis as $k => $v) {
            if (empty($exchangeId_userId[$k])) {
                Log::logger('uid', '交易所id：' . $k . ' 交易量：' . $v . '没有绑定钱包', 'Conversion');
                continue;
            }

            $total = $v;
            $num = bcmul($total, $turnoverRatio, 4);

            if ($num > 0) {
                $rewards[] = [
                    'type' => 1,
                    'orderNo' => SKOrderSn('j'),
                    'userId' => $exchangeId_userId[$k],
                    'total' => $total,
                    'ratio' => $turnoverRatio,
                    'num' => $num,
                ];
            }
        }

        if (!empty($rewards)) {
            Db::startTrans();
            try {
                $res = BatchRewards($rewards);
                if ($res) {
                    $output->writeln('Trade Exe success');
                }
            }catch (Exception $e){
                Log::logger('error','TradeScore Exe error:'.$e->getMessage(),'shell');
                $output->writeln('Trade Exe error:'.$e->getMessage());
            }
        }else{
            $output->writeln('Trade rewards is empty');
        }
    }
}

