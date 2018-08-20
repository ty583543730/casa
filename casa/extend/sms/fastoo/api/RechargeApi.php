<?php
namespace sms\fastoo\api;


use sms\fastoo\model\RechargeAlipayReturn;
use sms\fastoo\model\RechargeReturn;
use sms\fastoo\client\HttpPostClient;


/**
 * 账户充值Api
 * @author lb
 * @version 1.0
 * @date 2017-08-31
 */
class RechargeApi {
	/**
	 * 账户充值
	 * @param apiKey 用户唯一标识
	 * @param money 充值金额
	 * @return
	 */
	public static function RechargeAlipay($apiKey,$money){
		$parmstr=array(
				"apiKey"=>$apiKey,
				"money"=>$money
		);
		$url=new URLConfig();
		$client=new HttpPostClient();
		$bean=new RechargeAlipayReturn($client->sendPost($url->RechargeAlipayApiURL,$parmstr));
		return $bean;
	}
	/**
	 * 获取充值记录
	 * @param apiKey 用户唯一标识
	 * @param pageNo 页码
	 * @return
	 */
	public static function RechargeSearch($apiKey,$pageNo){
		$parmstr=array(
				"apiKey"=>$apiKey,
				"pageNo"=>$pageNo
		);
		$url=new URLConfig();
		$client=new HttpPostClient();
		$bean=new RechargeReturn($client->sendPost($url->RechargeSearchApiURL,$parmstr));
		return $bean;
	}
	
}
?>
