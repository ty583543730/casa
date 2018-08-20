<?php
/**
 * 注册model
 * User: yt
 * Date: 2018/7/10 0010
 * Time: 上午 11:44
 */

namespace app\api\model;

use think\Db;
use think\Loader;
use sms\Sms;

class Register extends Base
{
    /**
     *  APP端手机注册（1）
     * @author zjf 2018/5/29
     * @param  phoneArea            手机区号
     * @param  userPhone            手机号码
     * @param  invitePhone          邀请人手机号码
     * @param  mobileCode           短信验证码
     */
    public function phoneRegisterOne($param)
    {
        $must= ["phoneArea","userPhone","mobileCode"];
        $mustParams = mustParams($must, trimArray($param));
        if (!$mustParams) {
            return SKReturn("必填参数都不能为空");
        }
        if (!SKIsPhone($param['userPhone'])) {
            return SKReturn("手机号格式不正确");
        }
        $userId = Db::name("users")->where(['userPhone' => $param['userPhone']])->value("userId");
        if (!empty($userId)) {
            return SKReturn("该手机号码已经存在");
        }
        $numbers = Db::name("country")->where(['number' => $param['phoneArea']])->count();
        if (empty($numbers)) {
            return SKReturn("区号选择有误");
        }
        if (!empty($param['invitePhone'])) {
            if ($param['invitePhone'] == $param['userPhone']) {
                return SKReturn("推荐人手机号码只能是他人手机号码");
            }
            $userId = Db::name("users")->where(['userPhone' => $param['invitePhone']])->value("userId");
            if (empty($userId)) {
                return SKReturn("推荐人手机号码不存在");
            }
        }
        if (!config("app_debug")) {
            //校验短信
            $ls = new Sms();
            $result = $ls->verifySms($param['userPhone'], $param['mobileCode'], "register");
            if (!$result) {
                return SKReturn("短信验证码错误");
            }
        }
        return SKReturn("验证成功", 1);
    }

    /**
     * 手机注册（2）
     * @author zjf 2018/5/29
     * @param  loginPwd             密码
     * @param  password_confirm     确认密码
     * @param  phoneArea            手机区号
     * @param  userPhone            手机号码
     * @param  invitePhone          邀请人手机号码
     * @param  regTerminal 1pc 2H5 3ios 4 android
     */
    public function phoneRegisterTwo($param)
    {
        $must= ["loginPwd","password_confirm","phoneArea","userPhone","regTerminal"];
        $mustParams = mustParams($must, trimArray($param));
        if (!$mustParams) {
            return SKReturn("必填参数都不能为空");
        }
        $time = timeFormat();
        if (!SKIsPhone($param['userPhone'])) {
            return SKReturn("手机号格式不正确");
        }
        $userId = Db::name("users")->where(['userPhone' => $param['userPhone']])->value("userId");
        if (!empty($userId)) {
            return SKReturn("该手机号码已经存在");
        }
        $numbers = Db::name("country")->where(['number' => $param['phoneArea']])->count();
        if (empty($numbers)) {
            return SKReturn("区号选择有误");
        }
        //推荐人处理
        if (!empty($param['invitePhone'])) {
            if ($param['invitePhone'] == $param['userPhone']) {
                return SKReturn("推荐人手机号码只能是他人手机号码");
            }
            $inviteId = Db::name("users")->where(['userPhone' => $param['invitePhone']])->value("userId");
            if (empty($inviteId)) {
                return SKReturn("推荐人手机号码不存在");
            }
        } else {
            $inviteId = Db::name("users")->where(['userPhone' => SysConfig("invitePhone")])->value("userId");
            if (empty($inviteId)) {
                $inviteId = 0;
            }
        }
        $data = [
            'loginPwd' => $param["loginPwd"],
            'password_confirm' => $param["password_confirm"],
        ];
        $validate = Loader::validate("users", "validate", false, "api");
        $result = $validate->scene('loginPwd')->check($data);
        if (!$result) {
            return SKReturn($validate->getError());
        }
        $loginSecret = rand(1000, 9999);
        $userNo=substr(md5($param["loginPwd"] . $loginSecret), 0, 10);
        $userData = [
            "phoneArea" => $param['phoneArea'],
            "userPhone" => $param['userPhone'],
            "userName" => "KS".$userNo,
            "loginSecret" => $loginSecret,
            "loginPwd" => md5($param["loginPwd"] . $loginSecret),
            "parentId" => $inviteId,
            "userNo" => $userNo,
            "userPhoto" => SysConfig('userLogo'),
            "terminal" => $param['regTerminal'],
            "createIp" => request()->ip(0, true),
            "createTime" => $time,
        ];

        $coin = Db::name("coin")->where(['status' => 1, 'dataFlag' => 1])->select();
        Db::startTrans();
        try {
            $result = Db::name("users")->insertGetId($userData);
            foreach ($coin as $k => $v) {
                $insert = [
                    "userId" => $result,
                    "coin" => $v['coin'],
                ];
                $insertAll[] = $insert;
            }
            $messages = [
                "userId" => $result,
                "msgTitle" => "用户注册成功",
                "msgContent" => "尊敬的用户，您好。您于" . $time . "注册成功",
                "msgStatus" => 0,
                "createTime" => $time,
            ];
            Db::name("users_coin")->insertAll($insertAll);
            Db::name("users_extend")->insert(["userId" => $result]);
            Db::name("sys_msgs")->insert($messages);
            if(!empty($inviteId)){
                Db::name('users_extend')->where(['userId'=>$inviteId])->setInc('recommendNum');
            }

            Db::commit();

            return SKReturn("注册成功", 1);
        } catch (\Exception $e) {
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
     * PC端注册
     * @author zjf 2018/6/25
     * @param  protocol            服务协议
     * @param  phoneArea            手机区号
     * @param  userPhone            手机号码
     * @param  mobileCode           短信验证码
     * @param  loginPwd             密码
     * @param  password_confirm     确认密码
     * @param  invitePhone          邀请人手机号码
     * @param  regTerminal 1pc 2H5 3ios 4 android
     */
    public function pcRegister ($param) {
        $must= ["protocol","mobileCode","loginPwd","password_confirm","phoneArea","userPhone","regTerminal"];
        $mustParams = mustParams($must, trimArray($param));
        if (!$mustParams) {
            return SKReturn("必填参数都不能为空");
        }
        $time = timeFormat();
        if (!SKIsPhone($param['userPhone'])) {
            return SKReturn("手机号格式不正确");
        }
        $userId = Db::name("users")->where(['userPhone' => $param['userPhone']])->value("userId");
        if (!empty($userId)) {
            return SKReturn("该手机号码已经存在");
        }
        $numbers = Db::name("country")->where(['number' => $param['phoneArea']])->count();
        if (empty($numbers)) {
            return SKReturn("区号选择有误");
        }
        if (!empty($param['invitePhone'])) {
            if ($param['invitePhone'] == $param['userPhone']) {
                return SKReturn("推荐人手机号码只能是他人手机号码");
            }
            $userId = Db::name("users")->where(['userPhone' => $param['invitePhone']])->value("userId");
            if (empty($userId)) {
                return SKReturn("推荐人手机号码不存在");
            }
            $inviteId = $userId;
        } else {
            $userId = Db::name("users")->where(['userPhone' => SysConfig("invitePhone")])->value("userId");
            if (!empty($userId)) {
                $inviteId = $userId;
            } else {
                $inviteId = 0;
            }
        }
        $data = [
            'loginPwd' => $param["loginPwd"],
            'password_confirm' => $param["password_confirm"],
            'protocol' => $param["protocol"],
        ];
        $validate = Loader::validate("users", "validate", false, "common");
        $result = $validate->scene('pcRegister')->check($data);
        if (!$result) {
            return SKReturn($validate->getError());
        }
        //校验短信
        $ls = new Sms();
        $result = $ls->verifySms($param['userPhone'], $param['mobileCode'], "register");
        if (!$result) {
            return SKReturn("短信验证码错误");
        }
        $loginSecret = rand(1000, 9999);
        $userData = [
            "phoneArea" => $param['phoneArea'],
            "userPhone" => $param['userPhone'],
            "loginSecret" => $loginSecret,
            "loginPwd" => md5($param["loginPwd"] . $loginSecret),
            "parentId" => $inviteId,
            "userNo" => substr(md5($param["loginPwd"] . $loginSecret), 0, 10),
            "terminal" => $param['regTerminal'],
            "createIp" => request()->ip(0,true),
            "createTime" => $time,
        ];
        $coin = Db::name("coin")->where(['status' => 1, 'dataFlag' => 1])->select();
        Db::startTrans();
        try {
            $result = Db::name("users")->insertGetId($userData);
            foreach ($coin as $k => $v) {
                $insert = [
                    "userId" => $result,
                    "coin" => $v['coin'],
                ];
                $insertAll[] = $insert;
            }
            $messages = [
                "userId" => $result,
                "msgTitle" => "用户注册成功",
                "msgContent" => "尊敬的用户，您好。您于" . $time . "在平台注册成功",
                "msgStatus" => 0,
                "createTime" => $time,
            ];
            Db::name("users_coin")->insertAll($insertAll);
            Db::name("sys_msgs")->insert($messages);
            Db::commit();

            return SKReturn("注册成功", 1);
        } catch (\Exception $e) {
            Db::rollback();
            if (config("app_debug")) {
                $msg = "[Line:]" . $e->getLine() . "[Msg:]" . $e->getMessage();
            } else {
                $msg = "操作异常";
            }
            return SKReturn($msg);
        }
    }
}