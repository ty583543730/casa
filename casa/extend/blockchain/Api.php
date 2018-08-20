<?php
/**
 * 区块链Api总入口
 * User: Bourne
 * Date: 2018-3-14 10:17
 */

namespace blockchain;


class Api
{
    public $News = '';
    public $Coin = '';

    public function __construct($coin)
    {
        $this->Coin = $coin;
        switch ($coin) {
            case "ETH":
                $this->News = new \blockchain\eth\Api();
                break;
            case "USDT":
                $this->News = new \blockchain\usdt\Api();
                break;
            case "CASA":
                $this->News = new \blockchain\casa\Api();
                break;
            default: $this->News = false;
        }
    }

    /**创建新地址
     * @param $password
     * @param $name
     * @return mixed
     */
    public function getNewAddress($password = '', $name = '')
    {
        $eth = $this->News;
        return $eth->newAccount($password,$name);
    }

    /**获取余额
     * @param $address
     * @return number
     */
    public function getBalance($address)
    {
        $eth = $this->News;
        return $eth->getBalance($address);
    }

    /**转账
     * @param $from
     * @param $to
     * @param $eth
     * @param string $passwd
     * @return bool|mixed|string
     */
    public function transfer($from, $to, $num, $passwd = "123456")
    {
        $eth = $this->News;
        return $eth->transfer($from, $to, $num, $passwd);
    }

    /**交易状态查询（不必要的）
     * @param $hash
     * @return bool|mixed|string
     */
    public function txStatus($hash)
    {
        $eth = $this->News;
        return $eth->txStatus($hash);
    }
}