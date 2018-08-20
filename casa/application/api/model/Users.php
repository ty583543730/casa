<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/5/31 0031
 * Time: 上午 9:22
 */

namespace app\api\model;

use think\Db;
use think\Loader;

class Users extends Base
{
    public function _initialize()
    {
        ISLogin();
    }

    /**
     * 用户的基本信息
     * @author：zjf 2018/5/31
     */
    public function userInfo($param)
    {
        $param['userId'] = userId();

        //用户信息
        $user = Db::name("users")->field('loginSecret,loginPwd,terminal,createIp,createTime', true)->where(['userId' => $param['userId']])->find();
        if (empty($user)) {
            return SKReturn("获取失败", -8);
        }
        $user['userName'] = !empty($user['userName']) ? $user['userName'] : "";
        $user['trueName'] = !empty($user['trueName']) ? $user['trueName'] : "";
        $user['phoneHide'] = phoneHide($user['userPhone']);
        $user['url'] = BASE_URL . 'home/login/register/userNo/' . $user['userNo'] . '.html';
        $user['havePay'] = !empty($user['payPwd']) ? 1 : 0;
        unset($user['payPwd']);
        //用户类型
        $user['userTypeName'] = getUserTypeName($user['userType']);

        $coin = Db::name("users_coin")->field('coin,black,locker,forzen,rechargeType,drawType')->where(['userId' => $param['userId']])->select();
        foreach ($coin as $k => $v) {
            $user[$v['coin']] = [
                "black" => $v['black'],
                "locker" => $v['locker'],
                "forzen" => $v['forzen'],
                "rechargeType" => $v['rechargeType'],
                "drawType" => $v['drawType'],
            ];
        }
        //市价
        $radio = '0.11';
        $user['coin'] = SYSTEM_COIN;
        $user['coinTotal'] = bcadd($user[SYSTEM_COIN]['black'] + $user[SYSTEM_COIN]['locker'], $user[SYSTEM_COIN]['forzen'], 4);
        $user['coinCNY'] = bcmul($user['coinTotal'], $radio, 2);
        //积分
        $tmp = Db::name('users_extend')->where(['userId' => $param['userId']])->field('num as score,binding')->find();
        $user = array_merge($user, $tmp);


        $user['realNameStaus'] = Db::name("users_realname")->where(['userId' => $param['userId']])->value('checkStatus');
        if (is_null($user['realNameStaus'])) {
            $user['realNameStaus'] = -1;
        }
        return SKReturn("获取成功", 1, $user);
    }


    /**
     * 实名认证（人工认证）
     * @author：zjf 2018/5/31
     * @param $trueName 真实姓名
     * @param $cardID 身份证号码
     * @param $cardUrl 身份证正面图片
     * @param $cardBackUrl 身份证背面图片
     */
    public function realPersonAuth($param)
    {
        $must = ["trueName", "cardID", "cardUrl", "cardBackUrl"];
        $mustParams = mustParams($must, trimArray($param));
        if (!$mustParams) {
            return SKReturn("必填参数都不能为空");
        }
        $param['userId'] = userId();

        $data = [
            'trueName' => $param["trueName"],
            'cardID' => $param["cardID"],
            'cardUrl' => $param["cardUrl"],
            'cardBackUrl' => $param["cardBackUrl"],
        ];
        $validate = Loader::validate("users", "validate", false, "common");
        $result = $validate->scene('realPersonAuth')->check($data);
        if (!$result) {
            return SKReturn($validate->getError());
        }
        $users_realname = Db::name("users_realname")->field("id,checkStatus")->where(['userId' => $param['userId']])->find();
        if (!empty($users_realname)) {
            if ($users_realname['checkStatus'] == 1) {
                return SKReturn("您已经认证通过，请勿重复操作");
            }
            if ($users_realname['checkStatus'] == 0) {
                return SKReturn("认证审核中，请勿重复操作");
            }
        }
        $usersRealname = [
            'userId' => $param['userId'],
            'trueName' => $param['trueName'],
            'cardType' => 1,
            'cardID' => $param['cardID'],
            'cardUrl' => $param['cardUrl'],
            'cardBackUrl' => $param['cardBackUrl'],
            'checkStatus' => 0,
            'createTime' => timeFormat(),
        ];
        Db::startTrans();
        try {
            if (!empty($users_realname)) {
                $usersRealname['id'] = $users_realname['id'];
                Db::name('users_realname')->update($usersRealname);
            } else {
                Db::name('users_realname')->insert($usersRealname);
            }
            Db::commit();
            return SKReturn('提交成功，请等待审核结果。', 1);
        } catch (Exception $e) {
            Db::rollback();
            if (config("app_debug")) {
                $msg = "[Line:]" . $e->getLine() . "[Msg:]" . $e->getMessage();
            } else {
                $msg = "操作异常";
            }
            return SKReturn($msg);
        }
    }

    /**
     * 用户实名信息
     * @author：zjf 2018/5/31
     */
    public function realInfo($param)
    {
        $param['userId'] = userId();

        $users_realname = Db::name("users_realname")->where(['userId' => $param['userId']])->find();
        if (!empty($users_realname)) {
            $users_realname['cardUrl'] = $users_realname['cardUrl'];
            $users_realname['cardBackUrl'] = $users_realname['cardBackUrl'];
        }
        return SKReturn("获取成功", 1, $users_realname);
    }

    /**
     * 邀请好友列表
     * @author：zjf 2018/5/31
     */
    public function inviteList($param)
    {
        $param['userId'] = userId();

        $param['page'] = !empty($param['page']) ? $param['page'] : 1;
        $param['pageSize'] = !empty($param['pageSize']) ? $param['pageSize'] : 10;
        $data = Db::name("users")
            ->field("userPhone,userPhoto,createTime")
            ->where(['parentId' => $param['userId']])
            ->paginate($param['pageSize'], false, ['page' => $param['page']])
            ->toArray();
        $sum = Db::name("users")
            ->where(['parentId' => $param['userId']])
            ->count();
        if (!empty($data['data'])) {
            foreach ($data['data'] as $k => $v) {
                $data['data'][$k]['userPhone'] = phoneHide($v['userPhone']);
                $data['data'][$k]['userPhoto'] = $v['userPhoto'];
            }
        }
        $list['sum'] = !empty($sum) ? $sum : 0;
        $list['data'] = $data['data'];
        if (!empty($list['data'])) {
            return SKReturn("获取成功", 1, $list);
        } else {
            return SKReturn("未查询到数据", -8);
        }
    }

    /**
     * 邀请奖励明细
     * @author：zjf 2018/5/31
     */
    public function rewardList($param)
    {
        $param['userId'] = userId();

        $param['page'] = !empty($param['page']) ? $param['page'] : 1;
        $param['pageSize'] = !empty($param['pageSize']) ? $param['pageSize'] : 10;
        $list = Db::name("rewards")->alias("a")
            ->field("a.type,a.payNum,a.num,a.createTime,b.userPhone,b.userPhoto")
            ->join("users b", "a.sendId = b.userId")
            ->where(['a.userId' => $param['userId']])
            ->paginate($param['pageSize'], false, ['page' => $param['page']])
            ->toArray();
        $sum = Db::name("rewards")
            ->where(['userId' => $param['userId']])
            ->sum("num");
        if (!empty($list['data'])) {
            $type = getLogTradeList("rewards");
            foreach ($list['data'] as $k => $v) {
                $list['data'][$k]['type'] = $type[$v['type']];
                $data['data'][$k]['userPhone'] = phoneHide($v['userPhone']);
            }
        }
        $data['sum'] = !empty($sum) ? substr(sprintf("%.5f", $sum), 0, -1) : 0;
        $data['data'] = $list['data'];
        if (!empty($data['data'])) {
            return SKReturn("查询成功！", 1, $data);
        } else {
            return SKReturn("暂无数据！", -8);
        }
    }

    /**
     * 用户头像修改
     * @author：zjf 2018/5/31
     * @param $userPhoto 用户头像
     */
    public function editPhoto($param)
    {
        $must = ["userPhoto"];
        $mustParams = mustParams($must, trimArray($param));
        if (!$mustParams) {
            return SKReturn("必填参数都不能为空");
        }
        $userId = userId();

        $validate = Loader::validate("users", "validate", false, "common");
        $result = $validate->scene('userPhoto')->check(['userPhoto' => $param["userPhoto"]]);
        if (!$result) {
            return SKReturn($validate->getError());
        }
        $result = $this->where(['userId' => $userId])->setField('userPhoto', $param['userPhoto']);

        if (!empty($result)) {
            return SKReturn("修改成功！", 1, $param['userPhoto']);
        } else {
            return SKReturn("新地址与原地址不能相同,修改失败！");
        }
    }

    /**
     * 用户昵称修改
     * @author：zjf 2018/5/31
     * @param $userName 用户昵称
     */
    public function editName($param)
    {
        $must = ["userName"];
        $mustParams = mustParams($must, trimArray($param));
        if (!$mustParams) {
            return SKReturn("必填参数都不能为空");
        }
        $userId = userId();
        $validate = Loader::validate("users", "validate", false, "common");
        $result = $validate->scene('userName')->check(['userName' => $param["userName"]]);
        if (!$result) {
            return SKReturn($validate->getError());
        }
        $result = $this->where(['userId' => $userId])->setField('userName', $param['userName']);
        if (!empty($result)) {
            return SKReturn("修改成功！", 1, $param['userName']);
        } else {
            return SKReturn("修改失败！");
        }
    }

    /**
     * 指纹，面部识别登录,支付开启关闭
     * @author：zjf 2018/6/4
     * @param $password 密码
     * @param $type 设置类型（1：登录；2：支付；）
     */
    public function switchs($param)
    {
        $must = ["password", "type"];
        $mustParams = mustParams($must, trimArray($param));
        if (!$mustParams) {
            return SKReturn("必填参数都不能为空");
        }
        $param['userId'] = userId();

        $deviceId = $_SERVER['HTTP_DEVICEID'];

        if ($param['type'] == 1) {
            $field = "loginPwd";
        } else {
            $field = "payPwd";
        }
        $user = Db::name("users")->field("loginStatus,payStatus")->where(['userId' => $param['userId'], 'deviceId' => $deviceId])->find();
        if (empty($user)) {
            return SKReturn("没有找到该手机设备号，请重新登录");
        }
        $verifyResult = checkPwd($param['userId'], $param['password'], $field);
        if ($verifyResult['status'] != 1) {
            return SKReturn($verifyResult['msg']);
        }
        switch ($param['type']) {
            case 1:
                if ($user['loginStatus'] == 0) {
                    $msg = "登录开启成功";
                    $status = 1;
                } else {
                    $msg = "登录关闭成功";
                    $status = 0;
                }
                $field = 'loginStatus';
                break;
            case 2:
                if ($user['payStatus'] == 0) {
                    $msg = "支付开启成功";
                    $status = 1;
                } else {
                    $msg = "支付关闭成功";
                    $status = 0;
                }
                $field = 'payStatus';
                break;
        }
        $result = Db::name("users")->where(['userId' => $param['userId']])->update([$field => $status]);
        if (!empty($result)) {
            return SKReturn($msg, 1);
        } else {
            return SKReturn("操作失败");
        }
    }

}