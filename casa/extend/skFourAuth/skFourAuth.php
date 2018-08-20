<?php
/**
 * 银行卡四要素验证
 * ================================================================
 * @realName    姓名
 * @idCard      身份证
 * @bankCard    银行卡号
 * @mobile      手机号码
 * ================================================================
 */

namespace skFourAuth;

use think\Controller;
use think\Log;

require('class/Utils.class.php');
require('class/RequestHandler.class.php');
require('class/ClientResponseHandler.class.php');
require('class/PayHttpClient.class.php');
require('config/config.php');

Class skFourAuth extends Controller
{
    private $resHandler = null;
    private $reqHandler = null;
    private $pay = null;
    private $cfg = null;
    public $error = null;
    public $bankUrl = "bankCardAuth";   //银行卡验证后缀
    public $cardUrl = "idcardAuth";   //身份证验证后缀

    public function __construct()
    {
        $this->resHandler = new \ClientResponseHandler();
        $this->reqHandler = new \RequestHandler();
        $this->pay = new \PayHttpClient();
        $this->cfg = new \Config();
        $this->Request();
    }

    public function Request()
    {
        $this->reqHandler->setGateURL($this->cfg->C('url'));    //设置地址
        $key = $this->cfg->C('key');  //多个商户密钥时使用
        $this->reqHandler->setKey($key);                        //设置密钥
    }

    /**
     * 身份证信息验证接口
     * @realName    姓名
     * @idCard      身份证
     * @resource    日志名
     */
    public function verifyTwoAuth($realName = "", $idCard = "", $resource = "")
    {
        if (empty($realName)) {
            return SKReturn("姓名不能为空");
        }
        if (!validation_filter_id_card($idCard)) {
            return SKReturn("身份证号码有误");
        }
        if (empty($resource)) {
            return SKReturn("请求来源不能为空");
        }
        $time = time();
        $reqData = array(
            "appId" => $this->cfg->C('appId'),
            "idCard" => $idCard,
            "realName" => $realName,
            "time" => $time,
        );
        $this->reqHandler->setReqParams($reqData, array('method'));
        $this->reqHandler->twoCreateSign();//创建签名
        $data = $this->reqHandler->getAllParameters();
        Log::logger($resource . "请求数据", JSONReturn($data), "skFourAuth");
        $submitUrl = $this->cfg->C('url') . $this->cardUrl;
        $result = $this->pay->call($resource,JSONReturn($data),$submitUrl);
        if ($result) {
            $result = json_decode($this->pay->getResContent(),true);
            $respCode = isset($result['resCode']) ? $result['resCode'] : "";
            $returnInfo = isset($result['resDesc']) ? $result['resDesc'] : "";
            if ($respCode == "00") {
                return SKReturn("认证通过", 1);
            } else {
                return SKReturn($returnInfo);
            }
        } else {
            return SKReturn("验证失败");
        }
    }

    /**
     * 三要素验证方法
     * add by zyx 2017/11/23
     * @realName    姓名
     * @idCard      身份证
     * @bankCard    银行卡号
     * @mobile      手机号码
     */
    public function verifyThreeAuth($realName = "", $idCard = "", $bankCard = "", $resource = "")
    {
        if (empty($realName)) {
            return SKReturn("姓名不能为空");
        }
        if (!validation_filter_id_card($idCard)) {
            return SKReturn("身份证号码有误");
        }
        if (empty($bankCard)) {
            return SKReturn("银行卡号不能为空");
        }
        $time = time();
        $qryBatchNo = 'c' . SKOrderNo();
        $reqData = array(
            "appId" => $this->cfg->C('appId'),
            "bankCard" => $bankCard,
            "realName" => $realName,
            "time" => $time,
            "idCard" => $idCard,
        );
        $this->reqHandler->setReqParams($reqData, array('method'));
        $this->reqHandler->threeCreateSign();//创建签名
        $data = $this->reqHandler->getAllParameters();
        Log::logger($resource . "请求数据", JSONReturn($data), "skFourAuth");
        $submitUrl = $this->cfg->C('url') . $this->bankUrl;
        $this->pay->setReqContent($submitUrl, JSONReturn($data));
        $result = $this->pay->call($resource);
        if ($result) {
            $result = json_decode($this->pay->getResContent(),true);
            $respCode = isset($result['resCode']) ? $result['resCode'] : "";
            $returnInfo = isset($result['resDesc']) ? $result['resDesc'] : "";
            if ($respCode == "00") {
                return SKReturn("认证通过", 1);
            } else {
                return SKReturn($returnInfo);
            }
            $this->error = $returnInfo;
            return SKReturn($returnInfo);
//            return 'Error Code:' . $respCode . ' Error Message:' . $returnInfo;
        } else {
            $this->error = "验证失败";
            return SKReturn("验证失败");
//            return 'Response Code:' . $this->pay->getResponseCode() . ' Error Info:' . $this->pay->getErrInfo();
        }
    }

    /**
     * 四要素验证方法
     * add by zyx 2017/11/23
     * @realName    姓名
     * @idCard      身份证
     * @bankCard    银行卡号
     * @mobile      手机号码
     */
    public function verifyFourAuth($realName = "", $idCard = "", $bankCard = "", $mobile = "", $resource = "")
    {
        if (empty($realName)) {
            return SKReturn("姓名不能为空");
        }
        if (!validation_filter_id_card($idCard)) {
            return SKReturn("身份证号码有误");
        }
        if (empty($bankCard)) {
            return SKReturn("银行卡号不能为空");
        }
        if (!SKIsPhone($mobile)) {
            return SKReturn("手机号码格式不对！");
        }
        $time = time();
        $qryBatchNo = 'c' . SKOrderNo();
        $reqData = array(
            "appId" => $this->cfg->C('appId'),
            "bankCard" => $bankCard,
            "realName" => $realName,
            "time" => $time,
            "idCard" => $idCard,
            "mobile" => $mobile,
        );
        $this->reqHandler->setReqParams($reqData, array('method'));
        $this->reqHandler->createSign();//创建签名
        $data = $this->reqHandler->getAllParameters();
        Log::logger($resource . "请求数据", JSONReturn($data), "skFourAuth");
        $submitUrl = $this->cfg->C('url') . $this->bankUrl;
        $this->pay->setReqContent($submitUrl, JSONReturn($data));
        $result = $this->pay->call($resource);
        if ($result) {
            $result = json_decode($this->pay->getResContent(),true);
            $respCode = isset($result['resCode']) ? $result['resCode'] : "";
            $returnInfo = isset($result['resDesc']) ? $result['resDesc'] : "";
            if ($respCode == "00") {
                return SKReturn("认证通过", 1);
            } else {
                return SKReturn($returnInfo);
            }
            $this->error = $returnInfo;
            return SKReturn($returnInfo);
//            return 'Error Code:' . $respCode . ' Error Message:' . $returnInfo;
        } else {
            $this->error = "验证失败";
            return SKReturn("验证失败");
//            return 'Response Code:' . $this->pay->getResponseCode() . ' Error Info:' . $this->pay->getErrInfo();
        }
    }

    /**
     * 获取错误信息
     * @return type
     */
    public function getError()
    {
        return $this->error;
    }
}

?>