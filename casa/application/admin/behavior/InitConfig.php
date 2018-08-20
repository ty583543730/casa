<?php
namespace app\admin\behavior;
/**
 * 初始化基础数据
 */
class InitConfig 
{
    public function run(&$params){
        cache('AllListenUrl',model('admin/Privileges')->visitPrivilege(),86400,'Background_Authority');
    }
}