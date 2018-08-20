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

class AccountCopy extends Command
{
    protected function configure()
    {
        $this->setName('AccountCopy')->setDescription('Account Copy');
    }

    protected function execute(Input $input, Output $output)
    {
        //00:00把账户表拷贝等到临时表中 凌晨优先级最高
        $sql='INSERT INTO sk_check_tmp_tusercoin ( userId, coin, date, block,`lock`, forzen )( SELECT userId, coin, DATE_FORMAT( DATE_ADD(NOW(), INTERVAL - 1 DAY), "%Y%m%d" ) AS date, black, locker, forzen FROM sk_users_coin );';
        debug('begin');
        Db::execute("truncate sk_check_tmp_tusercoin");
        $num = Db::execute($sql);
        $logarr = [];
        $logarr["lasting"] = debug('begin', 'end', 6) . "s";
        $logarr["memory"] = debug('begin', 'end', 'm');
        $logarr["time"] = date('Y-m-d H:i:s');
        $logarr["res"] = '已把账户数据复制到对账临时表中' . $num . '条数据';
        Log::logger('insert sk_check_tmp_tusercoin', JSONReturn($logarr), 'accountcopy');
    }
}