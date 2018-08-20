<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/4/3 0003
 * Time: 上午 11:15
 */

namespace app\api\model;

use think\Db;
use sms\Sms;
use think\Exception;

class Login extends Base
{
    /**
     * 用户登录验证
     * @param $userPhone 手机号码
     * @param loginPwd 登录密码
     */
    public function loginOne($param)
    {
        $must = ["userPhone", "loginPwd"];
        $mustParams = mustParams($must, trimArray($param));
        if (!$mustParams) {
            return SKReturn("必填参数都不能为空");
        }
        //到今天凌晨剩余的时间
        $remaining = strtotime(date("Y-m-d", strtotime("+1 day"))) - time();

        $userPhone = $param['userPhone'];
        $loginPwd = $param['loginPwd'];
        $deviceId = $_SERVER['HTTP_DEVICEID'];
        $loginFailNum = cache("api_login:failnumber:" . $userPhone . $deviceId);
        if ($loginFailNum > 9) {
            return SKReturn("错误次数超过10次，今天不可再登录!");
        }
        $result = Db::name("users")->where("userPhone", $userPhone)->find();
        if (!empty($result)) {
            if ($result['userStatus'] == -1) {
                return SKReturn("账号被锁定！");
            }
            if (md5($loginPwd . $result["loginSecret"]) != $result['loginPwd']) {
                cache("api_login:failnumber:" . $userPhone . $deviceId, $loginFailNum * 1 + 1, $remaining);
                return SKReturn("账号或密码错误，请重试！");
            }
            //重置登录失败次数
            cache("api_login:failnumber:" . $userPhone . $deviceId, null);
            //非首次登录不要验证码
            $loginNum = Db::name('log_users_logins')->where('userId', $result['userId'])->count();
            if ($loginNum > 0) {
                Db::startTrans();
                try {
                    $user = $result;
                    //设备号不相等，关闭登录，支付
                    if ($user['deviceId'] != $deviceId) {
                        $update = [
                            'deviceId' => $deviceId,
                            "loginStatus" => 0,
                            "payStatus" => 0,
                        ];
                        Db::name("users")->where(['userId' => $user['userId']])->update($update);
                    }

                    SKUnset($user, "loginSecret,loginPwd,payPwd");
                    $user['deviceId'] = $deviceId;
                    $user['token'] = uniqid(rand(), TRUE);

                    //记录登录日志
                    $log_users_logins = [
                        "userId" => $user['userId'],
                        "loginTime" => timeFormat(),
                        "loginIp" => request()->ip(0, true),
                        "loginTer" => "2",
                        "loginRemark" => $user['token'],
                    ];
                    Db::name('log_users_logins')->insert($log_users_logins);

                    Db::commit();

                    $this->loginSet($user);
                    $user['isFirst'] = false;
                    $user['userTypeName'] = getUserTypeName($user['userType']);
                    return SKReturn("登录成功", 1, $user);
                } catch (\Exception $e) {
                    Db::rollback();
                    return SKReturn($e->getMessage());
                }
            } else {
                return SKReturn("验证成功", 1, ['isFirst' => true]);
            }
        }
        cache("api_login:failnumber:" . $userPhone . $deviceId, $loginFailNum * 1 + 1, $remaining);
        return SKReturn("账号或密码错误，请重试！");
    }

    /**
     * 用户登录验证
     * @param $userPhone 手机号码
     * @param $mobileCode 手机短信验证码
     * @param loginPwd 登录密码
     */
    public function loginTwo($param)
    {
        $must = ["userPhone", "loginPwd", "mobileCode"];
        $mustParams = mustParams($must, trimArray($param));
        if (!$mustParams) {
            return SKReturn("必填参数都不能为空");
        }
        $userPhone = $param['userPhone'];
        $deviceId = $_SERVER['HTTP_DEVICEID'];
        //校验短信
        $ls = new Sms();
        /* $res = $ls->verifySms($userPhone, $param['mobileCode'], "login");
         if (!$res) {
             return SKReturn("短信验证码错误");
         }*/
        $user = Db::name("users")->where("userPhone", $userPhone)->find();
        Db::startTrans();
        try {
            //设备号不相等，关闭登录，支付
            if ($user['deviceId'] != $deviceId) {
                $update = [
                    'deviceId' => $deviceId,
                    "loginStatus" => 0,
                    "payStatus" => 0,
                ];
            } else {
                $update = [
                    'deviceId' => $deviceId,
                ];
            }
            Db::name("users")->where(['userId' => $user['userId']])->update($update);

            SKUnset($user, "loginSecret,loginPwd,payPwd");
            $user['deviceId'] = $deviceId;
            $user['token'] = uniqid(rand(), TRUE);

            //记录登录日志
            $log_users_logins = [
                "userId" => $user['userId'],
                "loginTime" => timeFormat(),
                "loginIp" => request()->ip(0, true),
                "loginTer" => "2",
                "loginRemark" => $user['token'],
            ];
            Db::name('log_users_logins')->insert($log_users_logins);

            Db::commit();
            $this->loginSet($user);
            $user['userTypeName'] = getUserTypeName($user['userType']);
            return SKReturn("登录成功", 1, $user);
        } catch (Exception $e) {
            Db::rollback();
            return SKReturn($e->getMessage());
        }
    }

    /**
     * 指纹，面部识别登录
     * @author zjf 2018/6/4
     * @param deviceId 手机设备号
     */
    public function otherLogin($param)
    {
        $must = ["deviceId"];
        $mustParams = mustParams($must, trimArray($param));
        if (!$mustParams) {
            return SKReturn("必填参数都不能为空");
        }
        $user = Db::name("users")->where(['deviceId' => $param['deviceId']])->count();
        if (empty($user)) {
            return SKReturn("该手机设备号不存在，请使用密码登录");
        }
        $user = Db::name("users")->where(['deviceId' => $param['deviceId'], "loginStatus" => 1])->find();
        if (empty($user)) {
            return SKReturn("没有开启登录功能，请使用密码登录");
        }
        SKUnset($user, "loginSecret,loginPwd,payPwd");
        $user['token'] = uniqid(rand(), TRUE);

        //记录登录日志
        $log_users_logins = [
            "userId" => $user['userId'],
            "loginTime" => timeFormat(),
            "loginIp" => request()->ip(0, true),
            "loginTer" => "2",
            "loginRemark" => $user['token'],
        ];
        Db::name('log_users_logins')->insert($log_users_logins);

        $this->loginSet($user);
        return SKReturn("登录成功", 1, $user);
    }


    /**
     * 用户退出
     * @param $userId 用户id
     * @return array
     */
    public function loginOut($userId)
    {
        // 清空登录信息
        cache("client:userInfoByToken:" . $_SERVER["HTTP_TOKEN"], null);
        //清空存用户在app端的唯一登录码。防一用户多登录
        cache("client:soleflag:" . $userId, null);
        return SKReturn("退出成功", 1);
    }

    /**
     * APP写入登录信息
     * @param $user
     * @return bool
     */
    public function loginSet(array $user)
    {
        $user["login_time"] = time();
        $user["token_id"] = $_SERVER["HTTP_DEVICEID"];
        // 存登录信息
        cache("client:userInfoByToken:" . $user['token'], $user, 604800);
        //存用户在app端的唯一登录码。防一用户多登录
        cache("client:soleflag:" . $user['userId'], $user["token"] . "-" . $_SERVER["HTTP_DEVICEID"]);
        return true;
    }

    /**
     * 获取登录信息
     * @return bool
     */
    public function loginGet()
    {

        if (empty($_SERVER["HTTP_TOKEN"])) {
            return false;
        }
        $user = cache("client:userInfoByToken:" . $_SERVER["HTTP_TOKEN"]);
        // 未找到缓存登录信息，不允许登录
        if (empty($user) || $user === false) {
            return false;
        }
        // 验证是否合法的登录,有可能会同账号在其他app上登录.
        $soleflag = cache("client:soleflag:" . $user['userId']);
        if ($soleflag != $_SERVER["HTTP_TOKEN"] . "-" . $_SERVER["HTTP_DEVICEID"]) {
            config("uid", -100);
            cache("client:userInfoByToken:" . $_SERVER["HTTP_TOKEN"], null);
            return false;
        }
        //如果登录状态还剩下10个小时，续一下时间，
        if (time() - $user["login_time"] > 604200) {
            $this->loginSet($user);
        }

        if (is_array($user)) {
            config("uid", $user["userId"]);
            config("sk_user", $user);
        }
        return $user;
    }

}