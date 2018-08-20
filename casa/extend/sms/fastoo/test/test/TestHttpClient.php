<?php
include '/sdk/client/HttpPostClient.php';
function testPost(){
	$client=new HttpPostClient();
	$post_data=array(
			"apiKey"=>"a47dd09338834deda2e0e717a90e2cce"
	);
	$client->SendPost("http://120.26.10.238:12020/v1/admin/getUserAccounts.json",$post_data);
}
testPost();

?>