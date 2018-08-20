<?php
/**
 * 密码相关
 * User: Administrator
 * Date: 2018/4/2 0002
 * Time: 上午 10:08
 */

namespace app\api\controller;

use think\Log;
use app\api\model\Password as CU;

class Password extends Base
{
    /**
     *  找回登录密码提交
     * @author zjf 2018/5/29
     */
    public function retrievePwd($param)
    {
        try {
            $result = optBing("retrievePwdTwo",$param['userPhone'],0);
            if (empty($result)) {
                return SKReturn("业务正在处理中，请稍后");
            }
            $m = new CU();
            $result = $m->retrievePwd($param);
        } catch (\Exception $e) {
            Log::logger("app找回登录密码(2)", "[Line:]" . $e->getLine() . "[Msg:]" . $e->getMessage(), "retrievePwd");
            if (config("app_debug")) {
                $msg = "[Line:]" . $e->getLine() . "[Msg:]" . $e->getMessage();
            } else {
                $msg = "操作异常";
            }
            $result = SKReturn($msg);
        }
        optBing("retrievePwdTwo",$param['userPhone'],1);
        return $result;
    }

    /**
     *  修改登录密码提交
     * @author zjf 2018/5/29
     */
    public function modifyPwd($param)
    {
        try {
            ISLogin();
            $param['userId'] = userId();
            $result = optBing("modifyPwd",$param['userId'],0);
            if (empty($result)) {
                return SKReturn("业务正在处理中，请稍后");
            }
            $m = new CU();
            $result = $m->modifyPwd($param);
        } catch (\Exception $e) {
            Log::logger("修改登录密码", "[Line:]" . $e->getLine() . "[Msg:]" . $e->getMessage(), "modifyPwd");
            if (config("app_debug")) {
                $msg = "[Line:]" . $e->getLine() . "[Msg:]" . $e->getMessage();
            } else {
                $msg = "操作异常";
            }
            $result = SKReturn($msg);
        }
        optBing("modifyPwd",$param['userId'],1);
        return $result;
    }

    /**
     *  修改交易密码提交
     * @author zjf 2018/5/29
     */
    public function modifyPayPwd($param)
    {
        try {
            ISLogin();
            $param['userId'] = userId();
            $result = optBing("modifyPayPwd",$param['userId'],0);
            if (empty($result)) {
                return SKReturn("业务正在处理中，请稍后");
            }
            $m = new CU();
            $result = $m->modifyPayPwd($param);
        } catch (\Exception $e) {
            Log::logger("修改交易密码", "[Line:]" . $e->getLine() . "[Msg:]" . $e->getMessage(), "modifyPayPwd");
            if (config("app_debug")) {
                $msg = "[Line:]" . $e->getLine() . "[Msg:]" . $e->getMessage();
            } else {
                $msg = "操作异常";
            }
            $result = SKReturn($msg);
        }
        optBing("modifyPayPwd",$param['userId'],1);
        return $result;
    }

    /**
     * 指纹，面部识别支付
     * @author zjf 2018/6/4
     */
    public function otherPay()
    {
        try {
            ISLogin();
            $param['deviceId'] = $_SERVER['HTTP_DEVICEID'];
            $param['userId'] = userId();
            $result = optBing("otherPay",$param['deviceId'],0);
            if (empty($result)) {
                return SKReturn("业务正在处理中，请稍后");
            }
            $m = new CU();
            $result = $m->otherPay($param);
        } catch (\Exception $e) {
            Log::logger("指纹，面部识别支付", "[Line:]" . $e->getLine() . "[Msg:]" . $e->getMessage(), "otherPay");
            if (config("app_debug")) {
                $msg = "[Line:]" . $e->getLine() . "[Msg:]" . $e->getMessage();
            } else {
                $msg = "操作异常";
            }
            $result = SKReturn($msg);
        }
        optBing("otherPay",$param['deviceId']);
        return $result;
    }
}