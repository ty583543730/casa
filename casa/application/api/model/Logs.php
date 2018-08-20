<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/8/8 0001
 * Time: 下午 2:37
 */

namespace app\api\model;

use think\Db;

class Logs extends Base
{
    /**
     * 币流水记录
     * @author：yt 2018/6/1
     */
    public function coin($param)
    {
        $userId = 592;// userId();
        $param['page'] = !empty($param['page']) ? $param['page'] : 1;
        $param['pageSize'] = !empty($param['pageSize']) ? $param['pageSize'] : 10;

        $list = Db::name("log_casa")
            ->field("id,orderNo,type,coinBlack,coinLock,remark,createTime")
            ->where(['userId' => $userId])
            ->order('id desc')
            ->paginate($param['pageSize'], false, ['page' => $param['page']])
            ->toArray();
        $data =$list['data'];
        if (!empty($data)) {
            $type = getLogTradeList('log_coin');

            foreach ($data as $k => $v) {
                if (in_array($v['type'], [13, 14, 15])) {
                    $data[$k]['num'] = $v['coinLock'];
                    $data[$k]['coinType'] = '锁仓币';
                } else {
                    $data[$k]['num'] = $v['coinBlack'];
                    $data[$k]['coinType'] = '可用币';
                }

                unset($data[$k]['coinBlack']);
                unset($data[$k]['coinLock']);

                $data[$k]['type'] = $type[$v['type']];
            }
            return SKReturn("查询成功！", 1, $data);
        } else {
            return SKReturn("暂无数据！", -8);
        }
    }

    /**
     * 币记录详情
     * @author：yt 2018/6/1
     */
    public function coininfo($param)
    {
        $must = ["id"];
        $mustParams = mustParams($must, trimArray($param));
        if (!$mustParams) {
            return SKReturn("必填参数都不能为空");
        }

        $userId = 592;// userId();

        $info = Db::name("log_casa")
            ->field("orderNo,type,coinBlack,coinLock,coinFrozen,remark,createTime")
            ->where(['userId' => $userId, 'id' => $param['id']])
            ->find();

        if (!empty($info)) {
            $type = getLogTradeList('log_coin');
            switch ($info['type']){
                case 1://充值
                    $info['address']=Db::name('coin_recharge')->where(['userId'=>$userId,'orderNo'=>$info['orderNo']])->value('coinAddr');
                case 2://兑换
                    $tmp=Db::name('coin_exchange')->field('coin,num,radio')->where(['userId'=>$userId,'orderNo'=>$info['orderNo']])->find();
                    $info=array_merge($info,$tmp);
                case 3://到账收款
                    $tmp=Db::name('transfer_user a')->join('users u','a.sendId=b.userId')->field('total,fee,num,u.userName')->where(['userId'=>$userId,'orderNo'=>$info['orderNo']])->find();
                    $info=array_merge($info,$tmp);
                case 4://转账付款
                    $tmp=Db::name('transfer_user')->join('users u','a.userId=b.userId')->field('u.userName')->where(['sendId'=>$userId,'orderNo'=>$info['orderNo']])->find();
                    $info=array_merge($info,$tmp);
                case 14://各种奖励
                    $info['rewardsType']=Db::name('rewards')->where(['userId'=>$userId,'orderNo'=>$info['orderNo']])->value('type');
            }

            $info['typeName'] = $type[$info['type']];

            return SKReturn("查询成功！", 1, $info);
        } else {
            return SKReturn("暂无数据！", -8);
        }
    }

    /**
     * 积分记录
     * @author：yt 2018/6/1
     */
    public function score($param)
    {
        $param['userId'] =1;// userId();
        $param['page'] = !empty($param['page']) ? $param['page'] : 1;
        $param['pageSize'] = !empty($param['pageSize']) ? $param['pageSize'] : 10;

        $list = Db::name("log_score")
            ->field("id,orderNo,type,num,binding,remark,createTime")
            ->where(['userId' => $param['userId']])
            ->order('id desc')
            ->paginate($param['pageSize'], false, ['page' => $param['page']])
            ->toArray();

        $data =$list['data'];

        if (!empty($data)) {
            $type = getLogTradeList('log_score');

            foreach ($data as $k => $v) {
                if ($v['num'] == 0) {
                    $data[$k]['num'] = $v['binding'];
                    $data[$k]['scoreType'] = '绑定币';
                } else {
                    $data[$k]['scoreType'] = '可用币';
                }

                unset($data[$k]['binding']);

                $data[$k]['type'] = $type[$v['type']];
            }
            return SKReturn("查询成功！", 1, $data);
        } else {
            return SKReturn("暂无数据！", -8);
        }
    }

    /**
     * 积分记录详情
     * @author：yt 2018/6/1
     */
    public function scoreinfo($param)
    {
        $must = ["id"];
        $mustParams = mustParams($must, trimArray($param));
        if (!$mustParams) {
            return SKReturn("必填参数都不能为空");
        }

        $userId = 592;// userId();

        $info = Db::name("log_score")
            ->field("orderNo,type,num,binding,remark,createTime")
            ->where(['userId' => $userId, 'id' => $param['id']])
            ->find();

        if (!empty($info)) {
            $type = getLogTradeList('log_score');

            switch ($info['type']){
                case 3://奖励可用积分
                    $info['rewardsType']=Db::name('rewards')->where(['userId'=>$userId,'orderNo'=>$info['orderNo']])->value('type');
                case 4://奖励绑定积分
                    $info['rewardsType']=Db::name('rewards')->where(['userId'=>$userId,'orderNo'=>$info['orderNo']])->value('type');
            }

            $info['type'] = $type[$info['type']];

            return SKReturn("查询成功！", 1, $info);
        } else {
            return SKReturn("暂无数据！", -8);
        }
    }

}