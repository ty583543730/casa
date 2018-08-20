<?php

namespace app\admin\model;

use think\Db;

/**
 * 虚拟币提币业务处理
 */
class CoinDraws extends Base
{
    protected $drawType = [-1 => '审核拒绝', 0 => '审核中', 1 => '审核通过', '2' => '等待交易确认', '3' => '交易成功'];
    /*
     * 提币列表
     */
    public function pageQuery()
    {
        $where = [];
        $where['l.dataFlag'] = 1;
        if (input('userPhone')) {
            $where['u.userPhone'] = input('userPhone');
        }
        if (input('coin') && input('coin') != -1) {
            $where['l.coin'] = input('coin');
        }
        //起止时间
        if (input('startTime') || input('endTime')) {
            $where['l.createTime'] = timeTerm(input('startTime'), input('endTime'), 2);
        }
        $page = Db::name('coin_draw')->alias('l')
            ->join('users u', 'u.userId=l.userId', 'left')
            ->field('u.userPhone,l.*')
            ->where($where)
            ->order('l.id', 'desc')
            ->paginate(input('limit/d'))
            ->toArray();
        if (count($page['data']) > 0) {
            foreach ($page['data'] as $k => $v) {
                $page['data'][$k]['status'] = $this->drawType[$v['status']];
                if (empty($v['checkRemark'])){
                    $page['data'][$k]['checkRemark'] = '';
                }
            }
        }
        return $page;
    }

    /**
     * 虚拟币审核业务处理
     * @return array
     * @author tiger
     * @time 2018/03/09
     */
    public function drawSetStatus()
    {
        $data = input('post.');
        $data = trimArray($data);
        $staff = session('sk_staff');
        if(empty($staff))$staff = 0;
        $time = date('Y-m-d H:i:s');
        $coin_draw = [
            'checkStaffId' => $staff['staffId'],
            'checkTime' => $time
        ];
        $coin_draw['status'] = $data['status'];

        if (!empty(input('checkRemark', ''))) {
            $coin_draw['checkRemark'] = input('checkRemark', '');
        }
        //拒绝提现
        if ($data['status'] == -1) {
            if (empty($data['id'])) {
                return SKReturn('参数错误');
            }
            //查询当前操作提现订单信息
            $Info = Db::name('coin_draw')->where('id', $data['id'])->find();
            $miner = $Info['miner'];
            $miner = 0; //暂时设定矿工费为0;
            if (empty($Info)) {
                return SKReturn('该订单不存在');
            }
            //操作前查询一次用户钱包对应的币种资产
            $users_coina = Db::name('users_coin')->where(['userId' => $Info['userId'], 'coin' => $Info['coin']])->find();
            if (empty($users_coina)) {
                return SKReturn('提现用户对应币种的钱包资产不存在');
            }
            Db::startTrans();
            try {
                //后台拒绝通过本次提现申请
                $allNum = $Info['num'] + $miner + $Info['fee']; //拒绝通过提现时需要解除冻结的用户可用币数量
                if ($users_coina['forzen'] < $allNum) {
                    return SKReturn('该用户冻结币数量不足以本次提现操作所需');
                }
                //用户在平台对应的冻结中的币种钱包资产减少，可用币增加
                $updates['forzen'] = Db::raw('forzen-'.$allNum);
                $updates['black'] = Db::raw('black+'.$allNum);
                Db::name('users_coin')->where(['userId' => $Info['userId'], 'coin' => $Info['coin']])->update($updates);
                //生成一条最新的用户账户币种流水
                insertLog($Info['coin'], $Info['userId'], $Info['orderNo'], 7, $allNum, $Lock = 0.0000, '-'.$allNum, $remark = '平台拒绝通过用户虚拟币提现申请', $time);
                //修改用户数字货币充值表订单处理状态等信息
                Db::name('coin_draw')->where('id', $data['id'])->update($coin_draw);

                Db::commit();
                return SKReturn("操作成功", 1);
            } catch (Exception $exc) {
                Db::rollback();
                return SKReturn("操作失败，请重试");
            }

        } else if ($data['status'] == 1) {    //通过提现
            $where = [];
            $where['status'] = 0;
            if (!empty($data['username'])) {
                $user = Db::name('users')->where(['username'=>$data['username']])->field('userId')->find();
                if(empty($user)){
                    return SKReturn("提现用户名不存在");
                }
                $where['userId'] = $user['userId'];
            }
            if (!empty($data['coin']) && $data['coin'] != -1) {
                $where['coin'] = $data['coin'];
            }
            if (!empty($data['start']) || !empty($data['end'])) {
                $where['createTime'] = timeTerm($data['start'], $data['end'], 3);
            }
            if (!empty($data['id'])) $where['id'] = $data['id'];

            $res = Db::name('coin_draw')->where($where)->update(['status'=>1,'checkStaffId' => $staff['staffId'],'checkTime' => $time]);
            if($res){
                return SKReturn("操作成功", 1);
            }
            return SKReturn("操作失败，请重试");
        } else if ($data['status'] == -2) { //提现申请时申请交易失败，再次申请

        }
    }
}
