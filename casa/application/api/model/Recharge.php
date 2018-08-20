<?php
/**
 * 兑换
 * User: Administrator
 * Date: 2018/5/31 0031
 * Time: 上午 9:22
 */

namespace app\api\model;

use think\Db;
class Recharge extends Base
{
    /**
     * 点击充值获取充值的数字货币地址
     * @return array
     * @author tiger
     * @time 2018/5/8
     */
    public function cRechange($uid, $coin)
    {
        $users_coin = Db::name('users_coin')->field('platformAddr,addrPassword,accountName')->where(['userId' => $uid, 'coin' => $coin])->find();
        if ($users_coin['platformAddr'] == '') {
            Db::startTrans();
            try {
                //查询当前币种时货币还是代币
                $coinInfo = Db::name('coin')->where(['coin' => $coin])->field('isCurrency,superiorId')->find();
                //从币种地址列表取出一个新地址
                if($coinInfo['isCurrency'] == 1){   //当前币种时货币
                    $address = Db::name('coin_purse_address')->where(['coin' => $coin])->order('id asc')->find();
                }else{  //当前币种时代币，获取当前币种的上级货币钱包地址
                    $superiorCoin = Db::name('coin')->where(['id' => $coinInfo['superiorId']])->value('coin');
                    $address = Db::name('coin_purse_address')->where(['coin' => $superiorCoin])->order('id asc')->find();
                }

                if(!empty($address)){
                    //用户第一次充值，给用户分配一个数字货币充值地址
                    Db::name('users_coin')->where(['userId' => $uid, 'coin' => $coin])
                        ->update(['platformAddr' => $address['platformAddr'], 'addrPassword' => $address['addrPassword'], 'accountName' => $address['accountName']]);
                    $userCoin = Db::name('users_coin')->field('platformAddr,addrPassword')->where(['userId' => $uid, 'coin' => $coin])->find();
                    Db::name('coin_purse_address')->where(['id' => $address['id']])->delete();//从币种地址列表删除刚分配出去的新地址
                    //用户数字货币充值申请表插入一条数据
                    $request = request();
                    $coin_recharge_apply = [
                        'userId' => $uid,
                        'coin' => $coin,
                        'coinAddr' => $address['platformAddr'],
                        'accountName' => $address['accountName'],
                        'status' => 0,
                        'createTime' => date('Y-m-d H:i:s'),
                        'createIp' => $request->ip(),
                        'createTer' => '',
                        'updateTime' => date('Y-m-d H:i:s'),
                        'endTime' => date('Y-m-d H:i:s', strtotime('+1 day')),
                    ];
                    Db::name('coin_recharge_apply')->insert($coin_recharge_apply);
                    Db::commit();
                }

                $res['rate'] = '';
                if($coin != 'CASA'){
                    $btcName = 'CASA_'.$coin;
                    $res['rate'] = '0.01';
//                    $res['rate'] = Cache::get('btc')[$btcName]['latestPrice'];
                }
                $res['users_coin'] = $userCoin;
                $res['coinList'] = ['ETH','USDT'];
                return SKReturn("获取成功",1, $res);
            } catch (\Exception $e) {
                Db::rollback();
                return SKReturn("获取失败".$e->getMessage());
            }
        } else {
            $data = [
                'status' => 0,
                'updateTime' => date('Y-m-d H:i:s'),
                'endTime' => date('Y-m-d H:i:s', strtotime('+1 day'))];
            Db::name('coin_recharge_apply')->where(['coin' => $coin, 'userId' => $uid])->update($data);
        }
        $res['rate'] = '';
        if($coin != 'CASA'){
            $btcName = 'CASA_'.$coin;
            $res['rate'] = '0.01';
//            $res['rate'] = Cache::get('btc')[$btcName]['latestPrice'];
        }
        $res['users_coin'] = $users_coin;
        $res['coinList'] = ['ETH','USDT'];
        return SKReturn("获取成功",1, $res);
    }
}