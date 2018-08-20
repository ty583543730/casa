<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/5/31 0031
 * Time: 上午 10:01
 */

namespace app\home\controller;

use app\common\model\Login as L;
use app\home\model\Index as I;
use think\Controller;
use think\Lang;
use think\Db;
class Login extends Controller
{
    /*
     * H5注册
     * @author：zjf 2018/6/25
     */
    public function register()
    {
        $data = input("");
        $userPhone = array();
        $userPhone['phone'] = '';
        if (!empty($data['userNo'])) {
            $phone = Db::name('users')->where('userNo',$data['userNo'])->value('userPhone');
            $userPhone = ['phone'=>$phone,'phoneHide'=>phoneHide($phone)];
        }
        $lang = Lang::detect();
        if ($lang == 'en-us'){
            $country = Db::name("country")->where(['status' => 1, "dataFlag" => 1])->field("number,enName as cnName")->select();
        }else{
            $country = Db::name("country")->where(['status' => 1, "dataFlag" => 1])->field("number,cnName")->select();
        }
        $this->assign("country", $country);
        $this->assign("userPhone", $userPhone);
        return $this->fetch('login/register');
    }

    /*
     * H5注册提交
     * @author：zjf 2018/6/25
     */
    public function registerSumbit()
    {
        $param = input("post.");
        try {
            $param = input("post.");
            $result = optBing("registerSumbit", $param['userPhone'], 0);
            if (empty($result)) {
                return SKReturn("业务正在处理中，请稍后");
            }
            $object = new L();
            $result = $object->pcRegister($param);
        } catch (\Exception $e) {
            Log::logger("pc注册提交", "[Line:]" . $e->getLine() . "[Msg:]" . $e->getMessage(), "registerSumbit");
            if (config("app_debug")) {
                $msg = "[Line:]" . $e->getLine() . "[Msg:]" . $e->getMessage();
            } else {
                $msg = "操作异常";
            }
            $result = SKReturn($msg);
        }
        optBing("registerSumbit", $param['userPhone'], 1);
        return $result;
    }

    /*
    * 服务协议
    * @author：zjf 2018/6/29
    */
    public function protocol () {
        $lang = Lang::detect();
        $i = new I();
        $data = $i->getInfo($lang);
        $this->assign('data',$data);
        return $this->fetch('login/protocol');
    }

    /*
    * 下载页面
    * @author：zjf 2018/6/29
    */
    public function download () {
        if ((strpos($_SERVER['HTTP_USER_AGENT'], 'iPhone') || strpos($_SERVER['HTTP_USER_AGENT'], 'iPad'))) {
            $download = "";
        } else {
            $download = BASE_URL . "app/app-release.apk";
        }
        $this->assign("download", $download);
        return $this->fetch('login/download');
    }
}