<?php
namespace app\admin\model;
use think\Db;
/**
 * 广告位置业务处理
 */
class AdPositions extends Base{

	/**
	 * 分页
	 */
	public function pageQuery(){
		return $this->where('dataFlag',1)->field(true)->order('apSort asc,positionId asc')->paginate(input('limit/d'))->toArray();
	}

	/**
     * 获取所有广告位
	*/
    public function getAllAdPositions()
    {
        return Db::name('ad_positions')->where('dataFlag',1)->field('positionId,positionName')->order('apSort asc,positionId asc')->select();
	}

	public function getById($id){
		return $this->get(['positionId'=>$id,'dataFlag'=>1]);
	}
	/**
	 * 新增
	 */
	public function add(){
		$data = input('post.');
		SKUnset($data,'positionId');
		$result = $this->validate('AdPositions.add')->allowField(true)->save($data);
        if(false !== $result){
        	return SKReturn("新增成功", 1);
        }else{
        	return SKReturn($this->getError(),-1);
        }
	}
    /**
	 * 编辑
	 */
	public function edit(){
		$Id = (int)input('post.positionId');
	    $result = $this->validate('AdPositions.edit')->allowField(true)->save(input('post.'),['positionId'=>$Id]);
        if(false !== $result){
        	return SKReturn("编辑成功", 1);
        }else{
        	return SKReturn($this->getError(),-1);
        }
	}
	/**
	 * 删除
	 */
    public function del(){
	    $id = (int)input('post.id/d');
	    $result = $this->setField(['positionId'=>$id,'dataFlag'=>-1]);
        if(false !== $result){
        	return SKReturn("删除成功", 1);
        }else{
        	return SKReturn($this->getError(),-1);
        }
	}
}
