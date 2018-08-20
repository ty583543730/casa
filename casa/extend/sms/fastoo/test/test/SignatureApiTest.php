<?php 

include_once '/sdk/client/HttpPostClient.php';
include_once '/sdk/api/SignatureApi.php';
/**
 * 测试--签名报备
 * @author lb
 * @version 1.0
 * @date 2017-08-31
 */ class SignatureApiTest {
	/**
	 * 获取签名报备
	 */
	public static function TestSearch(){
		echo("===================测试查询========================"."\n");
		$SignatureApi=new SignatureApi();
		$bean=$SignatureApi->SignatureSearch("a47dd09338834deda2e0e717a90e2cce", "");
		echo "返回字符串：".$bean->jsonstr."\n";
		echo "code：".$bean->code."\n";
		echo "msg:".$bean->msg."\n";
		if($bean->code==0){
			echo("访问成功==========》发送成功");
			for($i=0;$i<sizeof($bean->SignatureList);$i++){
				$obj=$bean->SignatureList[$i];
				echo("详细数据：".$i."\n");
				echo("apiKey:".$obj->apiKey."\n");
				echo("signApplyTime:".$obj->signApplyTime."\n");
				echo("signAuditTime:".$obj->signAuditTime."\n");
				echo("signName:".$obj->signName."\n");
				echo("signStatus:".$obj->signStatus."\n");
			}
		}else{
			echo("访问成功==========》发送失败：");
		}
		echo("==========》访问失败：");
		$bean=$SignatureApi->SignatureSearch("1111", "");
		echo "返回字符串：".$bean->jsonstr."\n";
		echo "code：".$bean->code."\n";
		echo "msg:".$bean->msg."\n";
	}
	/**
	 * 添加/修改签名报备
	 */
	public static function TestSave(){
		echo("===================测试添加/修改签名报备========================"."\n");
		$SignatureApi=new SignatureApi();
		$bean=$SignatureApi->SignatureSave("a47dd09338834deda2e0e717a90e2cce", "a47dd09338834deda2e0e717a90e2cce", "测试");
		echo "返回字符串：".$bean->jsonstr."\n";
		echo "code：".$bean->code."\n";
		echo "msg:".$bean->msg."\n";
		if($bean->code==0){
			echo("访问成功==========》发送成功");
		}else{
			echo("访问成功==========》发送失败：");
		}
		echo("==========》访问失败：");
		$bean=$SignatureApi->SignatureSave("1111", "a47dd09338834deda2e0e717a90e2cce", "测试");
		echo "返回字符串：".$bean->jsonstr."\n";
		echo "code：".$bean->code."\n";
		echo "msg:".$bean->msg."\n";

	}
	/**
	 * 添加/修改签名报备
	 */
	public static function TestDel(){
		echo("===================测试删除签名报备========================");
		$SignatureApi=new SignatureApi();
		$bean=$SignatureApi->SignatureDel("a47dd09338834deda2e0e717a90e2cce", "a47dd09338834deda2e0e717a90e2cce");
		echo "返回字符串：".$bean->jsonstr."\n";
		echo "code：".$bean->code."\n";
		echo "msg:".$bean->msg."\n";
		if($bean->code==0){
			echo("访问成功==========》发送成功");
		}else{
			echo("访问成功==========》任务失败：");
		}
		echo("==========》访问失败：");
		$bean=$SignatureApi->SignatureDel("1111", "a47dd09338834deda2e0e717a90e2cce");
		echo "返回字符串：".$bean->jsonstr."\n";
		echo "code：".$bean->code."\n";
		echo "msg:".$bean->msg."\n";

	}
}
$test=new SignatureApiTest();
$test->TestSearch();
$test->TestSave();
$test->TestDel();
?>
