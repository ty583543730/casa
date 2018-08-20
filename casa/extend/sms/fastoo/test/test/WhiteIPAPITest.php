<?php 

include_once '/sdk/client/HttpPostClient.php';
include_once '/sdk/model/WhiteIPReturn.php';
include_once '/sdk/api/WhiteIPAPI.php';
/**
 * 测试-白名单WhiteIPAPI
 * @author lb
 * @version 1.0
 * @date 2017-08-31
 */ 
 class WhiteIPAPITest {
 
	/**
	 * 获取IP白名单
	 */
	public static function TestWhiteIPSearch(){
		echo("===================测试查询========================"."\n");
		$WhiteIPAPI=new WhiteIPAPI();
		$bean=$WhiteIPAPI->WhiteIPSearch("a47dd09338834deda2e0e717a90e2cce", "");
		echo "返回字符串：".$bean->jsonstr."\n";
		echo "code：".$bean->code."\n";
		echo "msg:".$bean->msg."\n";
		if($bean->code==0){
			echo("访问成功==========》发送成功");
			for($i=0;$i<sizeof($bean->userIplists);$i++){
				$obj=$bean->userIplists[$i];
				echo("详细数据：".$i."\n");
				echo("createTime:".$obj->createTime."\n");
				echo("id:".$obj->id."\n");
				echo("ip:".$obj->ip."\n");
				echo("remark:".$obj->remark."\n");
			}
		}else{
			echo("访问成功==========》发送失败："."\n");
		}
		echo("==========》访问失败：");
		$bean=$WhiteIPAPI->WhiteIPSearch("1111", "127.0.0.1");
		echo "返回字符串：".$bean->jsonstr."\n";
		echo "code：".$bean->code."\n";
		echo "msg:".$bean->msg."\n";
	}
	/**
	 *  添加/修改IP白名单
	 */
	public static function TestWhiteIPSave(){
		echo("===================测试添加/修改IP白名单========================"."\n");
		$WhiteIPAPI=new WhiteIPAPI();
		$bean=$WhiteIPAPI->WhiteIPSave("a47dd09338834deda2e0e717a90e2cce","123.123.32.24","测试服务器");
		echo "返回字符串：".$bean->jsonstr."\n";
		echo "code：".$bean->code."\n";
		echo "msg:".$bean->msg."\n";
		if($bean->code==0){
			echo("访问成功==========》发送成功："."\n");
		}else{
			echo("访问成功==========》发送失败："."\n");
		}
		echo("==========》访问失败："."\n");
		$bean=$WhiteIPAPI->WhiteIPSave("1111","123.123.32.24","测试服务器");
		echo "返回字符串：".$bean->jsonstr."\n";
		echo "code：".$bean->code."\n";
		echo "msg:".$bean->msg."\n";
	}
	/**
	 * 删除IP白名单
	 */
	public static function TestWhiteIPDel(){
		echo("===================测试删除IP白名单========================"."\n");
		$WhiteIPAPI=new WhiteIPAPI();
		$bean=$WhiteIPAPI->WhiteIPDel("a47dd09338834deda2e0e717a90e2cce", "1");
		echo "返回字符串：".$bean->jsonstr."\n";
		echo "code：".$bean->code."\n";
		echo "msg:".$bean->msg."\n";
		if($bean->code==0){
			echo("访问成功==========》发送成功："."\n");
		}else{
			echo("访问成功==========》发送失败："."\n");
		}
		echo("==========》访问失败："."\n");
		$bean=$WhiteIPAPI->WhiteIPDel("111", "1");
		echo "返回字符串：".$bean->jsonstr."\n";
		echo "code：".$bean->code."\n";
		echo "msg:".$bean->msg."\n";
	}
	/**
	 * 设置IP白名单保护
	 */
	public static function TestWhiteIPSwitch(){
		echo("===================测试设置IP白名单保护========================"."\n");
		$WhiteIPAPI=new WhiteIPAPI();
		$bean=$WhiteIPAPI->WhiteIPSwitch("a47dd09338834deda2e0e717a90e2cce","1");
		echo "返回字符串：".$bean->jsonstr."\n";
		echo "code：".$bean->code."\n";
		echo "msg:".$bean->msg."\n";
		if($bean->code==0){
			echo("访问成功==========》发送成功："."\n");
		}else{
			echo("访问成功==========》发送失败："."\n");
		}
		echo("==========》访问失败：");
		$bean=$WhiteIPAPI->WhiteIPSwitch("a47dd09338834deda2e0e717a90e2cce","1");
		echo "返回字符串：".$bean->jsonstr."\n";
		echo "code：".$bean->code."\n";
		echo "msg:".$bean->msg."\n";
	}
}
$test=new WhiteIPAPITest();
$test->TestWhiteIPSearch();
$test->TestWhiteIPSave();
$test->TestWhiteIPDel();
$test->TestWhiteIPSwitch();
?>
