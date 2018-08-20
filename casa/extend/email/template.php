<?php
/**
 * 邮箱模板
 * User: yt
 * Date: 2018/7/9 0009
 * Time: 下午 5:46
 */

namespace email;

use think\Db;

class template
{
    public function index($function, $code, $userName, $ip)
    {
        $website = BASE_URL; //官方网站
        $seriveTel = SysConfig("serviceTel");  //客服电话
        $emailService = SysConfig("serviceEmail");    //邮箱服务
        $time = date('Y-m-d H:i:s');
        $user = Db::name("users")->where(['userName' => $userName])->field("userId,phoneArea,userName,userEmail,gugeCode,userPhone,isReceiveEmail,isReceiveSMS")->find();
        switch ($function) {
            case "bindingEmail":
                $logTitle = "邮箱绑定";
                $html = $userName . ',您好！' .
                    "<br>您的账户于" . $time . "正在绑定本邮箱，请在绑定页面输入验证码：" . "<a href='" . $website . "'>" . $code . "</a>" .
                    "<br>当前登录 IP:" . $ip .
                    "<br>当前登录时间: " . $time . "(GMT+08:00) Beijing" .
                    "<br>如果此活动不是您本人所为，请尽快联系客服，" . $seriveTel . '。' .
                    "<br>请注意绝不会以任何形式询问您的帐户密码和验证码。" .
                    "<br>本邮件由系统自动生成，无需授权签名。" .
                    "<br>-----------------------------------------------" .
                    "<br>官方网站：" . $website .
                    "<br>邮箱服务：" . $emailService;
                break;
            case "unBindEmail":
                $logTitle = "邮箱解绑";
                $html = $userName . ',您好！' .
                    "<br>您的账户于" . $time . "正在进行邮箱解绑。请在解绑页面输入下列验证码：" . "<a href='" . $website . "'>" . $code . "</a>" .
                    "<br>bitapex交易所不承担您因为解绑邮箱而造成的任何损失。" .
                    "<br>当前登录 IP:" . $ip .
                    "<br>当前登录时间: " . $time . "(GMT+08:00) Beijing" .
                    "<br>如果此活动不是您本人所为，请尽快联系客服，" . $seriveTel . '。' .
                    "<br>请注意绝不会以任何形式询问您的帐户密码和验证码。" .
                    "<br>本邮件由系统自动生成，无需授权签名。" .
                    "<br>-----------------------------------------------" .
                    "<br>官方网站：" . $website .
                    "<br>邮箱服务：" . $emailService;
                break;
            case "findLoginPwd":
                $logTitle = "修改登录密码";
                $html = $userName . ',您好！' .
                    "<br>您的账户于" . $time . "修改登录密码成功。" .
                    "<br>当前登录 IP:" . $ip .
                    "<br>当前登录时间: " . $time . "(GMT+08:00) Beijing" .
                    "<br>如果此活动不是您本人所为，请尽快联系客服，" . $seriveTel . '。' .
                    "<br>请注意绝不会以任何形式询问您的帐户密码和验证码。" .
                    "<br>本邮件由系统自动生成，无需授权签名。" .
                    "<br>-----------------------------------------------" .
                    "<br>官方网站：" . $website .
                    "<br>邮箱服务：" . $emailService;
                break;
            case "findPayPwd":
                $logTitle = "修改交易密码";
                $html = $userName . ',您好！' .
                    "<br>您的账户于" . $time . "修改交易密码成功。" .
                    "<br>当前登录 IP:" . $ip .
                    "<br>当前登录时间: " . $time . "(GMT+08:00) Beijing" .
                    "<br>如果此活动不是您本人所为，请尽快联系客服，" . $seriveTel . '。' .
                    "<br>请注意绝不会以任何形式询问您的帐户密码和验证码。" .
                    "<br>本邮件由系统自动生成，无需授权签名。" .
                    "<br>-----------------------------------------------" .
                    "<br>官方网站：" . $website .
                    "<br>邮箱服务：" . $emailService;
                break;
            case "ipChange":
                $logTitle = "登录IP发生变化";
                $loginLog = Db::name('log_users_logins')->where(['userId' => $user['userId']])->limit(1, 1)->order('id desc')->find();
                $lastIp = isset($loginLog['loginIp']) ? $loginLog['loginIp'] : "";
                $lastTime = isset($loginLog['loginTime']) ? $loginLog['loginTime'] : "";
                $pwdUrl = BASE_URL . 'home/password/findloginpwd';
                $html = $userName . ',您好！' .
                    "<br>您的账户于" . $time . "登录，我们发现您本次登录IP与上次不一致，请确定这是您本人操作" .
                    "<br>当前登录 IP:" . $ip .
                    "<br>当前登录时间: " . $time . "(GMT+08:00) Beijing" .
                    "<br>上次登录 IP:" . $lastIp .
                    "<br>上次登录时间: " . $lastTime . "(GMT+08:00) Beijing" .
                    "<br>如果此活动不是您本人所为，请立即<a href=" . $pwdUrl . ">重设密码</a>,并尽快联系客服，客服电话" . $seriveTel . '。' .
                    "<br>请注意客服人员绝不会以任何形式询问您的帐户密码和验证码。" .
                    "<br>本邮件由系统自动生成，无需授权签名。" .
                    "<br>-----------------------------------------------" .
                    "<br>官方网站：" . $website .
                    "<br>邮箱服务：" . $emailService;
                break;
            case "bindingMobile":
                $logTitle = "手机绑定";
                $html = $userName . ',您好！' .
                    "<br>您的账户于" . $time . "手机号绑定更换为(" . $user['phoneArea'] . ")" . phoneHide($user['userPhone']) .
                    "<br>当前登录 IP:" . $ip .
                    "<br>当前登录时间: " . $time . "(GMT+08:00) Beijing" .
                    "<br>如果此活动不是您本人所为，请尽快联系客服，" . $seriveTel . '。' .
                    "<br>请注意客服 绝不会以任何形式询问您的帐户密码和验证码。" .
                    "<br>本邮件由系统自动生成，无需授权签名。" .
                    "<br>-----------------------------------------------" .
                    "<br>官方网站：" . $website .
                    "<br>邮箱服务：" . $emailService;
                break;
            case "unBindMobile":
                $logTitle = "手机解绑";
                $html = $userName . ',您好！' .
                    "<br>您的账户于" . $time . "解绑手机号(" . $user['phoneArea'] . ")" . phoneHide($user['userPhone']) . "成功。为了提高您的账户安全性，请您立即登录bitapex交易所 重新绑定手机。bitapex交易所不承担您因为解绑手机而造成的任何损失。" .
                    "<br>当前登录 IP:" . $ip .
                    "<br>当前登录时间: " . $time . "(GMT+08:00) Beijing" .
                    "<br>如果此活动不是您本人所为，请尽快联系客服，" . $seriveTel . '。' .
                    "<br>请注意客服 绝不会以任何形式询问您的帐户密码和验证码。" .
                    "<br>本邮件由系统自动生成，无需授权签名。" .
                    "<br>-----------------------------------------------" .
                    "<br>官方网站：" . $website .
                    "<br>邮箱服务：" . $emailService;
                break;
            case "bingGoogle":
                $logTitle = "谷歌验证绑定";
                $html = $userName . ',您好！' .
                    "<br>您的账户于" . $time . "绑定谷歌两步验证Google Authenticator。谷歌两步验证将在您登陆、交易、提现时进行安全验证。" .
                    "<br>当前登录 IP:" . $ip .
                    "<br>当前登录时间: " . $time . "(GMT+08:00) Beijing" .
                    "<br>如果此活动不是您本人所为，请尽快联系客服，" . $seriveTel . '。' .
                    "<br>请注意客服 绝不会以任何形式询问您的帐户密码和验证码。" .
                    "<br>本邮件由系统自动生成，无需授权签名。" .
                    "<br>-----------------------------------------------" .
                    "<br>官方网站：" . $website .
                    "<br>邮箱服务：" . $emailService;
                break;
            case "unBindGoogle":
                $logTitle = "谷歌验证解绑";
                $html = $userName . ',您好！' .
                    "<br>您的账户于" . $time . "成功解绑了谷歌两步验证Google Authenticator。为了提高您的账户安全性，请您立即登录bitapex交易所 重新绑定两步验证。bitapex交易所不承担您因为取消两步验证而造成的任何损失。" .
                    "<br>当前登录 IP:" . $ip .
                    "<br>当前登录时间: " . $time . "(GMT+08:00) Beijing" .
                    "<br>如果此活动不是您本人所为，请尽快联系客服，" . $seriveTel . '。' .
                    "<br>请注意客服 绝不会以任何形式询问您的帐户密码和验证码。" .
                    "<br>本邮件由系统自动生成，无需授权签名。" .
                    "<br>-----------------------------------------------" .
                    "<br>官方网站：" . $website .
                    "<br>邮箱服务：" . $emailService;
                break;
            default:
                return false;
        }
        return ['logTitle' => $logTitle, 'html' => $html];

    }
}