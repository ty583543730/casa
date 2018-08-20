<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/5/31 0031
 * Time: 下午 5:16
 */

namespace app\api\controller;

use think\Log;
use app\api\model\Transfer as T;

class Transfer extends Base
{
    public function _initialize()
    {
        ISLogin();
    }

    /**
     * 填写转账金额页面所需数据
     * @author：zjf 2018/6/7
     */
    public function userData($param)
    {
        $m = new T();
        $result = $m->userData($param);
        return $result;
    }

    /**
     * 用户转账数量验证
     * @author：zjf 2018/5/31
     */
    public function numVerify($param)
    {
        $m = new T();
        $result = $m->numVerify($param);
        return $result;
    }

    /**
     * 用户转账提交
     * @author：zjf 2018/5/31
     */
    public function transferSubmit($param)
    {
        try {
            $userId = userId();
            $result = optBing("transferSubmit", $userId, 0);

            if (empty($result)) {
                return SKReturn("业务正在处理中，请稍后");
            }
            $m = new T();
            $result = $m->transferSubmit($param);
        } catch (\Exception $e) {
            Log::logger("转账收款", "[Line:]" . $e->getLine() . "[Msg:]" . $e->getMessage(), "transferSubmit");
            $msg = "[Line:]" . $e->getLine() . "[Msg:]" . $e->getMessage();
            $result = SKReturn($msg);
        }
        optBing("transferSubmit", $userId, 1);
        return $result;
    }

}