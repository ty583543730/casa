<?php
/**
 * 阿里云短信接口
 * User: yt
 * Date: 2018/6/20 0020
 * Time: 上午 10:23
 */

namespace sms\aliyunSms;

class api
{
    public function __construct()
    {
        $this->accessKeyId = "LTAIb5s4DcG5KKYF";
        $this->accessKeySecret = "eBGVOukevP9RMtI1eHnxlqPN3Gf7P2";
        $this->SignName = "大鱼测试";
        $this->OutId = "abcdefgh";//设置流水号，可选
        $this->SmsUpExtendCode = "123456";
    }

    public $TemplateCode = [
        "login" => "SMS_128055022", //登录
        "findLoginPwd" => "SMS_128055022",  //找回登录密码
        "findPayPwd" => "SMS_128055022",    //找回交易密码
        "register" => "SMS_128055022",  //注册
        "draw" => "SMS_128055022",  //提现
        "bindingMobile" => "SMS_128055022", //手机绑定
        "unBindMobile" => "SMS_128055022",  //手机解绑
        "bindingEmail" => "", //手机绑定
        "unBindEmail" => "",  //手机解绑
        "bindAccount" => "SMS_128055022",  //支付系统绑定交易所账号
    ];

    public function sendSms($phone, $function, $code)
    {
        if (empty($phone) || empty($function) || empty($code)) {
            return SKReturn('缺少必要参数！');
        }
        $params["PhoneNumbers"] = $phone;        // 短信接收号码
        $params["SignName"] = $this->SignName;        // 短信签名
        $params["TemplateCode"] = $this->TemplateCode["$function"];//短信模板Code
        $params['TemplateParam'] = Array(
            "code" => $code
        );
        //模板参数
        if (!empty($params["TemplateParam"]) && is_array($params["TemplateParam"])) {
            $params["TemplateParam"] = json_encode($params["TemplateParam"], JSON_UNESCAPED_UNICODE);
        }
        $params['OutId'] = $this->OutId;//发送短信流水号
        $params['SmsUpExtendCode'] = $this->SmsUpExtendCode;//上行短信扩展码
        // 初始化SignatureHelper实例用于设置参数，签名以及发送请求
        $helper = new SignatureHelper();
        try {
            $content = $helper->request(
                $this->accessKeyId,
                $this->accessKeySecret,
                "dysmsapi.aliyuncs.com",
                array_merge($params, array(
                    "RegionId" => "cn-hangzhou",
                    "Action" => "SendSms",
                    "Version" => "2017-05-25",
                ))
            );
            if ($content->Code == 'OK') {
                return SKReturn($content->Message, 1, ['code' => $content->Code, 'msg' => $content->Message, 'content' => $content->RequestId]);
            } else {
                return SKReturn($content->Message);
            }
        } catch (\Exception $e) {
            return SKReturn($e->getMessage());
        }

    }


}