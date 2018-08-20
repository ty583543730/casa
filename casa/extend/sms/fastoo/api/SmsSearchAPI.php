<?php
namespace sms\fastoo\api;

use sms\fastoo\model\ReturnModel;
use sms\fastoo\model\SmsBatchRecordReturn;
use sms\fastoo\model\SmsExportReordReturn;
use sms\fastoo\model\SmsSendRecordParm;
use sms\fastoo\model\SmsSendRecordReturn;
use sms\fastoo\client\HttpPostClient;

/**
 * 发送记录
 * @author lb
 * @version 1.0
 * @date 2017-08-31
 */
class SmsSearchAPI {
	/**
	 * 获取发送记录
	 * @param parm
	 * @return
	 */
	public static function records($parm){
		$parmstr=array(
				"apiKey"=>$parm->apiKey,
				"pageNo"=>$parm->pageNo,
				"selectApiKey"=>$parm->selectApiKey,
				"createTimeStart"=>$parm->createTimeStart,
				"createTimeEnd"=>$parm->createTimeEnd,
				"destAddr"=>$parm->destAddr
		);
		$url=new URLConfig();
		$client=new HttpPostClient();
		$bean=new SmsSendRecordReturn($client->sendPost($url->SmsRecordsApiURL,$parmstr));
		return $bean;
	}
	/**
	 * 获取批量发送记录
	 * @param apiKey 用户唯一标识
	 * @param batchName 批次名称
	 * @param submitTime 提交时间
	 * @return
	 */
	public static function batchRecords($apiKey,$batchName ,$submitTime){
		$parmstr=array(
				"apiKey"=>$apiKey,
				"batchName"=>$batchName,
				"submitTime"=>$submitTime
		);
		$url=new URLConfig();
		$client=new HttpPostClient();
		$bean=new SmsBatchRecordReturn($client->sendPost($url->SmsBatchRecordsApiURL,$parmstr));
		return $bean;
	}
	/**
	 * 获取上月发送记录
	 * @param apiKey 用户唯一标识
	 * @return
	 */
	public static function RecordEexport($apiKey){
		$parmstr=array(
				"apiKey"=>$apiKey
		);
		$url=new URLConfig();
		$client=new HttpPostClient();
		$bean=new SmsExportReordReturn($client->sendPost($url->SmsExportRecordsApiURL,$parmstr));
		return $bean;
	}
}
?>
