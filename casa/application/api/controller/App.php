<?php
/**
 * Created by PhpStorm.
 * User: Bourne
 * Date: 2017-11-8 10:37
 */

namespace app\api\controller;

use think\Log;

class App extends Base
{
    /*获取系统参数*/
    public function getSystemInfo()
    {
        $data = sysConfig();
        $list = [
            "baseUrl" => BASE_URL,
            'androidVersion' => $data['androidVersion'],
            "url" => BASE_URL . 'app/app-release.apk',
            "appVersionMsg" => $data["appVersionMsg"],
            "update" => "1", //1强制更新 0无所谓
            "iosVersion" => $data["iosVersion"],//iso版本号
            "iosStatus" => $data["iosStatus"],//审核中，或已审核
            'Fee' => $data['Fee'],
            'serviceTel' => $data['serviceTel'],
            "websocketIP" => config('websocket_ip'),
            "websocketPort" => config('websocket_port'),
            "websocketSignkey" => config('rpc_sign_key')
        ];
        return SKReturn("获取成功", 1, $list);
    }

    /**
     * ios 客户端APP数据接口
     * @return type
     */
    public function ios()
    {
        return $this->app();
    }

    public function android()
    {
        return $this->app();
    }

    private function app()
    {
        //验签是否开启
        $kaiqi = false;
        if ($kaiqi) {
            $auth_token = "IX9vaE1Z902Bs2k0";
            $api_auth = $_SERVER["HTTP_AUTH"];
            $auth_arr = explode("|", $api_auth);
            $algo = $auth_arr[0];
            $exp_time = $auth_arr[1];
            $nonce = $auth_arr[2];
            $sign = $auth_arr[3];
            if ($exp_time - $_SERVER["REQUEST_TIME"] < 60) {
                exit("3.");
            }
            if ($algo == "md5") {
                $server_sign = md5($exp_time . $auth_token . $nonce);
            } elseif ($algo == "sha1") {
                $server_sign = sha1($exp_time . $auth_token . $nonce);
            } else {
                exit("2.");
            }
            if ($server_sign != $sign) {
                exit("1.");
            }
        }
        debug('begin');

        try {
            $request_body = file_get_contents('php://input');
            $reqData = json_decode($request_body, true);
            //业务编号
            $uriCode = str_replace(".", "/", $reqData['code']);
            //日志文件
            $logarr = [
                "timeFloat" => $_SERVER['REQUEST_TIME_FLOAT'],
                "timeInt" => $_SERVER['REQUEST_TIME'],
                "timeDate" => date("Y-m-d H:i:s"),
                "token" => $_SERVER['HTTP_TOKEN'],
                "device" => $_SERVER['HTTP_DEVICE'],
                "deviceId" => $_SERVER['HTTP_DEVICEID'],
                "ip" => $_SERVER['REMOTE_ADDR'],
                "effect" => 0,
                "error" => 0,
                "request" => $reqData,
                "bizCode" => $uriCode
            ];
            if ($uriCode != "") {
                $uriCodeArr = explode('-', $uriCode);
                $uriData = $reqData['data'] != NULL ? $reqData['data'] : array(); //业务参数
                if (count($uriCodeArr) > 1) {
                    $model = controller(strtolower($uriCodeArr[0]));
                    $method = $uriCodeArr[1];
                    if (method_exists($model, $method)) {
                        $result = $model->$method($uriData);
                        if (is_array($result) && isset($result['status']) && ($result['status'] === -1 && isset($result['msg']))) {
                            return SKReturn($result['msg']);
                        }
                        debug('end1');
                        //不需要登录信息的控制器方法
                        $noLogin = array('sendSmsWithCode', 'checkSmsCode', 'country',
                            'loginOne', 'loginTwo', 'otherLogin', 'phoneRegisterOne',
                            'phoneRegisterTwo', 'retrievePwdOne', 'retrievePwdTwo');
                        if (!in_array($method, $noLogin)) {
                            $logarr['userId'] = userId();
                        }
                        $logarr["respone"] = $result;
                        $logarr["lasting"] = debug('begin', 'end1', 6) . "s";
                        $logarr["memory"] = debug('begin', 'end1', 'm');
                        if (debug('begin', 'end1', 6) > 0.5) {
                            $logarr["effect"] = 1;
                        }
                        //$result["time"] = microtime(true) * 1000;
                        Log::logger($uriCode, json_encode($logarr, JSON_UNESCAPED_UNICODE), 'log_app');
                        return $result;
                    }
                }
            }
            $logarr["error"] = 1;
            $logarr["respone"] = "请求无效";
            Log::logger($uriCode, json_encode($logarr, JSON_UNESCAPED_UNICODE), 'log_app_error');
            return SKReturn("请求无效");
        } catch (\Exception $exc) {
             return $exc->getMessage();
            $logarr["error"] = 1;
            $logarr["respone"] = "[File:]" . $exc->getFile() . "[Line:]" . $exc->getLine() . "[Msg:]" . $exc->getMessage();
            Log::logger('Exception', json_encode($logarr, JSON_UNESCAPED_UNICODE), 'log_app_exception');
            if ($exc->getMessage() == "login-10") {
                return ['status' => -10, 'msg' => '该账号已在其他设备登录'];
            } elseif ($exc->getMessage() == "login-2") {
                return ['status' => -2, 'msg' => '对不起，您还没有登录，请先登录'];
            }
            if (config("app_debug")) {
                return SKReturn("File:" . $exc->getFile() . "     Line:" . $exc->getLine() . "     " . $exc->getMessage());
            } else {
                return SKReturn("调用失败");
            }
        }
    }
}