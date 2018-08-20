<?php
/**
 * 平台订单统计.
 * User: wjj
 * Date: 2018/6/5
 * Time: 15:32
 */

namespace app\common\command;


use think\console\Command;
use think\console\Input;
use think\console\Output;
use think\Db;

class TransCount extends Command
{
    protected function configure()
    {
        $this->setName('TransCount')->setDescription('订单统计');
    }

    protected function execute(Input $input, Output $output)
    {
        $time1 = date('Y-m-d 00:00:00', strtotime("-1 day"));
        $time2 = date('Y-m-d 23:59:59', strtotime("-1 day"));
        $sql = 'INSERT INTO sk_date_transfers (transferNum,transferCoin,paymentNum,paymentCoin,transferNumLock,transferCoinLock,paymentNumLock,paymentCoinLock,date,month) (SELECT count(case type when 1 then id else null end) as transferNum,
                sum(case type when 1 then total else 0 end) as transferCoin,
                count(case type when 2 then id else null end) as paymentNum,
                sum(case type when 2 then total else 0 end ) as paymentCoin,
                count(case coinType when 1 then id else null end) as transferNumLock,
                sum(case coinType when 1 then total else 0 end) as transferCoinLock,
                count(case coinType when 2 then id else null end) as paymentNumLock,
                sum(case coinType when 2 then total else 0 end) as paymentCoinLock,
                DATE_FORMAT( DATE_ADD(NOW(), INTERVAL - 1 DAY), "%Y%m%d" ) as date,
                DATE_FORMAT( DATE_ADD(NOW(), INTERVAL - 1 DAY), "%Y%m" ) as month
                from sk_transfers WHERE createTime BETWEEN "' . $time1 . '" and "' . $time2 . '");';
        $num = Db::execute($sql);
        if ($num) {
            $id = Db::name('date_transfers')->getLastInsID();
            $transCount = Db::name('date_transfers')
                ->field('sum(transferNum) transferNumTotal,sum(transferCoin) transferCoinTotal,sum(paymentNum) paymentNumTotal,sum(paymentCoin) paymentCoinTotal,sum(transferNumLock) transferNumTotalLock,sum(transferCoinLock) transferCoinTotalLock,sum(paymentNumLock) paymentNumTotalLock,sum(paymentCoinLock) paymentCoinTotalLock')->find();
            $result = Db::name('date_transfers')->where(['id' => $id,'date'=>date('Ymd', strtotime("-1 day"))])->update([
                'transferNumTotal' => $transCount['transferNumTotal'],
                'transferCoinTotal' => $transCount['transferCoinTotal'],
                'paymentNumTotal' => $transCount['paymentNumTotal'],
                'paymentCoinTotal' => $transCount['paymentCoinTotal'],
                'transferNumTotalLock' => $transCount['transferNumTotalLock'],
                'transferCoinTotalLock' => $transCount['transferCoinTotalLock'],
                'paymentNumTotalLock' => $transCount['paymentNumTotalLock'],
                'paymentCoinTotalLock' => $transCount['paymentCoinTotalLock']
            ]);
            if ($result !== false) {
                $output->writeln('TransCount Exe success');
            } else {
                $output->writeln('TransCount Exe failed');
            }
        } else {
            $output->writeln('TransCount Exe failed');
        }
    }
}