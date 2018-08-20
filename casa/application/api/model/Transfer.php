<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/5/31 0031
 * Time: 下午 5:16
 */

namespace app\api\model;

use think\Db;
use think\Exception;
use think\Loader;
use think\Queue;

class Transfer extends Base
{
    /**
     * 填写转账金额页面所需数据
     * @author：zjf 2018/6/7
     * @param $userId 付款人userId
     * @param $userPhone 收款人手机号码
     */
    public function userData($param)
    {
        $must = ["userPhone"];
        $mustParams = mustParams($must, trimArray($param));
        if (!$mustParams) {
            return SKReturn("必填参数都不能为空");
        }
        $param['userId'] = userId();
        $datas = [
            'userPhone' => $param["userPhone"],
            'userId' => $param["userId"],
        ];
        $validate = Loader::validate("Transfer", "validate", false, "common");

        $result = $validate->scene('userPhone')->check($datas);
        if (!$result) {
            return SKReturn($validate->getError());
        }
        $user = Db::name("users")->field("userId,userName,userPhone,userPhoto")->where(['userPhone' => $param['userPhone']])->find();
        $black = Db::name("users_coin")->where(['userId' => $param['userId'], 'coin' => "CASA"])->value('black');
        $data = [
            "userName" => !empty($user['userName']) ? $user['userName'] : "",
            "userPhone" => phoneHide($user['userPhone']),
        ];

        $data['num'] = $black;

        return SKReturn("获取成功", 1, $data);
    }

    /**
     * 用户转账数量验证
     * @author：zjf 2018/5/31
     * @param $num 币的数量
     */
    public function numVerify($param)
    {
        $must = ["num"];
        $mustParams = mustParams($must, trimArray($param));
        if (!$mustParams) {
            return SKReturn("必填参数都不能为空");
        }
        $param['userId'] = userId();
        $data = [
            'num' => $param["num"],
            'userId' => $param["userId"],
        ];
        $validate = Loader::validate("Transfer", "validate", false, "common");
        $result = $validate->scene('num')->check($data);
        if (!$result) {
            return SKReturn($validate->getError());
        }
        return SKReturn("验证成功", 1);
    }

    /**
     * 用户转账提交
     * @author：zjf 2018/5/31
     * @param $userId 付款人userId
     * @param $userPhone 收款人手机号码
     * @param $num 币的数量
     * @param $payPwd 付款人交易密码
     * @param $type 支付类型(1：转账；2：收款)
     */
    public function transferSubmit($param)
    {
        $must = ["userPhone", "num", "payPwd", "type"];
        $mustParams = mustParams($must, trimArray($param));
        if (!$mustParams) {
            return SKReturn("必填参数都不能为空");
        }
        $userId = userId();
        $user = Db::name("users_realname")->where(['userId' => $userId, 'checkStatus' => 1])->count();
        if (empty($user)) {
            return SKReturn("您还未进行实名认证！");
        }

        $num = $param["num"];
        $data = [
            'num' => $param["num"],
            'userId' => $userId,
            'userPhone' => $param["userPhone"],
        ];
        $validate = Loader::validate("Transfer", "validate", false, "api");
        $result = $validate->scene('transfer')->check($data);
        if (!$result) {
            return SKReturn($validate->getError());
        }
        $result = checkPwd($userId, $param['payPwd'], "payPwd");
        if ($result['status'] != 1) {
            return SKReturn($result['msg']);
        }
        $acceptUser = Db::name("users")->field("userId,userName,userPhone")->where(['userPhone' => $param["userPhone"]])->find();
        $acceptUserId = $acceptUser['userId'];
        $date = timeFormat(2);
        $time = timeFormat();
        $fee = SysConfig('Fee');

        $orderNo = SKOrderSn('c');
        if ($param['type'] == 1) {
            $remark = "转账成功";
        } else {
            $remark = "收款成功";
        }
        $feeNum = bcmul($num, $fee / 100, 4);    //手续费
        $surNum = bcsub($num, $feeNum, 4); //实际到账
        $transfers = [
            "orderNo" => $orderNo,
            "sendId" => $userId,
            "userId" => $acceptUserId,
            "type" => $param['type'],
            "total" => $num,
            "fee" => $feeNum,
            "num" => $surNum,
            "remark" => $remark,
            "createTime" => $time,
        ];
        Db::startTrans();
        try {
            Db::name("transfer_user")->insert($transfers);

            insertLog('CASA', $userId, $orderNo, 4, '-' . $surNum, 0, 0, "", $time);
            Db::name("users_coin")->where(['userId' => $userId, 'coin' => 'CASA'])->setDec('black',$num);

            insertLog('CASA', $acceptUserId, $orderNo, 3, $surNum, 0, 0, "", $time);
            Db::name("users_coin")->where(['userId' => $acceptUserId, 'coin' => 'CASA'])->setInc('black',$surNum);

            Db::commit();
            $data = [
                "orderNo" => $orderNo,
                "msg" => "支付成功",
                "userName" => !empty($acceptUser['userName']) ? $acceptUser['userName'] : "",
                "userPhone" => phoneHide($acceptUser['userPhone']),
                "num" => $num,
                "realNum" => $surNum,
                "createTime" => $time,
            ];
            return SKReturn('成功', 1, $data);
        } catch (\Exception $e) {
            Db::rollback();
            if (config("app_debug")) {
                $msg = "[Line:]" . $e->getLine() . "[Msg:]" . $e->getMessage();
            } else {
                $msg = $e->getMessage();
            }
            return SKReturn($msg);
        }
    }
}