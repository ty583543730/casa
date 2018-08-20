<?php
namespace app\admin\controller;
use app\admin\model\LogStaffLogins as M;
/**
 * 登录日志控制器
 */
class Logstafflogins extends Base{
	
    public function index(){
    	return $this->fetch("index");
    }
    
    /**
     * 获取分页
     */
    public function pageQuery(){
    	$m = new M();
    	return PQReturn($m->pageQuery());
    }
}
