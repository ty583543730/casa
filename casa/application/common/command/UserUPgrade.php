<?php
/**
 * 用户升级
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

class UserUPgrade extends Command
{

    protected function configure()
    {
        $this->setName('UserUPgrade')->setDescription('UserUPgrade');
    }

    protected function execute(Input $input, Output $output)
    {
        //获取所有用户类型及团队业绩参数配置
        $config = Db::name('users_type')->select();
        $userType = [];
        foreach ($config as $k => $v) {
            $userType[$v['id']] = $v;
        }

        //获取所有用户数据，处理达标可升级的用户
        $users = Db::name('users')->where('userType != 4')->select();
        foreach ($users as $v) {
            $userId = $v['id'];
            //获取当前用户的用户类型及对应的端对业绩参数配置
            $param = $userType[$v['userType']];

            //计算当前用户个人持币数
            $account = Db::name('users_coin')->where(['userId' => $userId, 'coin' => 'CASA'])->field('black,locker，forzen')->find();
            $ownCoin = bcadd(bcadd($account['black'], $account['locker'], 4), $account['forzen'], 4);

            //判断当前用户个人持币数是否达标
            if ($ownCoin >= $param['num']) {

                //统计当前用户直推人数是否达标 -------- 直推团队一级人数
                $sonUserIdOne = Db::name('users')->where(['parentId' => $userId])->column('userId');
                if (count($sonUserIdOne) >= $param['recommend']) {
                    //统计当前用户直推人数是否达标 -------- 团队二级人数
                    $sonUserIdTwo = Db::name('users')->where(['parentId' => ['in', $sonUserIdOne]])->column('userId');
                    //统计当前用户直推人数是否达标 -------- 团队三级人数
                    $sonUserIdThree = Db::name('users')->where(['parentId' => ['in', $sonUserIdTwo]])->column('userId');
                    //最终得到当前团队人数
                    $allNum = count($sonUserIdOne) + count($sonUserIdTwo) + count($sonUserIdThree);

                    if ($allNum >= $param['team']) {
                        $allUserId = array_merge($sonUserIdOne, $sonUserIdTwo, $sonUserIdThree);

                        //统计当前用户团队持币量
                        $teamCount = Db::name('users_coin')->field('sum(black) as black,sum(locker) as locker,sum(forzen) as forzen')->where(['userId' => ['in', $allUserId], 'coin' => 'CASA'])->find();
                        $teamCoinNum = bcadd(bcadd($teamCount['black'], $teamCount['locker'], 0), $teamCount['forzen'], 0);

                        if ($teamCoinNum >= $param['teamCoinNum']) {
                            //当前用户身份是经销商/代理商 -----  要直推2个经销商/代理商
                            if ($v['userType'] == 2 || $v['userType'] == 3) {
                                $identityNUm = Db::name('users')->where(['parentId' => $userId, 'userType' => $v['userType']])->count();
                                if ($identityNUm >= 2) {
                                    Db::name('users')->where(['userId' => $userId])->setInc('userType');
                                }
                            } else if ($v['userType'] == 1) {
                                Db::name('users')->where(['userId' => $userId])->setInc('userType');
                            }
                        }
                    }
                }

            } else {
                continue;
            }

        }
    }
}