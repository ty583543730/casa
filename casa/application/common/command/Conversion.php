<?php
/**
 * 锁仓币转可用币
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
use redis\Native;

class Conversion extends Command
{

    protected function configure()
    {
        $this->setName('Conversion')->setDescription('锁仓币转可用币');
    }

    protected function execute(Input $input, Output $output)
    {
        //获取交易所所有用户对应的当天消费额 获取得到数组集合
        $redis = new Native();
        $key = "exchangeTurnover:" . date("Ymd",strtotime("-1 day"));
        $data_redis = $redis->hgetall($key);

        //通过交易所id找到钱包内的用户id
        $users = Db::name('users')->field('userId,exchangeId')->where('exchangeId !=0')->select();
        $exchangeId_userId = array_column($users, 'userId', 'exchangeId');

        //释放配置
        $releaseConfig = Db::name('release_config')->select();

        $num = ceil(count($data_redis) / 500);
        for ($i = 1; $i <= $num; $i++)
        {
            $data = array_splice($data, 0, 500);

            foreach ($data as $k => $v) {
                $uid = $exchangeId_userId[$k];
                if (empty($uid)) {
                    Log::logger('uid', '交易所id：' . $k . ' 交易量：' . $v . '没有绑定钱包', 'Conversion');
                    continue;
                }

                //直推人数
                $recommendNum = Db::name('users_extend')->where(['userId' => $uid])->value('recommendNum');
                if ($recommendNum < SysConfig('releaseMin')) {
                    Log::logger('recommendNum', '用户id：' . $uid . ' 直推人数：' . $recommendNum . ' 小于系统设置不能释放锁仓币', 'Conversion');
                    continue;
                }

                $coin = Db::name('users_coin')->where(['userId' => $uid, 'coin' => 'CASA'])->field('id,black,locker,forzen')->find();
                $ownCoin = $coin['black'] + $coin['locker'] + $coin['forzen'];

                foreach ($releaseConfig as $kk => $vv) {
                    if ($ownCoin >= $vv['min'] && $ownCoin <= $vv['max']) {
                        $baseRatio = $vv['baseRatio'];
                        $addRatio = $vv['addRatio'] * floor($v / 10000) < $vv['addTop'] ? $vv['addRatio'] * floor($v / 10000) : $vv['addTop'];
                    }
                }

                if (empty($baseRatio) || empty($addRatio)) {
                    Log::logger('releaseConfig', '用户id：' . $uid . ' 持币量：' . $ownCoin . '没有对应的释放配置', 'Conversion');
                    continue;
                }
                $releaseNum = bcmul($ownCoin, $baseRatio + $addRatio, 4);
                if($releaseNum>0){
                    $insertLogData[] = [
                        'userId' => $uid,
                        'orderNo' => SKOrderSn('d'),
                        'type' => 7,
                        'preCoin' => $coin['black'],
                        'preCoinLock' => $coin['locker'],
                        'preCoinFrozen' => $coin['forzen'],
                        'coinBlack' => $releaseNum,
                        'coinLock' => '-' . $releaseNum,
                        'coinFrozen' => 0.000,
                        'remark' => '释放锁仓币',
                        'createTime' => date("Y-m-d H:i:s")
                    ];

                    $update[] = [$coin['id'],bcadd($coin['black'],$releaseNum,4),bcsub($coin['locker'],$releaseNum,4)];
                }
            }

            Db::startTrans();
            try {
                Db::name('log_casa')->insertAll($insertLogData);
                BatchUpdate('sk_users_coin', ['id','black','locker'], $data);
                Db::commit();
            } catch (Exception $e) {
                Db::rollback();
                Log::logger('releaseConfig', '数据：' . JSONReturn($insertLogData) . ' 插入失败，失败原因：' . $e->getMessage(), 'Conversion');
                $output->writeln('Conversion Exe fail');
            }
        }
        $output->writeln('Conversion Exe success');
    }

}