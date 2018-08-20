<?php 

include_once '/sdk/client/HttpPostClient.php';
include_once '/sdk/model/SmsBatchRecordReturn.php';
include_once '/sdk/model/SmsExportReordReturn.php';
include_once '/sdk/model/SmsSendRecordParm.php';
include_once '/sdk/model/SmsSendRecordReturn.php';
include_once '/sdk/api/SmsSearchAPI.php';

/**
 * 测试-发送记录
 * @author lb
 * @version 1.0
 * @date 2017-08-31
 */
class SmsSearchAPITest {
	/**
	 * 测试-获取发送记录
	 */
	public static function Testrecords(){
		echo("===================测试查询========================"."\n");
		$parm=new SmsSendRecordParm("a47dd09338834deda2e0e717a90e2cce", "a47dd09338834deda2e0e717a90e2cce",
				"2017-08-22 00:00:00", "2017-08-28 23:59:59", "13045377878", "0");
		$SmsSearchAPI=new SmsSearchAPI();
		$bean=$SmsSearchAPI->records($parm);
		echo "返回字符串：".$bean->jsonstr."\n";
		echo "code：".$bean->code."\n";
		echo "msg:".$bean->msg."\n";
		if($bean->code==0){
			echo("访问成功==========》发送成功"."\n");
			echo "selectApiKey：".$bean->selectApiKey."\n";
			echo "createTimeEnd：".$bean->createTimeEnd."\n";
			echo "createTimeStart：".$bean->createTimeStart."\n";
			echo "destAddr：".$bean->destAddr."\n";
			echo "size：".$bean->size."\n";
			echo "totalPages：".$bean->totalPages."\n";
			echo "totalElements：".$bean->totalElements."\n";
			for($i=0;$i<sizeof($bean->smsrecords);$i++){
				$obj=$bean->smsrecords[$i];
				echo("详细数据：".$i."\n");
				echo("apiKey:".$obj->apiKey."\n");
				echo("countryCode:".$obj->countryCode."\n");
				echo("destAddr:".$obj->destAddr."\n");
				echo("drErrorcode:".$obj->drErrorcode."\n");
				echo("drStatus:".$obj->drStatus."\n");
				echo("drStatuscode:".$obj->drStatuscode."\n");
				echo("feeCount:".$obj->feeCount."\n");
				echo("localMsgid:".$obj->localMsgid."\n");
				echo("loginName:".$obj->loginName."\n");
				echo("msgContent:".$obj->msgContent."\n");
				echo("mtErrorcode:".$obj->mtErrorcode."\n");
				echo("mtStatus:".$obj->mtStatus."\n");
				echo("mtStatuscode:".$obj->mtStatuscode."\n");
				echo("price:".$obj->price."\n");
				echo("submitTime:".$obj->submitTime."\n");
			}
		}else{
			echo("访问成功==========》发送失败："."\n");
		}
		echo("==========》访问失败："."\n");
		$parm=new SmsSendRecordParm("111", "a47dd09338834deda2e0e717a90e2cce",
				"2017-08-22 00:00:00", "2017-08-28 23:59:59", "13045377878", "0");
		$bean=$SmsSearchAPI->records($parm);
		echo "返回字符串：".$bean->jsonstr."\n";
		echo "code：".$bean->code."\n";
		echo "msg:".$bean->msg."\n";
	}
	/**
	 * 测试-获取批量发送记录
	 */
	public static function TestbatchRecords(){
		echo("===================测试查询========================"."\n");
		$SmsSearchAPI=new SmsSearchAPI();
		$bean=$SmsSearchAPI->batchRecords("a47dd09338834deda2e0e717a90e2cce", "test", "2017-08-22");
		echo "返回字符串：".$bean->jsonstr."\n";
		echo "code：".$bean->code."\n";
		echo "msg:".$bean->msg."\n";
		if($bean->code==0){
			echo("访问成功==========》发送成功"."\n");
			echo "size：".$bean->size."\n";
			echo "totalPages：".$bean->totalPages."\n";
			echo "totalElements：".$bean->totalElements."\n";
			for($i=0;$i<sizeof($bean->batchDtos);$i++){
				$obj=$bean->batchDtos[$i];
				echo("详细数据：".$i."\n");
				echo("apiKey:".$obj->apiKey."\n");
				echo("batchName:".$obj->batchName."\n");
				echo("content:".$obj->content."\n");
				echo("id:".$obj->id."\n");
				echo("sendFailNum:".$obj->sendFailNum."\n");
				echo("sendSuccessNum:".$obj->sendSuccessNum."\n");
				echo("status:".$obj->status."\n");
				echo("submitTime:".$obj->submitTime."\n");
				echo("submitTimeDt:".$obj->submitTimeDt."\n");
				echo("timing:".$obj->timing."\n");
				echo("totalNum:".$obj->totalNum."\n");
				echo("unSendNum:".$obj->unSendNum."\n");
				echo("userBaseId:".$obj->userBaseId."\n");
			}
		}else{
			echo("访问成功==========》发送失败："."\n");
		}
		echo("==========》访问失败："."\n");
		$bean=$SmsSearchAPI->batchRecords("111", "test", "2017-08-22");
		echo "返回字符串：".$bean->jsonstr."\n";
		echo "code：".$bean->code."\n";
		echo "msg:".$bean->msg."\n";
	}
	/**
	 * 测试-获取上月发送记录
	 */
	public static function TestRecordEexport(){
		echo("===================测试查询========================"."\n");
		$SmsSearchAPI=new SmsSearchAPI();
		$bean=$SmsSearchAPI->RecordEexport("a47dd09338834deda2e0e717a90e2cce");
		echo "返回字符串：".$bean->jsonstr."\n";
		echo "code：".$bean->code."\n";
		echo "msg:".$bean->msg."\n";
		if($bean->code==0){
			echo("访问成功==========》发送成功"."\n");
			for($i=0;$i<sizeof($bean->list);$i++){
				$obj=$bean->list[$i];
				echo("详细数据：".$i."\n");
				echo("apiKey:".$obj->apiKey."\n");
				echo("countryCode:".$obj->countryCode."\n");
				echo("destAddr:".$obj->destAddr."\n");
				echo("drStatus:".$obj->drStatus."\n");
				echo("feeCount:".$obj->feeCount."\n");
				echo("msgContent:".$obj->msgContent."\n");
				echo("mtStatus:".$obj->mtStatus."\n");
				echo("price:".$obj->price."\n");
				echo("submitTime:".$obj->submitTime."\n");
			}
		}else{
			echo("访问成功==========》发送失败："."\n");
		}
		echo("==========》访问失败："."\n");
		$bean=$SmsSearchAPI->RecordEexport("111");
		echo "返回字符串：".$bean->jsonstr."\n";
		echo "code：".$bean->code."\n";
		echo "msg:".$bean->msg."\n";
	}
}
$test=new SmsSearchAPITest();
$test->Testrecords();
$test->TestbatchRecords();
$test->TestRecordEexport();
?>
