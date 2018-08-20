<?php
/**
 * 平台币种统计.
 * User: wjj
 * Date: 2018/6/6
 * Time: 9:18
 */

namespace app\common\command;


use think\console\Command;
use think\console\Input;
use think\console\Output;
use think\Db;

class CoinCount extends Command
{
    protected function configure()
    {
        $this->setName('CoinCount')->setDescription('币种统计');
    }

    protected function execute(Input $input, Output $output)
    {
        $coin = SKBtcCoin();
        $date = getDateTime('yesterday');
        $todayCount = array();
        $coinCount = array();
        Db::startTrans();
        try {
            foreach ($coin as $k => $v) {
                //充值数据统计
                $todayCount['recharge'][$v['coin']] = Db::name('coin_recharge')->where(['coin'=>$v['coin'],'status'=>2,'createTime'=>['between time',$date]])->sum('num');
                $coinCount['recharge'][$v['coin']] = Db::name('coin_recharge')->where(['coin'=>$v['coin'],'status'=>2])->sum('num');
                //提现数据统计
                $todayCount['draw'][$v['coin']] = Db::name('coin_draw')->where(['coin'=>$v['coin'],'status'=>1,'checkTime'=>['between time',$date]])->sum('num');
                $coinCount['draw'][$v['coin']] = Db::name('coin_draw')->where(['coin'=>$v['coin'],'status'=>1])->sum('num');
                Db::name('date_coins')->insert([
                    'date' => date('Ymd', strtotime("-1 day")),
                    'month' => date('Ym', strtotime("-1 day")),
                    'coin' => $v['coin'],
                    'recharge' => $todayCount['recharge'][$v['coin']],
                    'rechargeTotal' => $coinCount['recharge'][$v['coin']],
                    'draw' => $todayCount['draw'][$v['coin']],
                    'drawTotal' => $coinCount['draw'][$v['coin']]
                ]);
            }
            Db::commit();
            $output->writeln(date("m-d H:i:s").'CoinCount Exe success');
        } catch (\Exception $e) {
            Db::rollback();
            $output->writeln(date("m-d H:i:s").$e->getMessage());
        }
    }
}