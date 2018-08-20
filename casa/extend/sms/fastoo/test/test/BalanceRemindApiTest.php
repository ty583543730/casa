<?php 
include_once '/sdk/client/HttpPostClient.php';
include_once '/sdk/api/BalanceRemindApi.php';
include_once '/sdk/model/UserGetReturn.php';
include_once '/sdk/model/UserInfoParm.php';
/**
 * 测试--余额提醒Api
 * @author lb
 * @version 1.0
 * @date 2017-08-31
 */
class BalanceRemindApiTest {
	
	/**
	 * 获取余额提醒
	 */
	public static function TestGetBalanceRemindInfo(){
		echo("==========》访问成功："."\n");
		$BalanceRemindApi=new BalanceRemindApi();
		$bean=$BalanceRemindApi->GetBalanceRemindInfo("a47dd09338834deda2e0e717a90e2cce");
		echo "返回字符串：".$bean->jsonstr."\n";
		echo "code：".$bean->code."\n";
		echo "msg:".$bean->msg."\n";
		if($bean->code==0){
			echo("访问成功==========》发送成功");
			echo("loginName：".$bean->loginName."\n");
			echo("startTime：".$bean->startTime."\n");
			echo("remindBalance：".$bean->remindBalance."\n");
			echo("endTime：".$bean->endTime."\n");
		}else{
			echo("访问成功==========》发送失败："."\n");
		}
		echo("==========》访问失败："."\n");
		$bean=$BalanceRemindApi->GetBalanceRemindInfo("111");
		echo "返回字符串：".$bean->jsonstr."\n";
		echo "code：".$bean->code."\n";
		echo "msg:".$bean->msg."\n";
	}
	/**
	 *设置余额提醒
	 */
	public static function TestSetBalanceRemind(){
		echo("==========》访问成功："."\n");
		$BalanceRemindApi=new BalanceRemindApi();
		$bean=$BalanceRemindApi->SetBalanceRemind("a47dd09338834deda2e0e717a90e2cce","100", "12:00", "20:00");
		echo "返回字符串：".$bean->jsonstr."\n";
		echo "code：".$bean->code."\n";
		echo "msg:".$bean->msg."\n";
		echo("==========》访问失败："."\n");
		$bean=$BalanceRemindApi->SetBalanceRemind("111","100", "12:00", "20:00");
		echo "返回字符串：".$bean->jsonstr."\n";
		echo "code：".$bean->code."\n";
		echo "msg:".$bean->msg."\n";
	}
	
}
	$test=new BalanceRemindApiTest();
	$test->TestGetBalanceRemindInfo();
   	$test->TestSetBalanceRemind();

?>
