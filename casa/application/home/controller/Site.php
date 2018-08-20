<?php
/**
 * Created by PhpStorm.
 * User: jiangjiang
 * Date: 2018/7/2
 * Time: 16:53
 */

namespace app\home\controller;

use app\common\Image;
use app\common\model\Site as S;

class Site extends Base
{
    /**
     *  验证图形验证码并发送短信
     * @author zjf 2018/1/11
     * @param  $userPhone  用户手机号
     * @param  $imageCode 图形验证码
     * @param  $sendCode   短信主题代码
     * @param  $isImage 是否要验证图形验证码（1：要；2：不要）
     */
    public function sendSmsWithCode()
    {
        if (request()->isAjax()) {
            $param = trimArray(input("post."));
            $m = new S();
            $param['isImage'] = 1;
            $param["resource"]= 0;
            $param["userId"] = session("SK_USER.userId") ? session("SK_USER.userId") : 0;
            $result = $m->sendSmsByCode($param);
            return $result;
        }
    }
}