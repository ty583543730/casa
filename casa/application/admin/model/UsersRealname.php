<?php
/**
 * 用户实名认证模型.
 * User: wjj
 * Date: 2018/5/30
 * Time: 16:21
 */

namespace app\admin\model;

use think\Db;

class UsersRealname extends Base
{
    /* 实名证件类型*/
    private $cardType = [1 => '二代身份证', 2 => '香港', 3 => '澳门', 4 => '台湾', 5 => '新加坡'];

    /* 分页*/
    public function pageQuery()
    {
        $where = [];

        $userId = input('get.userId/d', 0);
        if ($userId !== 0) {
            $where['a.userId'] = $userId;
        }

        $pageSize = empty(input('limit')) ? input('limit') : 20;

        $userName = trim(input('get.userName'));
        if ($userName != '') $where['u.userName'] = ['like', '%' . $userName . '%'];

        if (timeTerm(input('startDate'), input('endDate')) != false) {
            $where['a.createTime'] = timeTerm(input('startDate'), input('endDate'));
        }
        $userPhone = input('get.userPhone', '');
        if ($userPhone !== '') {
            $where['userPhone'] = $userPhone;
        }

        $phoneArea = input('get.phoneArea', '');
        if ($phoneArea !== '') {
            $where['phoneArea'] = $phoneArea;
        }

        $checkStatus = input('get.checkStatus', '');
        if ($checkStatus !== '') {
            $where['checkStatus'] = $checkStatus;
        }

        $data = $this->alias('a')
            ->join('users u', 'a.userId=u.userId', 'left')
            ->field('id,a.userId,u.userPhone,u.userName,cardType,checkStatus,a.trueName,a.createTime')
            ->where($where)
            ->order('a.id', 'asc')
            ->paginate($pageSize)
            ->toArray();
        foreach ($data['data'] as $k => $v) {
            $data['data'][$k]['cardType'] = $this->cardType[$v['cardType']];
        }
        return $data;
    }

    /*获取实名认证信息*/
    public function getById($id)
    {
        return $this->get(['id' => $id]);
    }

    /* 处理审核认证结果*/
    public function toHandle($param)
    {
        $id = $param['id'];
        $checkStatus = $param['checkStatus'];
        $checkRemark = $param['checkRemark'];
        $staffId = (int)session('sk_staff.staffId');
        $checkTime = date('Y-m-d H:i:s');
        if (empty($id)) {
            return SKReturn('无效数据');
        }
        if ($checkStatus == '') {
            return SKReturn('请选择审核结果');
        }
        if ($checkStatus == 2 && $checkRemark == '') {
            return SKReturn('请填写审核不通过原因');
        }
        if ($checkStatus == 2) {
            //处理审核失败
            $result = $this->where(['id' => $id])->update([
                'checkStatus' => $checkStatus,
                'checkRemark' => $checkRemark,
                'staffId' => $staffId,
                'checkTime' => $checkTime
            ]);
            if ($result !== false) {
                return SKReturn("处理成功", 1);
            } else {
                return SKReturn($this->getError(), -1);
            }
        } else if ($checkStatus == 1) {
            //处理审核成功
            Db::startTrans();
            try {
                $user = Db::name('users')->where(['userId' => $param['userId']])->find();
                if (empty($user)) {
                    Db::rollback();
                    return SKReturn('不存在此用户!');
                }
                $this->where(['id' => $id])->update(['checkStatus' => $checkStatus, 'staffId' => $staffId, 'checkTime' => $checkTime]);
                Db::name('users')->where(['userId' => $param['userId']])->update(['trueName' => $param['trueName']]);
                $coin_data = Db::name('users_coin')->field('black,locker,forzen')->where(['userId' => $param['userId'], 'coin' => 'CASA'])->find();
                $parentId = Db::name('users')->field('parentId')->where(['userId' => $param['userId']])->find()['parentId'];
                $score_data = Db::name('users_extend')->field('num,binding')->where(['userId' => $parentId])->find();

                if (empty($score_data)) {
                    Log::logger('UsersRealname', '用户id：' . $param['userId'] . '推荐人不存在', 'UsersRealname');
                } else {
                    $score_update['num'] = Db::raw('num+10');
                    $score_update['computingPower'] = Db::raw('computingPower+2');

                    $score_insert['userId'] = $parentId;
                    $score_insert['orderNo'] = SKorderSn('s');
                    $score_insert['type'] = 1;
                    $score_insert['preNum'] = $score_data['num'];
                    $score_insert['preBinding'] = $score_data['binding'];
                    $score_insert['num'] = 100;
                    $score_insert['binding'] = 0;
                    $score_insert['remark'] = '奖励积分给用户实名推荐人';
                    $score_insert['dataFlag'] = 1;
                    $score_insert['createTime'] = date('Y-m-d H:i:s');
                    Db::name('users_extend')->where(['userId' => $parentId])->update($score_update);
                    Db::name('log_score')->insert($score_insert);
                }
                //奖励用户100个锁仓币，推荐人10个可用积分 2个算力
                $coin_update['locker'] = Db::raw('locker+100');

                $casa_insert['userId'] = $param['userId'];
                $casa_insert['orderNo'] = SKorderSn('g');
                $casa_insert['type'] = 14;
                $casa_insert['preCoin'] = $coin_data['black'];
                $casa_insert['preCoinFrozen'] = $coin_data['forzen'];
                $casa_insert['preCoinLock'] = $coin_data['locker'];
                $casa_insert['coinBlack'] = 0;
                $casa_insert['coinFrozen'] = 0;
                $casa_insert['coinLock'] = 100;
                $casa_insert['remark'] = '用户实名奖励';
                $casa_insert['dataFlag'] = 1;
                $casa_insert['createTime'] = date('Y-m-d H:i:s');


                $coin_res = Db::name('users_coin')->where(['userId' => $param['userId'], 'coin' => 'CASA'])->update($coin_update);
                $casa_res = Db::name('log_casa')->insert($casa_insert);

                if ($coin_res == 1 && $casa_res == 1) {
                    Db::commit();
                    return SKReturn("处理成功", 1);
                } else {
                    return SKReturn('数据异常，请联系管理员');
                }

            } catch (\Exception $exc) {
                Db::rollback();
                return SKReturn($exc->getMessage());
            }
        } else {
            return SKReturn('非法请求');
        }
    }
}