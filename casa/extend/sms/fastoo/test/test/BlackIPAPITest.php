<?php 
include_once '/sdk/client/HttpPostClient.php';
include_once '/sdk/model/BlackIPReturn.php';
include_once '/sdk/api/BlackIPAPI.php';
/**
 * 测试-黑名单BlackIPAPI
 * @author lb
 * @version 1.0
 * @date 2017-08-31
 */
class BlackIPAPITest {
	/**
	 * 获取IP黑名单
	 */
	public function TestBlackIPSearch(){
		echo("===================测试查询========================"."\n");
		$BlackIPAPI=new BlackIPAPI();
		$bean=$BlackIPAPI->BlackIPSearch("a47dd09338834deda2e0e717a90e2cce", "张三");
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
				echo("phone:".$obj->phone."\n");
				echo("userName:".$obj->userName."\n");
			}
		}else{
			echo("访问成功==========》发送失败："."\n");
		}
		echo("==========》访问失败："."\n");
		$bean=$BlackIPAPI->BlackIPSearch("1111", "张三");
		echo "返回字符串：".$bean->jsonstr."\n";
		echo "code：".$bean->code."\n";
		echo "msg:".$bean->msg."\n";
	}
	/**
	 *  添加/修改IP黑名单
	 */
	public function TestBlackIPSave(){
		echo("===================测试添加/修改IP黑名单========================"."\n");
		$BlackIPAPI=new BlackIPAPI();
		$bean=$BlackIPAPI->BlackIPSave("a47dd09338834deda2e0e717a90e2cce","13023815549","张三","1");
		echo "返回字符串：".$bean->jsonstr."\n";
		echo "code：".$bean->code."\n";
		echo "msg:".$bean->msg."\n";
		if($bean->code==0){
			echo("访问成功==========》发送成功："."\n");
		}else{
			echo("访问成功==========》发送失败："."\n");
		}
		echo("==========》访问失败："."\n");
		$bean=$BlackIPAPI->BlackIPSave("1111","13023815549","张三","1");
		echo "返回字符串：".$bean->jsonstr."\n";
		echo "code：".$bean->code."\n";
		echo "msg:".$bean->msg."\n";
	}
	/**
	 * 删除IP黑名单
	 */
	public function TestBlackIPDel(){
		echo("===================测试删除IP黑名单========================"."\n");
		$BlackIPAPI=new BlackIPAPI();
		$bean=$BlackIPAPI->BlackIPDel("a47dd09338834deda2e0e717a90e2cce", "1");
		echo "返回字符串：".$bean->jsonstr."\n";
		echo "code：".$bean->code."\n";
		echo "msg:".$bean->msg."\n";
		if($bean->code==0){
			echo("访问成功==========》发送成功："."\n");
		}else{
			echo("访问成功==========》发送失败："."\n");
		}
		echo("==========》访问失败："."\n");
		$bean=$BlackIPAPI->BlackIPDel("111", "1");
		echo "返回字符串：".$bean->jsonstr."\n";
		echo "code：".$bean->code."\n";
		echo "msg:".$bean->msg."\n";
	}
}
$test=new BlackIPAPITest();
$test->TestBlackIPSearch();
$test->TestBlackIPSave();
$test->TestBlackIPDel();
?>
