<?php
namespace app\admin\model;
use think\Cache;
use think\Db;

/**

 * 角色志业务处理
 */
class Roles extends Base{
	/**
	 * 分页
	 */
	public function pageQuery(){
		return Db::name('roles')->where('dataFlag',1)->field('roleId,roleName,roleDesc')->paginate(input('limit/d'))->toArray();
	}
	/**
	 * 列表
	 */
	public function listQuery(){
		return $this->where('dataFlag',1)->field('roleId,roleName,roleDesc')->select();
	}
	/**
	 * 删除
	 */
    public function del(){
	    $id = input('post.id/d');
		$data = [];
		$data['dataFlag'] = -1;
	    $result = $this->update($data,['roleId'=>$id]);
        if(false !== $result){
            Cache::clear('Background_Authority');
        	return SKReturn("删除成功", 1);
        }else{
        	return SKReturn($this->getError(),-1);
        }
	}
	
	/**
	 * 获取角色权限
	 */
	public function getById($id){
		return $this->get(['dataFlag'=>1,'roleId'=>$id]);
	}
	
	/**
	 * 新增
	 */
	public function add(){
	    $data = input('post.');
	    $data['createTime'] = date("Y-m-d H:i:s");
		$result = $this->validate('Roles.add')->allowField(true)->save($data);
        if(false !== $result){
            Cache::clear('Background_Authority');
        	return SKReturn("新增成功", 1);
        }else{
        	return SKReturn($this->getError(),-1);
        }
	}
    /**
	 * 编辑
	 */
	public function edit(){
		$id = input('post.roleId/d');
	    $result = $this->validate('Roles.edit')->allowField(true)->save(input('post.'),['roleId'=>$id]);
        if(false !== $result){
            $staffRoleId = (int)session('sk_staff.staffRoleId');
        	if($id==$staffRoleId){
        		$STAFF = session('sk_staff');
        		$STAFF['privileges'] = explode(',',input('post.privileges'));
        		$STAFF['roleName'] = Input('post.roleName');
                session('sk_staff',$STAFF);
        	}
            Cache::clear('Background_Authority');
        	return SKReturn("编辑成功", 1);
        }else{
        	return SKReturn($this->getError(),-1);
        }
	}
	
}
