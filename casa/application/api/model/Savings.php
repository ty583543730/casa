<?php
/**
 * 储蓄
 * User: yt
 * Date: 2018/8/1
 * Time: 17:41
 */

namespace app\api\model;


use app\admin\model\SysConfigs;
use think\Db;
use think\Exception;

class Savings extends Base
{
    private $savingsStatus = [1 => '进行中', 2 => '结算中', 3 => '已结算'];

    /*列表*/
    public function pageQuery($param)
    {
        $userId = userId();
        $param['page'] = !empty($param['page']) ? $param['page'] : 1;
        $param['pageSize'] = !empty($param['pageSize']) ? $param['pageSize'] : 10;
        $list = Db::name("savings")
            ->field('id,orderNo,num,endTime,month,status')
            ->where(['userId' => $userId])
            ->order('status asc,id desc')
            ->paginate($param['pageSize'], false, ['page' => $param['page']])
            ->toArray();
        if (!empty($list['data'])) {
            foreach ($list['data'] as $k => $v) {
                $list['data'][$k]['status'] = $this->savingsStatus[$v['status']];
            }
            return SKReturn("查询成功！", 1, $list['data']);
        } else {
            return SKReturn("暂无数据！", -8);
        }
    }

    /*详情*/
    public function info($param)
    {
        $must = ["id"];
        $mustParams = mustParams($must, trimArray($param));
        if (!$mustParams) {
            return SKReturn("必填参数都不能为空");
        }
        $userId = userId();

        $info = Db::name("savings")->field('dataFlag',true)->where(['id' => $param['id']])->find();
        if (!empty($info)) {
            $info['status'] = $this->savingsStatus[$info['status']];
            $tmp=Db::name('rewards')->field('sum(score) as totalScore,sum(binding) as totalBinding,sum(locker) as totalLocker')->where(['userId' => $userId, 'orderNo' => $info['orderNo']])->find();
            $info=array_merge($info,$tmp);
            $list = Db::name('rewards')->field('score,binding,locker,createTime')->where(['userId' => $userId, 'orderNo' => $info['orderNo']])->select();
            if (empty($list)) {
                $info['rewards'] = [];
            } else {
                $info['rewards'] = $list;
            }
            return SKReturn("查询成功！", 1, $info);
        } else {
            return SKReturn("暂无数据！", -8);
        }
    }


    /*进入储蓄页面取参*/
    public function getSavings()
    {
        $useId =  userId();
        $data['black'] = Db::name("users_coin")->where(['userId' => $useId, 'coin' => "CASA"])->value('black');
        $tmp1 = Db::name('savings')->where(['userId' => $useId, 'status' => 1])->sum('num');
        $tmp2 = Db::name('savings')->where(['userId' => $useId, 'status' => 2])->sum('num');
        $tmp3 = Db::name('savings')->where(['userId' => $useId, 'status' => 3])->sum('num');
        $data['savings'] = $tmp1 + bcmul($tmp2, 0.7, 4) + bcmul($tmp3, 0.4, 4);
        $data['rule'] = 'hahahahhahahah';
        $data['rewards']=[
            ['month'=>3,'ratio'=>SysConfig('savingsThree')],
            ['month'=>6,'ratio'=>SysConfig('savingsSix')],
            ['month'=>12,'ratio'=>SysConfig('savingsTwelve')]
        ];
        return SKReturn('获取成功', 1, $data);
    }

    /*储蓄提交*/
    public function submit($param)
    {
        $must = ["month", "num",];
        $mustParams = mustParams($must, trimArray($param));
        if (!$mustParams) {
            return SKReturn("必填参数都不能为空");
        }
        $userId = userId();
        $orderNo = SKOrderSn('f');
        $insert = [
            'orderNo' => $orderNo,
            'userId' => $userId,
            'month' => $param['month'],
            'num' => $param['num'],
            'marketValue'=>'1',
            'createTime' => date('Y-m-d H:i:s')
        ];
        switch ($param['month']) {
            case 3:
                $insert['ratio'] = SysConfig('savingsThree');
                $insert['endTime'] = date('Y-m-d H:i:s', strtotime("+3 month"));
                break;
            case 6:
                $insert['ratio'] = SysConfig('savingsSix');
                $insert['endTime'] = date('Y-m-d H:i:s', strtotime("+6 month"));
                break;
            case 12:
                $insert['ratio'] = SysConfig('savingsTwelve');
                $insert['endTime'] = date('Y-m-d H:i:s', strtotime("+12 month"));
                break;
        }

        Db::startTrans();
        try {
            Db::name('savings')->insert($insert);
            insertLog('CASA', $userId, $orderNo, '13', '-' . $param['num'], 0, $param['num'], '储蓄冻结');
            $update = [
                'black' => Db::raw('black-' . $param['num']),
                'forzen' => Db::raw('forzen+' . $param['num']),
            ];
            Db::name('users_coin')->where(['userId' => $userId, 'coin' => 'CASA'])->update($update);
            Db::commit();
            return SKReturn('提交成功',1);
        } catch (Exception $e) {
            Db::rollback();
            return SKReturn('提交失败:'.$e->getMessage(),-8);
        }


    }
}