<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/5/31 0031
 * Time: 上午 9:22
 */

namespace app\api\controller;

use think\Log;
use think\Db;
use app\api\model\Users as U;

class Users extends Base
{
    public function _initialize()
    {
        ISLogin();
    }

    /**
     * 用户的基本信息
     * @author：zjf 2018/5/31
     */
    public function userInfo($param)
    {
        $m = new U();
        $result = $m->userInfo($param);
        return $result;
    }

    /**
     * 实名认证（人工认证）
     * @author：zjf 2018/5/31
     */
    public function realPersonAuth($param)
    {
        try {
            $result = optBing("realPortAuth", userId(), 0);
            if (empty($result)) {
                return SKReturn("业务正在处理中，请稍后");
            }
            $m = new U();
            $result = $m->realPersonAuth($param);
        } catch (\Exception $e) {
            Log::logger("实名认证（人工认证）", "[Line:]" . $e->getLine() . "[Msg:]" . $e->getMessage(), "realPersonAuth");
            if (config("app_debug")) {
                $msg = "[Line:]" . $e->getLine() . "[Msg:]" . $e->getMessage();
            } else {
                $msg = "操作异常";
            }
            $result = SKReturn($msg);
        }
        optBing("realPortAuth", userId(), 1);
        return $result;
    }

    /**
     * 用户实名信息
     * @author：zjf 2018/5/31
     */
    public function realInfo($param)
    {
        $m = new U();
        $result = $m->realInfo($param);
        return $result;
    }

    /**
     * 邀请好友列表
     * @author：zjf 2018/5/31
     */
    public function inviteList($param)
    {
        $m = new U();
        $result = $m->inviteList($param);
        return $result;
    }

    /**
     * 邀请奖励明细
     * @author：zjf 2018/5/31
     */
    public function rewardList($param)
    {
        $m = new U();
        $result = $m->rewardList($param);
        return $result;
    }

    /**
     * 用户头像修改
     * @author：zjf 2018/5/31
     */
    public function editPhoto($param)
    {

        try {
            $result = optBing("editPhoto", userId(), 0);
            if (empty($result)) {
                return SKReturn("业务正在处理中，请稍后");
            }
            $m = new U();
            $result = $m->editPhoto($param);
        } catch (\Exception $e) {
            Log::logger("修改用户头像", "[Line:]" . $e->getLine() . "[Msg:]" . $e->getMessage(), "editPhoto");
            if (config("app_debug")) {
                $msg = "[Line:]" . $e->getLine() . "[Msg:]" . $e->getMessage();
            } else {
                $msg = "操作异常";
            }
            $result = SKReturn($msg);
        }
        optBing("editPhoto", userId(), 1);
        return $result;
    }

    /**
     * 用户昵称修改
     * @author：zjf 2018/5/31
     */
    public function editName($param)
    {
        try {
            $result = optBing("editName", userId(), 0);
            if (empty($result)) {
                return SKReturn("业务正在处理中，请稍后");
            }
            $m = new U();
            $result = $m->editName($param);
        } catch (\Exception $e) {
            Log::logger("修改用户昵称", "[Line:]" . $e->getLine() . "[Msg:]" . $e->getMessage(), "editName");
            if (config("app_debug")) {
                $msg = "[Line:]" . $e->getLine() . "[Msg:]" . $e->getMessage();
            } else {
                $msg = "操作异常";
            }
            $result = SKReturn($msg);
        }
        optBing("editName", userId(), 1);
        return $result;
    }

    /**
     * 指纹，面部识别登录,支付开启关闭
     * @author：zjf 2018/6/4
     */
    public function switchs($param)
    {
        try {
            $param['userId'] = userId();
            $result = optBing("switchs", $param['userId'], 0);
            if (empty($result)) {
                return SKReturn("业务正在处理中，请稍后");
            }
            $m = new U();
            $result = $m->switchs($param);
        } catch (\Exception $e) {
            Log::logger("指纹，面部识别", "[Line:]" . $e->getLine() . "[Msg:]" . $e->getMessage(), "switchs");
            if (config("app_debug")) {
                $msg = "[Line:]" . $e->getLine() . "[Msg:]" . $e->getMessage();
            } else {
                $msg = "操作异常";
            }
            $result = SKReturn($msg);
        }
        optBing("switchs", $param['userId'], 1);
        return $result;
    }

}