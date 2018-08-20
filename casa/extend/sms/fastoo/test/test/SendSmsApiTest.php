<?php 
include_once '/sdk/client/HttpPostClient.php';
include_once '/sdk/model/SendSingleSmsParm.php';
include_once '/sdk/model/SendBatchSmsParm.php';
include_once '/sdk/api/SendSmsApi.php';
/**
 * 测试--发送短信Api
 * @author lb
 * @version 1.0
 * @date 2017-08-31
 */
 class SendSmsApiTest {
	/**
	 * 单条发送短信
	 */
	public static function TestQuickSendSingleSms(){
		$SendSmsApi=new SendSmsApi();
		$parm=new SendSingleSmsParm("a47dd09338834deda2e0e717a90e2cce", "China", "15711586282", "domestic", "");
		$bean=$SendSmsApi->QuickSendSingleSms($parm);
		echo "返回字符串：".$bean->jsonstr."\n";
		echo "code：".$bean->code."\n";
		echo "msg:".$bean->msg."\n";
		if($bean->code==0){
			echo("访问成功==========》发送成功");
		}else{
			echo("访问成功==========》发送失败："."\n");
		}
		echo("==========》访问失败：");
		$parm=new SendSingleSmsParm("111", "China", "15711586282", "domestic", "");
		$bean=$SendSmsApi->QuickSendSingleSms($parm);
		echo "返回字符串：".$bean->jsonstr."\n";
		echo "code：".$bean->code."\n";
		echo "msg:".$bean->msg."\n";
	}
	/**
	 * 发送短信
	 */
	public static function TestSubmit(){
		$SendSmsApi=new SendSmsApi();
		$bean=$SendSmsApi->Submit("a47dd09338834deda2e0e717a90e2cce", "8615711586282,8613037372345", "hello");
		echo "返回字符串：".$bean->jsonstr."\n";
		echo "code：".$bean->code."\n";
		echo "msg:".$bean->msg."\n";
		if($bean->code==0){
			echo("访问成功==========》发送成功");
		}else{
			echo("访问成功==========》发送失败："."\n");
		}
		echo("==========》访问失败：");
		$bean=$SendSmsApi->Submit("111", "8615711586282", "hello");
		echo "返回字符串：".$bean->jsonstr."\n";
		echo "code：".$bean->code."\n";
		echo "msg:".$bean->msg."\n";
	}
	/**
	 * 平台批量发送短信
	 */
	public static function TestQuickSendBatchSms(){
		$parm=new SendBatchSmsParm("a47dd09338834deda2e0e717a90e2cce","a47dd09338834deda2e0e717a90e2cce", 
				"20170822_test", "13788853675,13037372345", "abc", "2017-08-28 00:00");
		$SendSmsApi=new SendSmsApi();
		$bean=$SendSmsApi->QuickSendBatchSms($parm);
		echo "返回字符串：".$bean->jsonstr."\n";
		echo "code：".$bean->code."\n";
		echo "msg:".$bean->msg."\n";
		if($bean->code==0){
			echo("访问成功==========》发送成功");
		}else{
			echo("访问成功==========》发送失败："."\n");
		}
		echo("==========》访问失败：");
		$parm=new SendBatchSmsParm("111", "a47dd09338834deda2e0e717a90e2cce",
				 "20170822_test", "13788853675,13037372345", "abc", "2017-08-28 00:00");
		$bean=$SendSmsApi->QuickSendBatchSms($parm);
		echo "返回字符串：".$bean->jsonstr."\n";
		echo "code：".$bean->code."\n";
		echo "msg:".$bean->msg."\n";
	}
	/**
	 * 发送记录状态设置
	 */
	public static function TestBatchStatus(){
		$SendSmsApi=new SendSmsApi();
		$bean=$SendSmsApi->BatchStatus("a47dd09338834deda2e0e717a90e2cce", "2", "1");
		echo "返回字符串：".$bean->jsonstr."\n";
		echo "code：".$bean->code."\n";
		echo "msg:".$bean->msg."\n";
		if($bean->code==0){
			echo("访问成功==========》发送成功");
		}else{
			echo("访问成功==========》发送失败："."\n");
		}
		echo("==========》访问失败：");
		$bean=$SendSmsApi->BatchStatus("111", "1", "1");
		echo "返回字符串：".$bean->jsonstr."\n";
		echo "code：".$bean->code."\n";
		echo "msg:".$bean->msg."\n";
	}
}
$test=new SendSmsApiTest();
$test->TestQuickSendSingleSms();
$test->TestSubmit();
$test->TestQuickSendBatchSms();
$test->TestBatchStatus();
?>
