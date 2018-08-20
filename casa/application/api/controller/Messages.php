<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/5/31 0031
 * Time: 下午 2:36
 */

namespace app\api\controller;

use app\api\model\Messages as M;

class Messages extends Base
{
    public function _initialize()
    {
        ISLogin();
    }

    /**
     * 用户消息列表
     * @param $userId 用户Id
     */
    public function messageList($param) {
        $m = new M();
        $result = $m->messageList($param);
        return $result;
    }

    /**
     * 我的消息详情
     * @param
     */
    public function messageDetail($param)
    {
        $m = new M();
        $result = $m->messageDetail($param);
        return $result;
    }
}