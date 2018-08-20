<?php
/**
 * 用户功能
 * User: yt
 * Date: 2018/5/30 0030
 * Time: 上午 10:14
 */

namespace app\admin\controller;

use app\admin\model\Users as M;

class Users extends Base
{
    /*列表*/
    public function index()
    {
        return $this->fetch('index');
    }

    /**
     * 获取分页
     */
    public function pageQuery()
    {
        $m = new M();
        return PQReturn($m->pageQuery());
    }

    /**
     * 详情
     */
    public function info()
    {
        $id = input('get.id/d', 0);
        if ($id == 0) {
            echo '没有id！';
        }
        $m = new M();
        $info = $m->getInfo($id);
        $this->assign('info', $info);
        return $this->fetch('info');
    }

    /**
     * 编辑
     */
    public function edit()
    {
        $m = new M();
        return $m->edit();
    }

    /**
     * 修改用户状态
     */
    public function changeStatus()
    {
        $optbing = optBing('changeStatus', input('id'), 0);
        if (empty($optbing)) {
            return SKReturn("业务正在处理中，请稍后");
        }
        $m = new M();
        $res = $m->changeStatus();
        optBing('changeStatus', input('id'));
        return $res;
    }

    /**
     * 锁定解锁分佣奖励
     */
    public function changeRewards()
    {
        $optbing = optBing('changeRewards', input('id'), 0);
        if (empty($optbing)) {
            return SKReturn("业务正在处理中，请稍后");
        }
        $m = new M();
        $res = $m->changeRewards();
        optBing('changeRewards', input('id'));
        return $res;
    }

    /**
     * 用户下线
     */
    public function referrals()
    {
        $id = input('get.id/d', 0);
        if ($id == 0) {
            echo '没有id！';
        }
        $this->assign('id', $id);
        return $this->fetch('referrals');
    }

    /**
     * 用户下线获取分页
     */
    public function referralsPageQuery()
    {
        $m = new M();
        return PQReturn($m->referralsPageQuery());
    }

    /**
     * 用户手动升级 yt
     */
    public function userUpgrade()
    {
        $userId = input('userId/d', 0);
        $role = input('role/d', 0);
        if (empty($userId) || empty($role)) {
            return SKReturn('缺少参数');
        }
        $optbing = optBing('userUpgrade', $userId, 0);
        if (empty($optbing)) {
            return SKReturn("业务正在处理中，请稍后");
        }
        $m = new M();
        $res = $m->userUpgrade($userId, $role, 1);
        optBing('userUpgrade', $userId);
        return $res;
    }

    /*用户币种列表*/
    public function usersCoin(){
        if (request()->isAjax()){
            $m = new M();
            return PQReturn($m->coinPageQuery());
        }else{
            $this->assign('coinList',SKBtcCoin());
            return $this->fetch('userscoin');
        }
    }

    /*用户币种详情*/
    public function coinInfo(){
        $id = input('get.id/d', 0);
        if ($id == 0) {
            echo '没有id！';
        }
        $m = new M();
        $info = $m->getCoinInfo($id);
        $this->assign('info', $info);
        return $this->fetch('coinInfo');
    }

    /*锁定解锁用户币种账户*/
    public function changeType(){
        $optBing = optBing('changeType', input('id'), 0);
        if (empty($optBing)) {
            return SKReturn("业务正在处理中，请稍后");
        }
        $m = new M();
        $res = $m->changeType();
        optBing('changeType', input('id'));
        return $res;
    }
}