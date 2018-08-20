<?php
namespace app\admin\controller;
use app\admin\model\LogOperates as M;
/**
 * 操作日志控制器
 */
class Logoperates extends Base{
	
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
    /**
     * 获取指定记录
     */
    public function get(){
    	$m = new M();
    	return $m->getById(input('id/d',0));
    }
}
