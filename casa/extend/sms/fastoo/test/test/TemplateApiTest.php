<?php 
include_once '/sdk/client/HttpPostClient.php';
include_once '/sdk/model/TemplateReturn.php';
include_once '/sdk/api/TemplateApi.php';
/**
 * 测试--签名报备
 * @author lb
 * @version 1.0
 * @date 2017-08-31
 */
class TemplateApiTest {
	/**
	 * 获取签名报备
	 */
	public static function TestSearch(){
		echo("===================测试查询========================"."\n");
		$TemplateApi=new TemplateApi();
		$bean=$TemplateApi->TemplateSearch("a47dd09338834deda2e0e717a90e2cce", "","", "0");
		echo "返回字符串：".$bean->jsonstr."\n";
		echo "code：".$bean->code."\n";
		echo "msg:".$bean->msg."\n";
		if($bean->code==0){
			echo("访问成功==========》发送成功");
			for($i=0;$i<sizeof($bean->templatelist);$i++){
				$obj=$bean->templatelist[$i];
				echo("详细数据：".$i."\n");
				echo("apiKey:".$obj->apiKey."\n");
				echo("appUrl:".$obj->appUrl."\n");
				echo("applyTime:".$obj->applyTime."\n");
				echo("auditTime:".$obj->auditTime."\n");
				echo("id:".$obj->id."\n");
				echo("reason:".$obj->reason."\n");
				echo("state:".$obj->state."\n");
				echo("templateContent:".$obj->templateContent."\n");
				echo("templateType:".$obj->templateType."\n");
			}
		}else{
			echo("访问成功==========》发送失败："."\n");
		}
		echo("==========》访问失败："."\n");
		$bean=$TemplateApi->TemplateSearch("111", "a47dd09338834deda2e0e717a90e2cce","测试", "0");
		echo "返回字符串：".$bean->jsonstr."\n";
		echo "code：".$bean->code."\n";
		echo "msg:".$bean->msg."\n";
	}
	/**
	 * 添加/修改签名报备
	 */
	public static function TestSave(){
		echo("===================测试添加/修改签名报备========================"."\n");
		$TemplateApi=new TemplateApi();
		$bean=$TemplateApi->TemplateSave("a47dd09338834deda2e0e717a90e2cce", "1", "测试", 
				"7ae8a782847e49078a690df27e517fcf", "1", "http://service.blueorigintech.com/shs/wap/user/register");
		echo "返回字符串：".$bean->jsonstr."\n";
		echo "code：".$bean->code."\n";
		echo "msg:".$bean->msg."\n";
		if($bean->code==0){
			echo("访问成功==========》发送成功："."\n");
		}else{
			echo("访问成功==========》发送失败："."\n");
		}
		echo("==========》访问失败："."\n");
		$bean=$TemplateApi->TemplateSave("111", "1", "测试", 
				"7ae8a782847e49078a690df27e517fcf", "1", "http://service.blueorigintech.com/shs/wap/user/register");
		echo "返回字符串：".$bean->jsonstr."\n";
		echo "code：".$bean->code."\n";
		echo "msg:".$bean->msg."\n";
	}
	/**
	 * 添加/修改签名报备
	 */
	public static function TestDel(){
		echo("===================测试删除签名报备========================"."\n");
		$TemplateApi=new TemplateApi();
		$bean=$TemplateApi->TemplateDel("a47dd09338834deda2e0e717a90e2cce", "1");
		echo "返回字符串：".$bean->jsonstr."\n";
		echo "code：".$bean->code."\n";
		echo "msg:".$bean->msg."\n";
		if($bean->code==0){
			echo("访问成功==========》发送成功："."\n");
		}else{
			echo("访问成功==========》发送失败："."\n");
		}
		echo("==========》访问失败："."\n");
		$bean=$TemplateApi->TemplateDel("111", "1");
		echo "返回字符串：".$bean->jsonstr."\n";
		echo "code：".$bean->code."\n";
		echo "msg:".$bean->msg."\n";
	}	
}
	$test=new TemplateApiTest();
	$test->TestSearch();
	$test->TestSave();
	$test->TestDel();
?>