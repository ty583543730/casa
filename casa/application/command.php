<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: yunwuxin <448901948@qq.com>
// +----------------------------------------------------------------------

return [
    //采集各种币的价格
    'app\common\command\CoinmarketcapPrice',
    'app\common\command\AccountCopy',
    'app\common\command\AccountCheck',

    'app\common\command\CoinCount',
    'app\common\command\TransCount',
    'app\common\command\UserCount',

    'app\yingmai\command\WalletCheck',
    'app\yingmai\command\PurseAddress',

    //用户升级
    'app\common\command\UserUPgrade',

    //锁仓币转可用币
    'app\common\command\Conversion',
    //挖矿
    'app\common\command\Miner',
    //新增业绩奖励
    'app\common\command\Entrance',
    //团队业绩奖励
    'app\common\command\Team',
    //交易奖励
    'app\common\command\Trade',
    //定期储蓄奖励
    'app\common\command\Savings',
];
