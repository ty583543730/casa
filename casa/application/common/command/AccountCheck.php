<?php
/**
 * 对账脚本
 * User: yt
 * Date: 2018/6/4 0004
 * Time: 下午 2:36
 */

namespace app\common\command;

use think\console\Command;
use think\console\Input;
use think\console\Output;
use think\Db;
use think\Log;

class AccountCheck extends Command
{
    protected function configure()
    {
        $this->setName('AccountCheck')->setDescription('Account Checking');
    }

    protected function execute(Input $input, Output $output)
    {
        //所有的币种
        $coins = Db::name('coin')->where(['dataFlag' => 1, 'status' => 1])->column('coin');
        Db::execute("truncate sk_check_tmp_log");
        foreach ($coins as $v) {
            $coin = strtolower($v);
            //日志统计
            $sql = "INSERT INTO sk_check_tmp_log (userId,coin,blockChange,lockChange,forzenChange) (select userId,'$v'as coin,sum(coinBlack),sum(coinLock),sum(coinFrozen) from sk_log_" . $coin . " WHERE createTime BETWEEN DATE_FORMAT(DATE_SUB(NOW(), INTERVAL 1 DAY),'%Y-%m-%d 00:00:00') AND DATE_FORMAT( NOW(), '%Y-%m-%d 00:00:00' )  group by userId);";
            debug('begin');
            $num = Db::execute($sql);
            $logarr = [];
            $logarr["lasting"] = debug('begin', 'end', 6) . "s";
            $logarr["memory"] = debug('begin', 'end', 'm');
            $logarr["time"] = date('Y-m-d H:i:s');
            $logarr["res"] = $coin . '流水日志汇总 ' . $num . '条数据';
            Log::logger('insert sk_check_tmp_log', JSONReturn($logarr), 'AccountCheck');
        }

        // 用户统计
        $sql = 'INSERT INTO sk_check_detail (date,month,userId,coin,blockChange,lockChange,forzenChange) (SELECT DATE_FORMAT( DATE_ADD(NOW(), INTERVAL - 1 DAY), "%Y%m%d" ) AS date, DATE_FORMAT(NOW(), "%Y%m") AS month, userId,coin,blockChange,lockChange,forzenChange FROM sk_check_tmp_log);';
        debug('begin');
        $logarr = [];
        $num = Db::execute($sql);
        $logarr["lasting"] = debug('begin', 'end', 6) . "s";
        $logarr["memory"] = debug('begin', 'end', 'm');
        $logarr["time"] = date('Y-m-d H:i:s');
        $logarr["res"] = '临时数据插入到用户统计表中 ' . $num . '条数据';
        Log::logger('insert sk_check_detail', JSONReturn($logarr), 'AccountCheck');

        $sql = 'UPDATE sk_check_detail a, ( SELECT t.userId, t.coin, t.block AS tblock, IFNULL(y.block, 0.0000) AS yblock, IFNULL(l.blockChange, 0.0000) AS blockChange, t.`lock` AS tlock, IFNULL(y.`lock`, 0.0000) AS ylock, IFNULL(l.lockChange, 0.0000) AS lockChange, t.forzen AS tforzen, IFNULL(y.forzen, 0.0000) AS yforzen, IFNULL(l.forzenChange, 0.0000) AS forzenChange FROM sk_check_tmp_tusercoin t LEFT JOIN sk_check_tmp_yusercoin y ON y.userId = t.userId AND y.coin = t.coin LEFT JOIN sk_check_tmp_log l ON l.userId = t.userId AND l.coin = t.coin ) AS sub SET a.block = sub.tblock, a.`lock` = sub.tlock, a.forzen = sub.tforzen, a.blockDiff = sub.tblock - sub.yblock - sub.blockChange, a.lockDiff = sub.tlock - sub.ylock - sub.lockChange, a.forzenDiff = sub.tforzen - sub.yforzen - sub.forzenChange WHERE a.userId = sub.userId AND a.coin = sub.coin AND a.`date` = DATE_FORMAT( DATE_ADD(NOW(), INTERVAL - 1 DAY), "%Y%m%d" );';
        debug('begin');
        $num = Db::execute($sql);
        $logarr = [];
        $logarr["lasting"] = debug('begin', 'end', 6) . "s";
        $logarr["memory"] = debug('begin', 'end', 'm');
        $logarr["time"] = date('Y-m-d H:i:s');
        $logarr["res"] = '临时数据更新到用户统计表中 ' . $num . '条数据';
        Log::logger('update sk_check_detail', JSONReturn($logarr), 'AccountCheck');

        //平台统计
        $sql = 'insert into sk_check_result(date,month,coin,block,`lock`,forzen,blockDiff,lockDiff,forzenDiff,blockChange,lockChange,forzenChange)(select date,month,coin,sum(block),sum(`lock`),sum(forzen),sum(blockDiff), sum(lockDiff),sum(forzenDiff),sum(blockChange), sum(lockChange),sum(forzenChange) from sk_check_detail where `date`=DATE_FORMAT( DATE_ADD(NOW(), INTERVAL - 1 DAY), "%Y%m%d" )  GROUP BY coin);';
        debug('begin');
        $num = Db::execute($sql);
        $logarr = [];
        $logarr["lasting"] = debug('begin', 'end', 6) . "s";
        $logarr["memory"] = debug('begin', 'end', 'm');
        $logarr["time"] = date('Y-m-d H:i:s');
        $logarr["res"] = '系统统计表插入 ' . $num . '条数据';
        Log::logger('insert sk_check_result', JSONReturn($logarr), 'AccountCheck');

        //把数据插入到昨天账户临时表中留作备用
        debug('begin');
        Db::execute("truncate sk_check_tmp_yusercoin");
        $num = Db::execute('INSERT INTO sk_check_tmp_yusercoin select * from sk_check_tmp_tusercoin;');
        debug('end');
        $logarr = [];
        $logarr["lasting"] = debug('begin', 'end', 6) . "s";
        $logarr["memory"] = debug('begin', 'end', 'm');
        $logarr["time"] = date('Y-m-d H:i:s');
        $logarr["res"] = '已把数据插入到昨天账户临时表中留作备用' . $num . '条数据';
        Log::logger('insert sk_check_tmp_yusercoin', JSONReturn($logarr), 'AccountCheck');
    }
}