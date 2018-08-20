<?php 
include_once '/sdk/client/HttpPostClient.php';
include_once '/sdk/api/UserManageApi.php';
include_once '/sdk/model/UserGetReturn.php';
include_once '/sdk/model/UserInfoParm.php';
/**
 * 测试--获取账号Api
 * @author lb
 * @version 1.0
 * @date 2017-08-31
 */
class UserManageApiTest {
	/**
	 * 用户-获取用户信息Api
	 */
	public static function TestGetUser(){
		echo("==========》访问成功："."\n");
		$UserManageApi=new UserManageApi();
		$bean=$UserManageApi->GetUser("13023813552","123456");
		echo "返回字符串：".$bean->jsonstr."\n";
		echo "code：".$bean->code."\n";
		echo "msg:".$bean->msg."\n";
		if($bean->code==0){
			echo("访问成功==========》发送成功");
			for($i=0;$i<sizeof($bean->apiKeyList);$i++){
				echo("详细数据：".$i."\n");
				echo("apiKey:".$bean->apiKeyList[$i]."\n");
			}
		}
		echo("==========》访问失败："."\n");
		$bean=$UserManageApi->GetUser("13023813552","1234567");
		echo "返回字符串：".$bean->jsonstr."\n";
		echo "code：".$bean->code."\n";
		echo "msg:".$bean->msg."\n";
	}
	/**
	 * 调用获取账号Api
	 */
	public static function TestGetUserAccounts(){
		echo("==========》访问成功："."\n");
		$UserManageApi=new UserManageApi();
		$bean=$UserManageApi->GetUserAccounts("a47dd09338834deda2e0e717a90e2cce");
		echo "返回字符串：".$bean->jsonstr."\n";
		echo "code：".$bean->code."\n";
		echo "msg:".$bean->msg."\n";
		if($bean->code==0){
			echo("访问成功==========》发送成功");
			for($i=0;$i<sizeof($bean->userAccounts);$i++){
				echo("apiKey:".$bean->userAccounts[$i]."\n");
			}
		}
		echo("==========》访问失败："."\n");
		$bean=$UserManageApi->GetUserAccounts("111");
		echo "返回字符串：".$bean->jsonstr."\n";
		echo "code：".$bean->code."\n";
		echo "msg:".$bean->msg."\n";
	}
	/**
	 *  查询余额Api
	 */
	public static function TestGetUserBalance(){
		echo("==========》访问成功："."\n");
		$UserManageApi=new UserManageApi();
		$bean=$UserManageApi->GetUserBalance("a47dd09338834deda2e0e717a90e2cce");
		echo "返回字符串：".$bean->jsonstr."\n";
		echo "code：".$bean->code."\n";
		echo "msg:".$bean->msg."\n";
		echo("余额："+$bean->balance."\n");
		echo("==========》访问失败："."\n");
		$bean=$UserManageApi->GetUserBalance("111");
		echo "返回字符串：".$bean->jsonstr."\n";
		echo "code：".$bean->code."\n";
		echo "msg:".$bean->msg."\n";
	}
	/**
	 *  账户充值Api
	 */
	public static function TestRechargeAlipay(){
		echo("==========》访问成功："."\n");
		$UserManageApi=new UserManageApi();
		$bean=$UserManageApi->RechargeAlipay("a47dd09338834deda2e0e717a90e2cce", "100");
		echo "返回字符串：".$bean->jsonstr."\n";
		echo "code：".$bean->code."\n";
		echo "msg:".$bean->msg."\n";
		echo("textHtml："+$bean->textHtml);
		echo("==========》访问失败："."\n");
		$bean=$UserManageApi->RechargeAlipay("111", "100");
		echo "返回字符串：".$bean->jsonstr."\n";
		echo "code：".$bean->code."\n";
		echo "msg:".$bean->msg."\n";
	}
	/**
	 *  账户充值Api
	 */
	public static function TestRechargeSearch(){
		echo("==========》访问成功："."\n");
		$UserManageApi=new UserManageApi();
		$bean=$UserManageApi->RechargeSearch("a47dd09338834deda2e0e717a90e2cce", "0");
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
		$bean=$UserManageApi->RechargeSearch("1111", "0");
		echo "返回字符串：".$bean->jsonstr."\n";
		echo "code：".$bean->code."\n";
		echo "msg:".$bean->msg."\n";
	}
	/**
	 * 获取余额提醒
	 */
	public static function TestGetBalanceRemindInfo(){
		echo("==========》访问成功："."\n");
		$UserManageApi=new UserManageApi();
		$bean=$UserManageApi->GetBalanceRemindInfo("a47dd09338834deda2e0e717a90e2cce");
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
		$bean=$UserManageApi->GetBalanceRemindInfo("111");
		echo "返回字符串：".$bean->jsonstr."\n";
		echo "code：".$bean->code."\n";
		echo "msg:".$bean->msg."\n";
	}
	/**
	 *设置余额提醒
	 */
	public static function TestSetBalanceRemind(){
		echo("==========》访问成功："."\n");
		$UserManageApi=new UserManageApi();
		$bean=$UserManageApi->SetBalanceRemind("a47dd09338834deda2e0e717a90e2cce","100", "12:00", "20:00");
		echo "返回字符串：".$bean->jsonstr."\n";
		echo "code：".$bean->code."\n";
		echo "msg:".$bean->msg."\n";
		echo("==========》访问失败："."\n");
		$bean=$UserManageApi->SetBalanceRemind("111","100", "12:00", "20:00");
		echo "返回字符串：".$bean->jsonstr."\n";
		echo "code：".$bean->code."\n";
		echo "msg:".$bean->msg."\n";
	}
	/**
	 * 获取账号信息Api
	 */
	public static function TestGetUserInfo(){
		echo("==========》访问成功："."\n");
		$UserManageApi=new UserManageApi();
		$bean=$UserManageApi->GetUserInfo("a47dd09338834deda2e0e717a90e2cce");
		echo "返回字符串：".$bean->jsonstr."\n";
		echo "code：".$bean->code."\n";
		echo "msg:".$bean->msg."\n";
		echo("返回数据："."\n");
		echo("balance：".$bean->balance);
		echo("businessLicence：".$bean->businessLicence);
		echo("businessLicenceImg：".$bean->businessLicenceImg);
		echo("company：".$bean->company);
		echo("idcard：".$bean->idcard);
		
		echo("industry：".$bean->industry);
		echo("ipEnable：".$bean->ipEnable);
		echo("loginName：".$bean->loginName);
		echo("realName：".$bean->realName);
		echo("userType：".$bean->userType);
		echo("==========》访问失败："."\n");
		$bean=$UserManageApi->GetUserInfo("111");
		echo "返回字符串：".$bean->jsonstr."\n";
		echo "code：".$bean->code."\n";
		echo "msg:".$bean->msg."\n";
	}
	/**
	 * 设置账号信息
	 */
	public static function TestUpdateUserInfo(){
		echo("==========》访问成功："."\n");
		$parm=new UserInfoParm("a47dd09338834deda2e0e717a90e2cce", "1", "daniel.company", "edu", "445381600190918",
				"ba781dc817a8cb3092c57b1e9201ad8a5ca89a57.png", "张三", "34759837573322");
		$UserManageApi=new UserManageApi();
		$bean=$UserManageApi->UpdateUserInfo($parm);
		echo "返回字符串：".$bean->jsonstr."\n";
		echo "code：".$bean->code."\n";
		echo "msg:".$bean->msg."\n";
		echo("==========》访问失败："."\n");
		$parm=new UserInfoParm("1111", "1", "daniel.company", "edu", "445381600190918",
			"ba781dc817a8cb3092c57b1e9201ad8a5ca89a57.png", "张三", "34759837573322");
		$bean=$UserManageApi->UpdateUserInfo($parm);
		echo "返回字符串：".$bean->jsonstr."\n";
		echo "code：".$bean->code."\n";
		echo "msg:".$bean->msg."\n";
	}
	/**
	 *修改密码
	 */
	public static function TestChangePWD(){
		echo("==========》访问成功："."\n");
		$UserManageApi=new UserManageApi();
		$bean=$UserManageApi->ChangePWD("a47dd09338834deda2e0e717a90e2cce","123456", "asdfgh");
		echo "返回字符串：".$bean->jsonstr."\n";
		echo "code：".$bean->code."\n";
		echo "msg:".$bean->msg."\n";
		echo("==========》访问失败："."\n");
		$bean=$UserManageApi->ChangePWD("1111","123456", "asdfgh");
		echo "返回字符串：".$bean->jsonstr."\n";
		echo "code：".$bean->code."\n";
		echo "msg:".$bean->msg."\n";
	}
}
	$test=new UserManageApiTest();
	$test->TestGetUser();
	$test->TestGetUserAccounts();
	$test->TestGetUserBalance();
	$test->TestRechargeAlipay();
	$test->TestRechargeSearch();
	$test->TestGetBalanceRemindInfo();
   $test->TestSetBalanceRemind();
   $test->TestGetUserInfo();
	$test->TestUpdateUserInfo();
	$test->TestChangePWD();

?>
