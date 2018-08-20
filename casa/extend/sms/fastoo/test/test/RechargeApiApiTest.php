<?php 
include_once '/sdk/client/HttpPostClient.php';
include_once '/sdk/api/RechargeApi.php';
include_once '/sdk/model/UserGetReturn.php';
include_once '/sdk/model/UserInfoParm.php';
/**
 * 测试--账户充值Api
 * @author lb
 * @version 1.0
 * @date 2017-08-31
 */
class RechargeApiTest {
	
	/**
	 *  账户充值Api
	 */
	public static function TestRechargeAlipay(){
		echo("==========》访问成功："."\n");
		$RechargeApi=new RechargeApi();
		$bean=$RechargeApi->RechargeAlipay("a47dd09338834deda2e0e717a90e2cce", "100");
		echo "返回字符串：".$bean->jsonstr."\n";
		echo "code：".$bean->code."\n";
		echo "msg:".$bean->msg."\n";
		echo("textHtml："+$bean->textHtml);
		echo("==========》访问失败："."\n");
		$bean=$RechargeApi->RechargeAlipay("111", "100");
		echo "返回字符串：".$bean->jsonstr."\n";
		echo "code：".$bean->code."\n";
		echo "msg:".$bean->msg."\n";
	}
	/**
	 *  账户充值记录查询Api
	 */
	public static function TestRechargeSearch(){
		echo("==========》访问成功："."\n");
		$RechargeApi=new RechargeApi();
		$bean=$RechargeApi->RechargeSearch("a47dd09338834deda2e0e717a90e2cce", "0");
		echo "返回字符串：".$bean->jsonstr."\n";
		echo "code：".$bean->code."\n";
		echo "msg:".$bean->msg."\n";
		if($bean->code==0){
			echo("访问成功==========》发送成功");
			echo("size：".$bean->size."\n");
			echo("totalPages：".$bean->totalPages."\n");
			echo("totalElements：".$bean->totalElements."\n");
			for($i=0;$i<sizeof($bean->recharges);$i++){
				$obj=$bean->recharges[$i];
				echo("createTime:".$obj->createTime."\n");
				echo("id:".$obj->id."\n");
				echo("rechargeNo:".$obj->rechargeNo."\n");
				echo("status:".$obj->status."\n");
				echo("totalPrice:".$obj->totalPrice."\n");
				echo("tradeNo:".$obj->tradeNo."\n");
			}
		}else{
			echo("访问成功==========》发送失败："."\n");
		}
		echo("==========》访问失败："."\n");
		$bean=$RechargeApi->RechargeSearch("1111", "0");
		echo "返回字符串：".$bean->jsonstr."\n";
		echo "code：".$bean->code."\n";
		echo "msg:".$bean->msg."\n";
	}
	
}
	$test=new RechargeApiTest();
	$test->TestRechargeAlipay();
	$test->TestRechargeSearch();

?>
