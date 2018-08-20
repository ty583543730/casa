<?php
/**
 * eth的Api入口
 * User: Bourne
 * Date: 2018-3-5 10:23
 */

namespace blockchain\eth;

use think\Log;

class Api
{
    /**获取余额
     * @param $address
     * @return number
     */
    public function getBalance($address)
    {
        if ($address == '') {
            return 0;
        }
        $result = $this->sendPost("eth_getBalance", [$address, "latest"]);
        if (!$result) {
            return $result;
        }
        $balance = hexdec($result); //最小单位 wei
        $balance = $balance / pow(10, 18);//最大单位 ETH
        $balance = substr(sprintf("%.9f", $balance), 0, -1);//保留8位，后面的舍去
        return $balance;
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

    /** 转账
     * @param $from  钱包地址
     * @param $to  钱包地址
     * @param $eth  数量
     * @param string $passwd
     * @return bool|mixed|string
     */
    public function transfer($from, $to, $eth, $passwd = "123456")
    {
        $wei = "0x" . dechex($eth);
        $tx = [
            "from" => $from,
            "to" => $to,
            "value" => $wei,
        ];
        $result = $this->sendPost("personal_sendTransaction", [$tx, $passwd]);
        if (substr($result, 0, 2) == '0x') {
            return $result;
        } else {
            return false;
        }
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
        $result = $this->sendPost("eth_getBlockByNumber", ["0x" . dechex($number), true]);
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
            dump($result);

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