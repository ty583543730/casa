<?php
/**
 * 用户注册升级统计.
 * User: wjj
 * Date: 2018/6/4
 * Time: 16:52
 */

namespace app\common\command;

use think\console\Command;
use think\console\Input;
use think\console\Output;
use think\Db;

class UserCount extends Command
{
    protected function configure()
    {
        $this->setName('UserCount')->setDescription('用户统计');
    }

    protected function execute(Input $input, Output $output)
    {
        $date = getDateTime('yesterday');
        //当日注册用户量
        $dateCount = Db::name('users')->where(['createTime'=>['between time',$date]])->count('userId');
        //总用户量
        $userCount = Db::name('users')->where(['createTime'=>['<=',$date['1']]])->count();
        //总系统管理员数量
        $xitongCount = Db::name('users')->where(['userType'=>3,'createTime'=>['<=',$date['1']]])->count();
        //当日区长增加数量
        $wardenCount = Db::name('users_upgrade')->where(['createTime'=>['between time',$date],'afterRole'=>2])->count('id');
        //当日管理员增加数量
        $adminCount = Db::name('users_upgrade')->where(['createTime'=>['between time',$date],'afterRole'=>3])->count('id');

        $result = Db::name('date_users')->insert([
            'date' => date('Ymd', strtotime("-1 day")),
            'month' => date('Ym', strtotime("-1 day")),
            'userNum' => $dateCount,
            'userNumTotal' => $userCount,
            'xitongNum' => $adminCount,
            'xitongTotal' => $xitongCount
        ]);

        if ($result !== false){
            $output->writeln('UserCount Exe success');
        }else{
            $output->writeln('UserCount Exe failed');
        }
    }
}