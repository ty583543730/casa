<?php

namespace app\api\model;

use appVerify\Image;
use think\Db;
use sms\Sms;

class Site extends Base
{
    /**
     *  验证图形验证码并发送短信
     * @param  $userPhone  用户手机号
     * @param  $sendCode  短信主题代码
     * @param  $userId  用户Id
     *
     */
    public function sendSmsByCode($param)
    {
        //检验验证码
        if (!empty($param['isImage'])) {
            if ($param['isImage'] == 1) {
                if (empty($param['imageCode'])) {
                    return SKReturn("缺少验证码");
                } else {
                    if (!empty($param['resource'])) {
                        if (!Image::checkCode("app_logic", $param['imageCode'], true)) {
                            return SKReturn("图形验证码验证失败");
                        }
                    } else {
                        if (!checkVerify($param['imageCode'])) {
                            return SKReturn("图形验证码验证失败");
                        }
                    }
                }
            }
        }
        if (empty($param['phoneArea'])) {
            $phoneArea = 86;
        }else{
            $phoneArea = $param['phoneArea'];
        }
        if (empty($param['userPhone'])) {
            return SKReturn("缺少手机号码");
        } else {
            if (!SKIsPhone($param['userPhone'])) {
                return SKReturn("手机号格式不正确");
            }
            $userPhone = $param['userPhone'];
        }
        if (empty($param['function'])) {
            return SKReturn("短信类型不能为空");
        } else {
            $function = $param['function'];
        }
        //根据发送的类型先进行校验
        switch ($function) {
            case 'login':
                $user = Db::name("users")->where(['userPhone' => $userPhone])->field("userId,phoneArea")->find();
                if (empty($user)) {
                    return SKReturn("该手机号码还未注册");
                }
                break;
            case 'register':
                $user = Db::name("users")->where(['userPhone' => $userPhone])->field("userId")->find();
                if (!empty($user)) {
                    return SKReturn("该手机号码已经被绑定");
                }
                break;
            case 'findLoginPwd':
                $user = Db::name("users")->where(['userPhone' => $userPhone])->field("userId,phoneArea")->find();
                if (empty($user)) {
                    return SKReturn("该手机号码不存在");
                }
                break;
            case 'findPayPwd':
                $user = Db::name("users")->where(['userPhone' => $userPhone])->field("userId,phoneArea")->find();
                if (empty($user)) {
                    return SKReturn("该手机号码不存在");
                }
                break;
            default :
                break;
        }

        if (!empty($user)) {
            $userId = $user['userId'];
            $phoneArea = $user['phoneArea'];
        }
        if(empty($userId)){
            $userId = config("SK_USER.userId") ? config("SK_USER.userId") : 0;
        }
        $alimns = new Sms();
        $result = $alimns->sendSms($userPhone, $function, $userId, $phoneArea);
        if ($result['status'] == 1) {
            return SKReturn("发送成功", 1);
        } else {
            return SKReturn($result['msg']);
        }
    }

    /**
     *  获得区号列表
     *  add by zjf 2018/4/12
     */
    public function country()
    {
        $country = Db::name("country")->where(['status' => 1, "dataFlag" => 1])->field("number,cnName,enName")->select();
        return SKReturn("", 1, $country);
    }

}
