<?php
/**
 * 重构的短信接口 例子参照test
 * User: yt
 * Date: 2018/6/19 0019
 * Time: 下午 5:08
 */

namespace sms\fastoo;

use sms\fastoo\model\SendSingleSmsParm;
use sms\fastoo\model\SendBatchSmsParm;
use sms\fastoo\model\ReturnModel;
use sms\fastoo\api\SendSmsApi;
use sms\fastoo\api\URLConfig;
use sms\fastoo\client\HttpPostClient;

class api
{
    //短信apikey 必填
    private $apiKey = "f9390a4bc5844dc18798deefe64e4305";

    /**
     * 单条发送短信
     */
    public static function testquickSendSingleSms()
    {
        $SendSmsApi = new SendSmsApi();
        $parm = new SendSingleSmsParm("a47dd09338834deda2e0e717a90e2cce", "China", "15016712500", "domestic", "123213");
        $bean = $SendSmsApi->QuickSendSingleSms($parm);
        return ['status' => $bean->code, 'msg' => $bean->msg];
    }

    /**
     * @param 参数
     * @param apiKey  用户唯一标识
     * @param da      发送号码（支持多个，用逗号分割）
     * @param msg     发送内容（url编码）
     * @return
     */
    public function sendSms($phone, $function, $code, $userName)
    {
        $tempalte = new template();
        $msg = $tempalte->sprintf($function, $code, $userName);

        $apiKey = $this->apiKey;
        $parmstr = array(
            "apikey" => $apiKey,
            "da" => $phone,
            "msg" => $msg
        );
        $url = new URLConfig();
        $client = new HttpPostClient();
        $bean = new ReturnModel($client->sendPost($url->SubmitApiURL, $parmstr));
        //$bean->code =0 发送成功
        if ($bean->code == 0) {
            return SKReturn($bean->msg, 1, ['code' => $bean->code, 'msg' => $bean->jsonstr, 'content' => $msg]);
        } else {
            return SKReturn($bean->msg, -1, ['code' => $bean->code, 'msg' => $bean->jsonstr, 'content' => $msg]);
        }
    }
}