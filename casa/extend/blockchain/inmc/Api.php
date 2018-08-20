<?php
/**
 * inmc的Api入口
 * User: zhouying
 * Date: 2018-6-25
 */

namespace blockchain\inmc;

use think\Log;

class Api
{
    //智能合约地址
    private $token = "0x94fe1f430cbab047ce4bbcf3fa6010ed16c9d6c8";
    //MethodID(0x + 方法名的sha3的前8个字节)
    private $balanceOf = "0x70a08231";
    private $transfer = "0xa9059cbb";
    //参数长度
    private $byteLen = 64;

    public function __construct()
    {
        $app = config("app_status"); //dev-测试用  pro-生产用
        if ($app == "dev") {
            $this->token = "0x94fe1f430cbab047ce4bbcf3fa6010ed16c9d6c8";
        } else if ($app == "pro") {
            $this->token = "0x94fe1f430cbab047ce4bbcf3fa6010ed16c9d6c8";
        }
    }

    /**获取代币余额
     * @param $address
     * @return number
     */
    public function getBalance($address)
    {
        if ($address == '') {
            return 0;
        }
        $len = $this->byteLen - strlen(substr($address, 2));
        $str = str_repeat("0", $len) . substr($address, 2);
        $data = $this->balanceOf .$str;
        $tx = [
            "from" => $address,
            "to" => $this->token,
            "data" => $data,
        ];
        $result = $this->sendPost("eth_call", [$tx, "latest"]);
        if (!$result) {
            return $result;
        }
        $balance = hexdec($result);
        $balance = $balance / pow(10, 18);//最大单位 ETH
        $balance = substr(sprintf("%.9f", $balance), 0, -1);//保留8位，后面的舍去
        return $balance;
    }

    /** 代币转账
     * @param $from  钱包地址
     * @param $to  钱包地址
     * @param $eth  数量
     * @param string $passwd
     * @return bool|mixed|string
     */
    public function transfer($from, $to, $eth, $passwd = "123456")
    {
        $eth = dechex($eth * pow(10, 18));
        $tempAddress = substr($to, 2);
        $eth = str_repeat("0", $this->byteLen - strlen($eth)) . $eth;
        $data = $this->transfer . str_repeat("0", $this->byteLen - strlen($tempAddress)) . $tempAddress . $eth;
        $tx = [
            "from" => $from,
            "to" => $this->token,
            "data" => $data,
        ];
        $result = $this->sendPost("personal_sendTransaction", [$tx, $passwd]);
        if (substr($result, 0, 2) == '0x') {
            return $result;
        } else {
            return false;
        }
    }


    /**导入密钥生成新地址
     * @param $key
     * @param $password
     * @return mixed
     */
    public function importKey($key, $password)
    {
        return $this->sendPost("personal_importRawKey", [$key, $password]);
    }

    /**创建新地址
     * @param $password
     * @param $name
     * @return mixed
     */
    public function newAccount($password)
    {
        return $this->sendPost("personal_newAccount", [$password]);
    }

    /**交易状态查询
     * @param $hash
     * @return bool|mixed|string
     */
    public function txStatus($hash)
    {
        $result = $this->sendPost("eth_getTransactionByHash", [$hash]);
        if (is_null($result['blockNumber'])) {
            return false;
        } else {
            return true;
        }
    }

    /**获取区块信息
     * @param $number 十进制区块高度
     */
    public function getBlockByNumber($number)
    {
        $result = $this->sendPost("eth_getBlockByNumber", ["0x" . dechex($number),true]);
        return $result;
    }


    private function sendPost($method, $params)
    {
        $post_data = [
            "jsonrpc" => "2.0",
            "method" => $method,
            "params" => $params,
            "id" => 63
        ];
        $post_data = json_encode($post_data);
        $options = array(
            'http' => array(
                'method' => 'POST',
                'header' => 'Content-type:application/json',
                'content' => $post_data,
                'timeout' => 15 * 60 // 超时时间（单位:s）
            )
        );
        try {
            $context = stream_context_create($options);
            if (config("app_status") == "dev") {
                $result = file_get_contents("http://13.250.103.238:8545", false, $context); //外网测试链 -测试用
            } elseif (config("app_status") == "pro") {
                $result = file_get_contents("http://172.31.28.186:8545", false, $context); // 内网公链 -生产用（钱包节点）
            } else {
                return false;
            }
            $result = json_decode($result, true);

            if (isset($result["result"])) {
                return $result["result"];
            } elseif (isset($result["error"])) {
                return $result["error"]["message"];
            } else {
                sendSms();
                return $result;
            }
        } catch (\Exception $exc) {
            sendSms();
            Log::logger("", "postData:" . json_encode($post_data) . "\nException:" . $exc->getMessage(), "eth_rpc_err");
            return false;
        }
    }
}