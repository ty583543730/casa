<?php
/**
 * 用户
 * User: yt
 * Date: 2018/5/30 0030
 * Time: 上午 11:17
 */

namespace app\admin\model;

use think\Db;

class Users extends Base
{
    public $userType = [1 => '普通用户', 2 => '区长', 3 => '系统管理员', 4 => '区域代理商'];

    public $update = [];

    public function __construct($data = [])
    {
        parent::__construct($data);

        $userType = Db::name('users_type')->field('id,name')->select();

        foreach ($userType as $k => $v) {
            $res[$v['id']] = $v['name'];
        }
        $this->userType = $res;
    }

    /**
     * 分页
     */
    public function pageQuery()
    {
        $where = [];

        $userId = input('get.userId/d');
        if (!empty($userId)) {
            $where['a.userId'] = $userId;
        }
        $userName = trim(input('get.userName'));
        if ($userName != '') $where['a.userName'] = ['like', '%' . $userName . '%'];

        if (timeTerm(input('startDate'), input('endDate')) != false) {
            $where['a.createTime'] = timeTerm(input('startDate'), input('endDate'));
        }
        $isRealname = input('get.isRealname/d', 0);
        if ($isRealname == 1) {
            $where['a.trueName'] = ['exp', Db::raw('is not null')];
        }
        if ($isRealname == 2) {
            $where['a.trueName'] = ['exp', Db::raw('is null')];
        }

        $phoneArea = input('get.phoneArea', '');
        if ($phoneArea != '') {
            $where['phoneArea'] = $phoneArea;
        }

        $userPhone = trim(input('get.userPhone'));
        if ($userPhone != 0) {
            $where['userPhone'] = $userPhone;
        }

        $phoneArea = input('get.phoneArea', '');
        if ($phoneArea != '') {
            $where['phoneArea'] = $phoneArea;
        }

        $userType = input('get.userType', '');
        if ($userType != '') {
            $where['userType'] = $userType;
        }

        $data = $this->alias('a')
            ->join('country b', 'a.phoneArea=b.number', 'left')
            ->field('a.userId,a.userName,a.userPhone,a.userType,a.trueName,a.parentId,a.userStatus,a.createTime,b.cnName')
            ->where($where)
            ->order('a.userId', 'asc')
            ->paginate(input('limit/d'))
            ->toArray();

        foreach ($data['data'] as $k => $v) {
            $data['data'][$k]['userType'] = $this->userType[$v['userType']];
            if (empty($v['trueName'])) $data['data'][$k]['trueName'] = '--';
        }
        return $data;

    }


    /**
     * 详情
     */
    public function getInfo($id)
    {
        $info = $this->get($id);
        if (empty($info['trueName'])) $info['trueName'] = '--';
        $info['userType'] = $this->userType[$info['userType']];
        return $info;
    }

    /**
     * 编辑
     */
    public function edit()
    {
        $id = input('post.userId/d');
        $data = input('post.');
        //$result = $this->validate('Users.edit')->allowField(true)->save($data, ['userId' => $id]);
        $result = $this->save($data, ['userId' => $id]);
        if (false !== $result) {
            return SKReturn("编辑成功", 1);
        } else {
            return SKReturn($this->getError());
        }
    }

    /**
     * 修改用户状态
     */
    public function changeStatus()
    {
        $data = input('post.');
        if (empty($data['id']) || empty($data['status'])) {
            return SKReturn('缺少必需参数');
        }
        $where = ['userId' => $data['id'], 'userStatus' => $data['status']];
        if ($data['status'] == -1) {
            $userStatus = 1;
        } else {
            $userStatus = -1;
        }
        $res = Db::name('users')->where($where)->setField('userStatus', $userStatus);
        if (false !== $res) {
            return SKReturn("修改成功", 1);
        } else {
            return SKReturn("修改失败");
        }
    }

    /**
     * 锁定解锁分佣奖励
     */
    public function changeRewards()
    {
        $id = input('post.id/d', 0);
        $status = input('post.status/d', 0);
        if (empty($id) || empty($status)) {
            return SKReturn('缺少必需参数id');
        }
        $user = $this->get($id);
        if ($user['userType'] == 1) {
            return SKReturn('只能锁定解锁区长或系统管理员的奖励');
        }
        if ($user['userStatus'] != $status) {
            return SKReturn('传递状态有误');
        }

        $where = ['userId' => $id, 'userStatus' => $status];

        if ($status == 2) {
            $userStatus = 1;
        } else {
            $userStatus = 2;
        }
        $res = Db::name('users')->where($where)->setField('userStatus', $userStatus);
        if (false !== $res) {
            return SKReturn("修改成功", 1);
        } else {
            return SKReturn("修改失败");
        }
    }

    /**
     * 用户下线获取分页
     */
    public function referralsPageQuery()
    {
        $where = [];
        $parentId = input('id/d', 0);
        if ($parentId === 0) {
            return [];
        } else {
            $where['a.parentId'] = $parentId;
        }
        $userId = input('get.userId/d', 0);
        if ($userId !== 0) {
            $where['a.userId'] = $userId;
        }
        $userName = trim(input('get.userName'));
        if ($userName != '') $where['a.userName'] = ['like', '%' . $userName . '%'];

        if (timeTerm(input('startDate'), input('endDate')) != false) {
            $where['a.createTime'] = timeTerm(input('startDate'), input('endDate'));
        }
        $isRealname = input('get.isRealname/d', 0);
        if ($isRealname == 1) {
            $where['a.trueName'] = ['exp', Db::raw('is not null')];
        }
        if ($isRealname == 2) {
            $where['a.trueName'] = ['exp', Db::raw('is null')];
        }

        $userStatus = input('get.userStatus/d', 0);
        if ($userStatus !== 0) {
            $where['userStatus'] = $userStatus;
        }

        $phoneArea = input('get.phoneArea', '');
        if ($phoneArea !== '') {
            $where['phoneArea'] = $phoneArea;
        }

        $userType = input('get.userType', '');
        if ($userType !== '') {
            $where['userType'] = $userType;
        }

        $data = $this->alias('a')
            ->join('country b', 'a.phoneArea=b.number', 'left')
            ->field('a.userId,a.userName,a.userPhone,a.userType,a.trueName,a.parentId,a.userStatus,a.createTime,b.cnName')
            ->where($where)
            ->order('a.userId', 'asc')
            ->paginate(input('limit/d'))
            ->toArray();

        foreach ($data['data'] as $k => $v) {
            $data['data'][$k]['userType'] = $this->userType[$v['userType']];
            if (empty($v['trueName'])) $data['data'][$k]['trueName'] = '--';
        }
        return $data;
    }

    /**
     * 用户升级 uid 用户id role升级到的角色 type 1手动升级2自动升级
     */
    public function userUpgrade($uid, $role = 2, $type = 2)
    {
        $user = Db::name('users')->where(['userId' => $uid])->field('userType,userStatus')->find();
        if ($user['userType'] == $role) {
            return SKReturn('该用户已经是区长了，不能再升级了');
        }
        if ($user['userStatus'] != 1) {
            return SKReturn('该用户被锁定');
        }

        Db::startTrans();
        try {
            $UpgradeData = [
                'type' => $type,
                'userId' => $uid,
                'preRole' => (int)$role - 1,
                'afterRole' => $role,
                'createTime' => date('Y-m-d H:i:s')
            ];
            Db::name('users_upgrade')->insert($UpgradeData);
            Db::name('users')->where(['userId' => $uid])->setField('userType', $role);
            Db::commit();
            return SKReturn('升级成功', 1);
        } catch (\Exception $e) {
            Db::rollback();
            return SKReturn($e->getMessage(), -2);
        }


    }

    /*用户币种列表*/
    public function coinPageQuery()
    {
        $where = [];
        $where['coin'] = 'CASA';
        $userPhone = trim(input('get.userPhone'));
        if ($userPhone != '') $where['u.userPhone'] = ['like', '%' . $userPhone . '%'];
        $res = Db::name('users_coin')->alias('c')
            ->join('users u', 'c.userId=u.userId', 'left')
            ->where($where)
            ->field('id,u.userId,phoneArea,userPhone,coin,black,locker,forzen,rechargeTotal,drawType')
            ->order('c.id desc')
            ->paginate(input('limit/d'))
            ->toArray();
        return $res;
    }

    /*获取用户币种数据*/
    public function getCoinInfo($id)
    {
        $data = Db::name('users_coin')->alias('c')
            ->join('users u', 'c.userId=u.userId')
            ->where(['id' => $id])
            ->field('c.userId,userName,userPhone,userStatus,coin,black,locker,forzen')
            ->find();
        if ($data['coin'] == 'CASA') {
            //获取累计提现
            $data['recharge'] = Db::name('log_casa')->where(['userId' => $data['userId'], 'type' => 6])->sum('coinBlack');
            //获取累计充值
            $data['drawCount'] = Db::name('log_casa')->where(['userId' => $data['userId'], 'type' => 1])->sum('coinBlack');
            //获取累计转账
            $data['outCount'] = Db::name('transfers')->where(['sendId' => $data['userId'], 'type' => 1])->sum('total');
            //获取累计收款
            $data['enterCount'] = Db::name('transfers')->where(['userId' => $data['userId'], 'type' => 2])->sum('total');
            //获取累计分佣
            $data['maidCount'] = Db::name('rewards')->where(['userId' => $data['userId']])->sum('num');
        }
        return $data;
    }

    /*锁定解锁用户币种账户*/
    public function changeType()
    {
        $data = input('post.');
        if (empty($data['id']) || empty($data['status'])) {
            return SKReturn('缺少必需参数');
        }
        $where = ['id' => $data['id'], 'drawType' => $data['status']];
        if ($data['status'] == -1) {
            $drawType = 1;
        } else {
            $drawType = -1;
        }
        $res = Db::name('users_coin')->where($where)->setField('drawType', $drawType);
        if (false !== $res) {
            return SKReturn("处理成功", 1);
        } else {
            return SKReturn("处理失败");
        }
    }
}