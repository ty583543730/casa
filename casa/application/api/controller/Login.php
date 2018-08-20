<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/4/2 0002
 * Time: 上午 11:41
 */

namespace app\api\controller;

use think\Log;
use app\api\model\Login as L;
class Login
{

    /**
     * 客户端用户登录(1)
     * @author zjf 2018/5/29
     */
    public function loginOne($param)
    {
        try {
            $result = optBing("loginOne",$param['userPhone'],0);
            if (empty($result)) {
                return SKReturn("业务正在处理中，请稍后");
            }
            $user = new L();
            $result = $user->loginOne($param);
        } catch (\Exception $e) {
            Log::logger("客户端用户登录(1)", "[Line:]" . $e->getLine() . "[Msg:]" . $e->getMessage(), "checkLogin");
            if (config("app_debug")) {
                $msg = "[Line:]" . $e->getLine() . "[Msg:]" . $e->getMessage();
            } else {
                $msg = "操作异常";
            }
            $result = SKReturn($msg);
        }
        optBing("loginOne",$param['userPhone']);
        return $result;
    }

    /**
     * 客户端用户登录(2)
     * @author zjf 2018/5/29
     */
    public function loginTwo($param)
    {
        try {
            $result = optBing("loginTwo",$param['userPhone'],0);
            if (empty($result)) {
                return SKReturn("业务正在处理中，请稍后");
            }
            $user = new L();
            $result = $user->loginTwo($param);
        } catch (\Exception $e) {
            Log::logger("客户端用户登录(2)", "[Line:]" . $e->getLine() . "[Msg:]" . $e->getMessage(), "checkLogin");
            if (config("app_debug")) {
                $msg = "[Line:]" . $e->getLine() . "[Msg:]" . $e->getMessage();
            } else {
                $msg = "操作异常";
            }
            $result = SKReturn($msg);
        }
        optBing("loginTwo",$param['userPhone']);
        return $result;
    }

    /**
     * 指纹，面部识别登录
     * @author zjf 2018/6/4
     */
    public function otherLogin()
    {
        try {
            $param['deviceId'] = $_SERVER['HTTP_DEVICEID'];
            $result = optBing("otherLogin",$param['deviceId'],0);
            if (empty($result)) {
                return SKReturn("业务正在处理中，请稍后");
            }
            $user = new L();
            $result = $user->otherLogin($param);
        } catch (\Exception $e) {
            Log::logger("指纹，面部识别登录", "[Line:]" . $e->getLine() . "[Msg:]" . $e->getMessage(), "otherLogin");
            if (config("app_debug")) {
                $msg = "[Line:]" . $e->getLine() . "[Msg:]" . $e->getMessage();
            } else {
                $msg = "操作异常";
            }
            $result = SKReturn($msg);
        }
        optBing("otherLogin",$param['deviceId']);
        return $result;
    }

    /**
     * 客户端用户退出
     * @author zjf 2018/5/29
     */
    public function loginOut()
    {
        try {
            ISLogin();
            $user = new L();
            $result = $user->loginOut(userId());
        } catch (\Exception $e) {
            Log::logger("客户端用户退出", "[Line:]" . $e->getLine() . "[Msg:]" . $e->getMessage(), "loginOut");
            if (config("app_debug")) {
                $msg = "[Line:]" . $e->getLine() . "[Msg:]" . $e->getMessage();
            } else {
                $msg = "操作异常";
            }
            $result = SKReturn($msg);
        }
        return $result;
    }

}