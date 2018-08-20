<?php
/**
 * 短信服务
 * User: yt
 * Date: 2018/6/19 0019
 * Time: 下午 4:25
 */

namespace sms;

use redis\Native;
use think\Db;

class Sms
{
    /**
     * 发送短信
     * @param $phone 手机号码
     * @param $function 业务类型
     * @param int $userId 用户id
     * @param int $phoneArea 国家码
     * @param int $type 渠道类型 1fastoo 2aliyun
     * @return array
     */
    public function sendSms($phone, $function, $userId = 0, $phoneArea = 86, $type = 1)
    {

        if (empty($phone) || empty($function)) {
            return SKReturn("缺少参数");
        }

        //debug开启时 发送6个8
        if (config("app_debug")) {
            cache("VerifyCode_" . $function . $phone, 888888, 300);
            return SKReturn("发送成功", 1);
        }

        $redis = new Native();
        //检查手机号码是否是小于30秒发送
        $phoneSendTime = $redis->get('sendsms:phone:time:' . $phone);
        if (!empty($phoneSendTime) && ((time() - strtotime($phoneSendTime)) < 30)) {
            return SKReturn('30秒内该手机已发送过短信，请勿频繁发送，请稍后再发');
        }
        //检测是否超过每日短信发送数
        $phoneSendNum = $redis->get('sendsms:phone:nums:' . $phone);
        if (empty($phoneSendNum)) $phoneSendNum = 0;
        if ($phoneSendNum > (int)SysConfig("smsNumLimit")) {
            return SKReturn('该手机号码发送短信次数已超过系统设置，请联系客服');
        }
        //检测IP是否超过发短信次数 同一ip下是否是小于60秒发送
        $ip = request()->ip(0, true);
        $whiteListIp = ["113.88.178.72"];
        $ipSendNum = $redis->get('sendsms:ip:nums:' . $ip);
        if (empty($ipSendNum)) $ipSendNum = 0;
        $ipSendTime = $redis->get('sendsms:ip:time:' . $ip);
        if (!in_array($ip, $whiteListIp)) {
            if (!empty($ipSendTime) && ($ipSendNum > (int)SysConfig("smsIpLimit"))) {
                return SKReturn('该IP发送短信次数已超过系统设置，请联系客服');
            }
            if (!empty($ipSendTime) && ((time() - strtotime($ipSendTime)) < 30)) {
                return SKReturn('30秒内该IP已发送过短信，请勿频繁发送，请稍后再发');
            }
        }
        $code = rand(111111, 999999);
        $userName = '';
        if ($userId != 0) {
            $userName = Db::name('users')->where(['userId' => $userId])->value('userName');
        }
        //对接不同的短信渠道
        switch ($type) {
            case 1://http://www.fastoo.cn/
                $fastoo = new \sms\fastoo\api();
                $res = $fastoo->sendSms($phoneArea . $phone, $function, $code, $userName);
                break;
            case 2://阿里云
                $aliyun = new \sms\aliyunSms\api();
                $res = $aliyun->sendSms($phone, $function, $code);
                break;
        }
        if ($res['status'] == 1) {
            $log = [
                'type' => 1,
                'to' => $phone,
                'userId' => $userId,
                'function' => $function,
                'code' => $code,
                'content' => $res['data']['content'],
                'returnCode' => $res['data']['code'],
                'returnMsg' => $res['data']['msg'],
                'ip' => $ip,
                'createTime' => date('Y-m-d H:i:s')
            ];
            Db::name('email_sms')->insert($log);
            cache("VerifyCode_" . $function . $phone, $code, 300);
            $time_out = strtotime(date("Y-m-d", strtotime("+1 day"))) - time();
            $redis->set('sendsms:phone:time:' . $phone, date('Y-m-d H:i:s'), $time_out);
            $redis->set('sendsms:ip:time:' . $ip, date('Y-m-d H:i:s'), $time_out);
            $redis->set('sendsms:phone:nums:' . $phone, (int)$phoneSendNum + 1, $time_out);
            $redis->set('sendsms:ip:nums:' . $ip, (int)$ipSendNum + 1, $time_out);
            return SKReturn('短信发送成功', 1);
        } else {
            return SKReturn($res['msg']);
        }
    }

    /**
     * 校验验证码
     * @param verifyData 验证参数（手机号码或者邮箱）
     * @param function 短信主题
     * @param code 短信验证码
     * @return boolean
     */
    public function verifySms($verifyData, $code, $function)
    {
        $cacheCode = (string)cache("VerifyCode_" . $function . $verifyData);
        if ($cacheCode != "" && $cacheCode == $code) {
            cache("VerifyCode_" . $function . $verifyData, null);
            return true;
        }
        return false;
    }
}