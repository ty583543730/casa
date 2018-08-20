<?php
/**
 * 发送邮件
 * User: yt
 * Date: 2018/7/9 0009
 * Time: 下午 5:44
 */

namespace email;

use think\Db;

class email
{
    /**
     * 发送邮件
     * @param $email
     * @param $function
     * @param int $userId
     * @param int $code
     * @return array
     * @throws \PHPMailer\PHPMailer\Exception
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function sendEmail($email, $function, $userId = 0, $code = 0)
    {
        if (empty($email) || empty($function)) {
            return SKReturn('缺少不要参数');
        }
        if (empty($code)) $code = rand(111111, 999999);
        $userName = '';
        if ($userId != 0) {
            $userName = Db::name('users')->where(['userId' => $userId])->value('userName');
        }
        $ip = request()->ip(0, true);
        $template = new template();
        $Template = $template->index($function, $code, $userName, $ip);
        if (empty($Template)) {
            return SKReturn('没有该function的信息');
        }
        $sendRs = $this->phpMailer($email, $Template['logTitle'], $Template['html']);
        $log = [
            'type' => 1,
            'to' => $email,
            'userId' => $userId,
            'function' => $function,
            'code' => $code,
            'content' => $Template['html'],
            'returnCode' => $sendRs['status'],
            'returnMsg' => $sendRs['msg'],
            'ip' => $ip,
            'createTime' => date('Y-m-d H:i:s')
        ];
        if ($sendRs['status'] == 1) {
            Db::name('email_sms')->insert($log);
            cache("VerifyCode_" . $function . $email, $code, 300);
            return SKReturn("邮件发送成功", 1);
        } else {
            return SKReturn($sendRs['msg']);
        }
    }

    /**
     * @param $to
     * @param $subject
     * @param $content
     * @return array
     * @throws \PHPMailer\PHPMailer\Exception
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function phpMailer($to, $subject, $content)
    {
        $usableEmail = "";
        $emailConfigs = Db::name("email_configs")->where(['dataFlag' => 1])->select();
        foreach ($emailConfigs as $k => $v) {
            if ($v['useTime'] != 0) {
                $usableEmail = $emailConfigs[$k];
                break;
            }
        }
        if (!is_array($usableEmail)) {
            return SKReturn("邮箱发送失败");
        }
        $mail = new \PHPMailer\PHPMailer\PHPMailer();
        //是否启用smtp的debug进行调试 开发环境建议开启 生产环境注释掉即可 默认关闭debug调试模式
        $mail->SMTPDebug = 0;
        //使用smtp鉴权方式发送邮件
        $mail->isSMTP();
        //smtp需要鉴权 这个必须是true
        $mail->SMTPAuth = $usableEmail['mailAuth'];
        //链接qq域名邮箱的服务器地址
        $mail->Host = $usableEmail['mailSmtp'];
        //设置使用ssl加密方式登录鉴权
        $mail->SMTPSecure = 'ssl';
        //设置ssl连接smtp服务器的远程服务器端口号，以前的默认是25，但是现在新的好像已经不可用了 可选465或587
        $mail->Port = $usableEmail['mailPort'];
        //设置发件人的主机域 可有可无 默认为localhost 内容任意，建议使用你的域名
        $mail->Hostname = BASE_URL;
        //设置发送的邮件的编码 可选GB2312 我喜欢utf-8 据说utf8在某些客户端收信下会乱码
        $mail->CharSet = 'UTF-8';
        //设置发件人姓名（昵称） 任意内容，显示在收件人邮件的发件人邮箱地址前的发件人姓名
        $mail->FromName = $usableEmail['mailSendTitle'];
        //smtp登录的账号 这里填入字符串格式的qq号即可(97800715 或者 97800715@qq.com 格式都可以)
        $mail->Username = $usableEmail['mailUserName'];
        //smtp登录的密码 使用生成的授权码（就刚才叫你保存的最新的授权码）
        $mail->Password = $usableEmail['mailPassword'];
        //设置发件人邮箱地址 这里填入上述提到的“发件人邮箱”
        $mail->From = $usableEmail['mailAddress'];
        //邮件正文是否为html编码 注意此处是一个方法 不再是属性 true或false
        $mail->isHTML(true);
        //设置收件人邮箱地址 该方法有两个参数 第一个参数为收件人邮箱地址 第二参数为给该地址设置的昵称 不同的邮箱系统会自动进行处理变动 这里第二个参数的意义不大
        $mail->addAddress($to, $subject);
        //添加该邮件的主题
        $mail->Subject = $subject;
        //添加邮件正文 上方将isHTML设置成了true，则可以是完整的html字符串 如：使用file_get_contents函数读取本地的html文件
        $mail->Body = $content;
        // 发送邮件
        if ($mail->Send()) {
            Db::name("email_configs")->where(['id' => $usableEmail['id']])->setDec('useTime');
            return SKReturn("成功", 1);
        } else {
            return SKReturn($mail->ErrorInfo);
        }
    }
}