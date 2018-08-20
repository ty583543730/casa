<?php
namespace app\admin\controller;
use app\admin\model\SysConfigs as M;
/**

 * 配置控制器
 */
class Sysconfigs extends Base{
	
    public function index(){
    	$m = new M();
    	$configs = $m->getSysConfigs();
    	$this->assign("configs",$configs);
    	return $this->fetch("edit");
    }
    
    /**
     * 保存
     */
    public function edit(){
    	$m = new M();
    	return $m->edit();
    }
}
