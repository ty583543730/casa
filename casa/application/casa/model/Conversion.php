<?php

namespace app\casa\model;

use think\Db;
use think\Log;
use app\api\model\Base;

/**
 * 锁仓币转化成可用币
 * User: tiger
 * Date: 2018-7-02 14:54
 */
class Conversion extends Base
{
    /**
     * @Description  锁仓币转化成可用币
     * @DateTime    2018-7-02 14:54
     */
    public function coinConversion(){
        //获取转化比例
        $conversionRate = SysConfig('Conversion');
        $rate = bcdiv($conversionRate,100,4);
        //查询所有币种账户锁定币额度大于0的数据进行处理
        $users_coin = Db::name('users_coin')->where(['locker' =>['gt', 0]])->field('id,userId,coin,locker')->select();

        Db::startTrans();
        try {
            foreach($users_coin as $k=>$v){
                $orderNo = SKOrderSn('d');
                $rateNum = bcmul($v['locker'],$rate,4);//本次转化数量
                $res['black'] = Db::raw('black+'.$rateNum);
                $res['locker'] = Db::raw('locker-'.$rateNum);

                Db::name('users_coin')->where(['userId' => $v['userId'], 'coin' => $v['coin']])->update($res);
                insertLog($v['coin'],$v['userId'],$orderNo,47,$rateNum,'-'.$rateNum,0.0000,'锁定币转换');
                Log::logger("", 'userId:'.$v['userId'] ."币种:".$v['coin']. "本次转化数量" . $rateNum ."orderNo:" . $orderNo, "conversionlog");
            }
            Db::commit();
            return SKReturn("操作成功", 1);
        } catch (\Exception $e) {
            Db::rollback();
            Log::logger("", "操作失败" . $e->getMessage() . "行数:" . $e->getLine() . "文件:" . $e->getFile(), "conversionlog");
            return SKReturn('操作失败，请重试');
        }

    }
}