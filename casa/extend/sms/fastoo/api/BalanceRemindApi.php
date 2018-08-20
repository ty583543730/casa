<?php
namespace sms\fastoo\api;
/**
 * 余额提醒Api
 * @author lb
 * @version 1.0
 * @date 2017-08-31
 */
use sms\fastoo\client\HttpPostClient;
use sms\fastoo\model\BalanceRemindInfoReturn;

class BalanceRemindApi {
	
	/**
	 * 获取余额提醒
	 * @param apiKey 用户唯一标识
	 * @return
	 */
	public static function GetBalanceRemindInfo($apiKey){
		$parmstr=array(
				"apiKey"=>$apiKey
		);
		$url=new URLConfig();
		$client=new HttpPostClient();
		$bean=new BalanceRemindInfoReturn($client->sendPost($url->GetBalanceRemindInfoApiURL,$parmstr));
		return $bean;
	}
	/**
	 * 设置余额提醒
	 * @param apiKey 用户唯一标识
	 * @param remindBalance 提醒金额
	 * @param startTime 提醒开始时间
	 * @param endTime 提醒结束时间
	 * @return
	 */
	public static function SetBalanceRemind($apiKey,$remindBalance,$startTime,$endTime){
		$parmstr=array(
				"apiKey"=>$apiKey,
				"remindBalance"=>$remindBalance,
				"startTime"=>$startTime,
				"endTime"=>$endTime,
		);
		$url=new URLConfig();
		$client=new HttpPostClient();
		$bean=new ReturnModel($client->sendPost($url->SetBalanceRemindApiURL,$parmstr));
		return $bean;
	}
}
?>
