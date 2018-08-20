<?php
/**
 * 基础方法
 * User: yt
 * Date: 2018-4-8 9:24
 */

namespace app\api\controller;

use appVerify\Image;
use app\api\model\Site as S;

class Site extends Base
{
    /**
     * 图形验证码.md
     */
    public function code()
    {
        Image::genCode("app_logic");
    }

    /**
     * 验证码校验
     * @param $code
     * @return array
     */
    public function checkCode($code)
    {
        if (!Image::checkCode("app_logic", $code, false)) {
            return SKReturn("图片验证码错误!");
        }
        return SKReturn("验证成功", 1);
    }

    /**
     *  验证图形验证码并发送短信
     * @author zjf 2018/1/11
     * @param  $userPhone  用户手机号
     * @param  $userEmail  用户邮箱
     * @param  $imageCode 图形验证码
     * @param  $sendCode  短信主题代码
     * @param  $isImage 是否要验证图形验证码（1：要；2：不要）
     * @param  $resource 请求来源（0:PC；1:APP）
     */
    public function sendSmsWithCode($data)
    {
        $optbing = optBing('sendSmsWithCode', $data['userPhone'] . ':' . $data['function'], 0);

        if (empty($optbing)) {
            return SKReturn("业务正在处理中，请勿重复提交");
        }
        $m = new S();
        $result = $m->sendSmsByCode($data);
        optBing('sendSmsWithCode', $data['userPhone'] . ':' . $data['function']);
        return $result;
    }

    /**
     *  短信验证码校验
     * @param verifyData 验证参数（手机号码或者邮箱）
     * @param function 短信主题
     * @param code 短信验证码
     */
    public function checkSmsCode($param)
    {
        if (empty($param['verifyData']) || empty($param['function']) || empty($param['code'])) {
            return SKReturn("必填参数都不能为空");
        }
        $optbing = optBing('sendSmsWithCode', $param['function'] . ':' . $param['verifyData'], 0);
        if (empty($optbing)) {
            return SKReturn("业务正在处理中，请勿重复提交");
        }
        $m = new \sms\Sms();
        $result = $m->verifySms($param['verifyData'], $param['code'], $param['function']);
        optBing('sendSmsWithCode', $param['function'] . ':' . $param['verifyData']);
        if($result){
             return SKReturn('短信校验成功',1);
        }
        return  SKReturn('短信校验失败');
    }

    /**
     *  获得区号列表
     * @param  param
     *  add by zjf 2018/4/12
     */
    public function country()
    {
        $m = new S();
        $result = $m->country();
        return $result;
    }

    /*上传图片*/
    public function uploadPic()
    {
        $dir = input('post.dir');
        return uploadPic($dir,'app');
    }
}