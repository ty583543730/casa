<?php

namespace app\casa\model;

use think\Db;
use think\Log;
use app\common\model\Base;
use think\console\Output as O;

/**
 * 数字货币定时业务处理
 */
class BlockChain extends Base
{
    /**
     * @Description  检测不同币种钱包地址数量是否告急(数量小于等于100为告急) 每半小时执行一次
     * @DateTime    2018-03-26
     */
    public function newAccountV1($coin)
    {
        $output = new O();
        $time = date('Y-m-d H:i:s');
        if ($coin !== '') { //平台发布新币种时分配对应的钱包地址
            $eth = new \blockchain\Api($coin);
            if ($eth->News == false) { //判断是否有币种服务器
                return SKReturn(lang("server not exist"));
            }
            for ($i = 0; $i < 100; $i++) {
                $addrPassword = substr(uniqid(), 4, 16);
                $accountName = $coin . rand() . rand(1000, 9999);
                $platformAddr = $eth->getNewAddress($addrPassword, $accountName);
                $addrPassword = encrypt($addrPassword);

                if (!$platformAddr) { //若链接不上服务器，中断操作
                    break;
                }
                $array = [
                    'coin' => $coin,
                    'accountName' => $accountName,
                    'addrPassword' => $addrPassword,
                    'platformAddr' => $platformAddr,
                    'createTime' => $time
                ];
                Db::name('coin_purse_address')->insert($array);
            }
            $output->writeln($time . "币种" . $coin . "钱包地址已批量生成");
        } else { //检测不同币种钱包地址数量是否告急
            $res = Db::name('coin_purse_address')->field('coin,count(*) as num')->group('coin')->select();
            foreach ($res as $k => $v) {
                if ($v['num'] <= 100) {
                    $this->newAccount($v['coin']);
                }
            }
        }
        return SKReturn(lang("New wallet address success"), 1);
    }

    public function newAccount()
    {
        $output = new O();
        $time = date('Y-m-d H:i:s');

        $coinList = Db::name('coin')->field('coin,isCurrency,superiorId')->where('ownHost', 1)->select();
        foreach ($coinList as $item) {
            //只生成货币的钱包地址
            if($item['isCurrency'] == 1){
                $coin = $item["coin"];
                $num = Db::name('coin_purse_address')->where('coin', $coin)->value('count(id) as num');
                if ($num > 100) {
                    continue;
                }
                $isDo = false;
                $eth = new \blockchain\Api($coin);
                for ($i = 0; $i < 100; $i++) {
                    $addrPassword = substr(uniqid(), 4, 16);
                    $accountName = $coin . rand() . rand(1000, 9999);

                    $platformAddr = $eth->getNewAddress($addrPassword, $accountName);
                    $addrPassword = SKencrypt($addrPassword);
                    $accountName = SKencrypt($accountName);
                    if (!$platformAddr) { //若链接不上服务器，中断操作
                        $log = $coin . "节点服务器连接失败 \n";
                        Log::logger("", $log, "address");
                        continue;
                    }
                    $array = [
                        'coin' => $coin,
                        'accountName' => $accountName,
                        'addrPassword' => $addrPassword,
                        'platformAddr' => $platformAddr,
                        'createTime' => $time
                    ];
                    Db::name('coin_purse_address')->insert($array);
                    echo $platformAddr . "\n";
                    $isDo = true;
                }

                if ($isDo == true) {
                    $log = $time . "币种" . $coin . "钱包地址已批量生成 \n";
                    Log::logger("", $log, "address");
                    $output->writeln($time . "币种" . $coin . "钱包地址已批量生成");
                } else {
                    $log = $time . "币种" . $coin . "钱包地址批量生成失败 \n";
                    Log::logger("", $log, "address");
                    $output->writeln($time . "币种" . $coin . "钱包地址批量生成失败");
                }
            }
        }
        return true;
    }
}