<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/8/1
 * Time: 10:26
 */

namespace app\api\controller;

use think\Log;
use app\api\model\Register as R;

class Register extends Base
{
    /**
     * 手机注册（1）
     * @author zjf 2018/5/29
     */
    public function phoneRegisterOne ($param) {
        try {
            $result = optBing("phoneRegisterOne",$param['userPhone'],0);
            if (empty($result)) {
                return SKReturn("业务正在处理中，请稍后");
            }
            $object = new R();
            $result = $object->phoneRegisterOne($param);
        } catch (\Exception $e) {
            Log::logger("app注册提交1", "[Line:]" . $e->getLine() . "[Msg:]" . $e->getMessage(), "phoneRegister");
            if (config("app_debug")) {
                $msg = "[Line:]" . $e->getLine() . "[Msg:]" . $e->getMessage();
            } else {
                $msg = "操作异常";
            }
            $result = SKReturn($msg);
        }
        optBing("phoneRegisterOne",$param['userPhone'],1);
        return $result;
    }

    /**
     * 手机注册（2）
     * @author zjf 2018/5/29
     */
    public function phoneRegisterTwo ($param) {
        try {
            $result = optBing("phoneRegisterTwo",$param['userPhone'],0);
            if (empty($result)) {
                return SKReturn("业务正在处理中，请稍后");
            }
            $object = new R();
            $result = $object->phoneRegisterTwo($param);
        } catch (\Exception $e) {
            Log::logger("app注册提交2", "[Line:]" . $e->getLine() . "[Msg:]" . $e->getMessage(), "phoneRegister");
            if (config("app_debug")) {
                $msg = "[Line:]" . $e->getLine() . "[Msg:]" . $e->getMessage();
            } else {
                $msg = "操作异常";
            }
            $result = SKReturn($msg);
        }
        optBing("phoneRegisterTwo",$param['userPhone'],1);
        return $result;
    }

    /*
     * H5注册提交
     * @author：zjf 2018/6/25
     */
    public function registerSumbit($param)
    {
        try {
            $result = optBing("registerSumbit", $param['userPhone'], 0);
            if (empty($result)) {
                return SKReturn("业务正在处理中，请稍后");
            }
            $object = new R();
            $result = $object->pcRegister($param);
        } catch (\Exception $e) {
            Log::logger("pc注册提交", "[Line:]" . $e->getLine() . "[Msg:]" . $e->getMessage(), "registerSumbit");
            if (config("app_debug")) {
                $msg = "[Line:]" . $e->getLine() . "[Msg:]" . $e->getMessage();
            } else {
                $msg = "操作异常";
            }
            $result = SKReturn($msg);
        }
        optBing("registerSumbit", $param['userPhone'], 1);
        return $result;
    }
}