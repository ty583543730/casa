<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/3/16 0016
 * Time: 下午 5:47
 */

namespace app\api\model;

use Think\Db;
use think\Loader;
use sms\Sms;

class Password extends Base
{


    /**
     *  找回密码更新密码
     * @author zjf 2018/5/29
     * @param  userPhone                手机号码
     * @param  loginPwd                 登录密码
     * @param  password_confirm         登录确认密码
     */
    public function retrievePwd($param)
    {
        $must = ["userPhone","loginPwd","password_confirm"];
        $mustParams = mustParams($must, trimArray($param));
        if (!$mustParams) {
            return SKReturn("必填参数都不能为空");
        }

        $data = [
            'loginPwd' => $param["loginPwd"],
            'password_confirm' => $param["password_confirm"],
        ];
        $validate = Loader::validate("users", "validate", false, "common");
        $result = $validate->scene('loginPwd')->check($data);
        if (!$result) {
            return SKReturn($validate->getError());
        }
        $user = Db::name("users")->where(['userPhone' => $param['userPhone']])->field("userId,loginSecret,loginPwd")->find();
        if (empty($user)) {
            return SKReturn("该手机号码不存在");
        }
        $newPwd = md5($param['loginPwd'] . $user['loginSecret']);
        if ($user['loginPwd'] === $newPwd) {
            return SKReturn("新密码不能跟原密码相同");
        }

        $result = Db::name("users")->where(["userId" => $user['userId']])->setField('loginPwd',$newPwd);
        if ($result) {
            return SKReturn("找回密码成功", 1);
        } else {
            return SKReturn("找回密码失败");
        }
    }

    /**
     *  修改登录密码
     * @author zjf 2018/5/29
     * @param  userId                   用户ID
     * @param  oldPwd                   原始密码
     * @param  loginPwd                 新登录密码
     * @param  password_confirm         新登录确认密码
     */
    public function modifyPwd($param)
    {
        $must = ["oldPwd","loginPwd","password_confirm"];
        $mustParams = mustParams($must, trimArray($param));
        if (!$mustParams) {
            return SKReturn("必填参数都不能为空");
        }
        $param['userId'] = userId();
        $user = Db::name("users")->where(['userId' => $param['userId']])->field("loginPwd,loginSecret")->find();
        $newPwd = md5($param['loginPwd'] . $user['loginSecret']);
        $verifyResult = checkPwd($param['userId'], $param['oldPwd'], "loginPwd");
        if ($verifyResult['status'] != 1) {
            return SKReturn("原密码不正确");
        }
        $data = [
            'loginPwd' => $param["loginPwd"],
            'password_confirm' => $param["password_confirm"],
        ];
        $validate = Loader::validate("users", "validate", false, "common");
        $result = $validate->scene('loginPwd')->check($data);
        if (!$result) {
            return SKReturn($validate->getError());
        }
        if ($user['loginPwd'] === $newPwd) {
            return SKReturn("新密码不能跟原密码相同");
        }
        $result = Db::name("users")->where('userId',$param['userId'])->setField("loginPwd",$newPwd);
        if ($result) {
            return SKReturn("登录密码修改成功", 1);
        } else {
            return SKReturn("登录密码修改失败");
        }
    }

    /**
     *  修改交易密码
     * @author zjf 2018/5/29
     * @param  userId                   用户ID
     * @param  mobileCode               短信验证码
     * @param  payPwd                   交易密码
     * @param  password_confirm         交易密码确认密码
     */
    public function modifyPayPwd($param)
    {
        $must = ["mobileCode","payPwd","password_confirm"];
        $mustParams = mustParams($must, trimArray($param));
        if (!$mustParams) {
            return SKReturn("必填参数都不能为空");
        }
        $param['userId'] = userId();

        $data = [
            'payPwd' => $param["payPwd"],
            'password_confirm' => $param["password_confirm"],
        ];
        $validate = Loader::validate("users", "validate", false, "common");
        $result = $validate->scene('payPwd')->check($data);
        if (!$result) {
            return SKReturn($validate->getError());
        }
        $user = Db::name("users")->where(['userId' => $param['userId']])->field("userPhone,loginSecret,payPwd")->find();
        $newPwd = md5($param['payPwd'] . $user['loginSecret']);
        if ($user['payPwd'] === $newPwd) {
            return SKReturn("新密码不能跟原密码相同");
        }
        //校验短信验证码
        $ls = new Sms();
        $result = $ls->verifySms($user['userPhone'], $param['mobileCode'], "findPayPwd");
        if (!$result) {
            return SKReturn("短信验证码错误");
        }
        $result = Db::name("users")->where('userId',$param['userId'])->setField("payPwd",$newPwd);
        if ($result) {
            return SKReturn("交易密码修改成功", 1);
        } else {
            return SKReturn("交易密码修改失败");
        }
    }

    /**
     * 指纹，面部识别支付
     * @author zjf 2018/6/4
     * @param userId 用户ID
     * @param deviceId 手机设备号
     */
    public function otherPay($param)
    {
        $must = ["deviceId"];
        $mustParams = mustParams($must, trimArray($param));
        if (!$mustParams) {
            return SKReturn("必填参数都不能为空");
        }
        $user = Db::name("users")->where(['deviceId' => $param['deviceId']])->count();
        if (empty($user)) {
            return SKReturn("该手机设备号不存在，请尝试使用密码重新登录");
        }
        $user = Db::name("users")->where(['userId' => $param['userId'],'deviceId' => $param['deviceId'],"payStatus" => 1])->count();
        if (empty($user)) {
            return SKReturn("该账户没有开启支付功能，请尝试使用密码重新登录");
        }
        return SKReturn("验证成功", 1);
    }
}