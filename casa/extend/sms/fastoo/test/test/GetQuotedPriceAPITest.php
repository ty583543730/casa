<?php 
include_once '/sdk/client/HttpPostClient.php';
include_once '/sdk/model/QuotedPriceReturn.php';
include_once '/sdk/api/GetQuotedPriceAPI.php';
/**
 * 测试-获取短信/语音价格
 * @author lb
 * @version 1.0
 * @date 2017-08-31
 */
class GetQuotedPriceAPITest {
	public static function Test(){
		$GetQuotedPriceAPI=new GetQuotedPriceAPI();
		$bean=$GetQuotedPriceAPI->GetQuotedPrice("China");
		echo "返回字符串：".$bean->jsonstr."\n";
		echo "code：".$bean->code."\n";
		echo "msg:".$bean->msg."\n";
		if($bean->code==0){
			echo("访问成功==========》发送成功");
			echo("vtprice:".$bean->vtprice."\n");
			echo("mtprice:".$bean->mtprice."\n");
		}else{
			echo("访问成功==========》发送失败："."\n");
		}
		echo("==========》访问失败："."\n");
		$bean=$GetQuotedPriceAPI->GetQuotedPrice("China1");
		echo "返回字符串：".$bean->jsonstr."\n";
		echo "code：".$bean->code."\n";
		echo "msg:".$bean->msg."\n";
	}
}
$test=new GetQuotedPriceAPITest();
$test->Test();
?>
