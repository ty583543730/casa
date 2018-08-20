<?php 
include_once '/sdk/client/HttpPostClient.php';
include_once '/sdk/model/ApiKeyReturn.php';
include_once '/sdk/model/ReturnModel.php';
include_once '/sdk/api/ApiKeysApi.php';
/**
 * 测试-ApiKeys
 * @author lb
 * @version 1.0
 * @date 2017-08-31
 */
 class ApiKeysApiTest {
	/**
	 * 查询APIKey
	 */
	public  function TestApiKeysSearch(){
		$ApiKeysApi=new ApiKeysApi();
		echo("===================测试查询========================"."\n");
		$bean=$ApiKeysApi->ApiKeysSearch("a47dd09338834deda2e0e717a90e2cce", "");
		echo "返回字符串：".$bean->jsonstr."\n";
		echo "code：".$bean->code."\n";
		echo "msg:".$bean->msg."\n";
		if($bean->code==0){
			echo("访问成功==========》发送成功"."\n");
			for($i=0;$i<sizeof($bean->APIKeyList);$i++){
				$obj=$bean->APIKeyList[$i];
				echo("详细数据：".$i."\n");
				echo("apiKey:".$obj->apiKey."\n");
				echo("drUrl:".$obj->drUrl."\n");
				echo("id:".$obj->id."\n");
			}
		}else{
			echo("访问成功==========》发送失败："."\n");
		}
		echo("==========》访问失败："."\n");
		$bean=$ApiKeysApi->ApiKeysSearch("1111", "测试");
		echo "返回字符串：".$bean->jsonstr."\n";
		echo "code：".$bean->code."\n";
		echo "msg:".$bean->msg."\n";
	}
	/**
	 * 增加APIKey
	 */
	public  function TestApiKeysAdd(){
		$ApiKeysApi=new ApiKeysApi();
		echo("===================测试添加ApiKeys========================"."\n");
		$bean=$ApiKeysApi->ApiKeysAdd("a47dd09338834deda2e0e717a90e2cce");
		echo "返回字符串：".$bean->jsonstr."\n";
		echo "code：".$bean->code."\n";
		echo "msg:".$bean->msg."\n";
		if($bean->code==0){
			echo("访问成功==========》发送成功");
		}else{
			echo("访问成功==========》发送失败：");
		}
		echo("==========》访问失败：");
		$bean=$ApiKeysApi->ApiKeysAdd("111");
		echo "返回字符串：".$bean->jsonstr."\n";
		echo "code：".$bean->code."\n";
		echo "msg:".$bean->msg."\n";
	}
	/**
	 * 修改APIKey
	 */
	public  function TestApiKeysSaveDr(){
		$ApiKeysApi=new ApiKeysApi();
		echo("===================测试修改ApiKeys========================"."\n");
		$bean=$ApiKeysApi->ApiKeysSaveDr("a47dd09338834deda2e0e717a90e2cce", "1", "http://test.heysky.com/dr");
		echo "返回字符串：".$bean->jsonstr."\n";
		echo "code：".$bean->code."\n";
		echo "msg:".$bean->msg."\n";
		if($bean->code==0){
			echo("访问成功==========》发送成功");
		}else{
			echo("访问成功==========》发送失败：");
		}
		echo("==========》访问失败：");
		$bean=$ApiKeysApi->ApiKeysSaveDr("111", "1", "http://test.heysky.com/dr");
		echo "返回字符串：".$bean->jsonstr."\n";
		echo "code：".$bean->code."\n";
		echo "msg:".$bean->msg."\n";
	}
	/**
	 * 删除APIKey
	 */
	public  function TestApiKeysDel(){
		$ApiKeysApi=new ApiKeysApi();
		echo("===================测试删除ApiKeys========================"."\n");
		$bean=$ApiKeysApi->ApiKeysDel("a47dd09338834deda2e0e717a90e2cce", "1");
		echo "返回字符串：".$bean->jsonstr."\n";
		echo "code：".$bean->code."\n";
		echo "msg:".$bean->msg."\n";
		if($bean->code==0){
			echo("访问成功==========》发送成功");
		}else{
			echo("访问成功==========》发送失败：");
		}
		echo("==========》访问失败：");
		$bean=$ApiKeysApi->ApiKeysDel("111", "1");
		echo "返回字符串：".$bean->jsonstr."\n";
		echo "code：".$bean->code."\n";
		echo "msg:".$bean->msg."\n";
	}
}
$test=new ApiKeysApiTest();
$test->TestApiKeysSearch();
$test->TestApiKeysAdd();
$test->TestApiKeysSaveDr();
$test->TestApiKeysDel();
?>
