<?php
/**
 * usdt的Api入口
 * User: Bourne
 * Date: 2018-3-5 10:23
 */

namespace blockchain\usdt;


class Api
{
    public $Bitcoin = '';
    public $Propertyid = 1;

    public function __construct()
    {
        require_once('easybitcoin.php');
        $app = config("app_status"); //dev-测试用  pro-生产用
        if ($app == "dev") {
            $this->Propertyid = 1;
        } else if ($app == "pro") {
            $this->Propertyid = 31;
        }
        $this->Bitcoin = new Bitcoin($app);
    }

    /**获取余额
     * @param $address 地址
     * @return number
     */
    public function getBalance($address)
    {
        if ($address == '') {
            return 0;
        }
        $result = $this->Bitcoin->omni_getbalance($address, $this->Propertyid);//查询余额
        return $result['balance'];
    }

    /**创建新地址
     * @return mixed
     */
    public function newAccount()
    {
        return $this->Bitcoin->getnewaddress(); //创建新地址
    }

    /** 转账
     * @param $from  转出者的地址
     * @param $to    接收者的地址
     * @param $eth  数量
     * @return bool|mixed|string
     */
    public function transfer($from, $to, $eth)
    {
        return $this->Bitcoin->omni_send($from, $to, $this->Propertyid, $eth); //转账，转出者的地址 接收者的地址 属性标识符 数量
    }

    /**交易状态查询
     * @param $hash
     * @return bool|mixed|string
     */
    public function txStatus($hash)
    {
        $result = $this->Bitcoin->omni_gettransaction($hash);//查询交易状态
        if (!array_key_exists('valid', $result)) {
            return false;
        } else {
            return true;
        }
    }
}