<?php

namespace app\admin\model;

use think\Db;

/**
 * 广告业务处理
 */
class ads extends Base
{
    /**
     * 分页
     */
    public function pageQuery()
    {
        $where = [];
        $where['a.dataFlag'] = 1;
        $apId = (int)input('positionId', 0);
        if ($apId != 0) $where['a.adPositionId'] = $apId;

        return $this->alias('a')
            ->join('ad_positions ap', 'a.adPositionId=ap.positionId AND ap.dataFlag=1', 'left')
            ->field('adId,adName,adFile,adURL,adStartDate,adEndDate,adClickNum,ap.positionName,a.adSort,a.createTime')
            ->where($where)->order('adId desc')
            ->order('adSort', 'asc')
            ->paginate(input('limit/d'))
            ->toArray();
    }

    public function getById($id)
    {
        $res = Db::name('ads')->alias('a')
            ->join('ad_positions p','a.adPositionId=p.positionId','left')
            ->where('adId',$id)
            ->field('adId,adPositionId,adName,positionName,positionWidth,positionHeight,adFile,adURL,adStartDate,adEndDate,adSort')
            ->find();
        return $res;
    }

    /**
     * 新增
     */
    public function add()
    {
        $data = input('post.');
        $data['createTime'] = date('Y-m-d H:i:s');
        SKUnset($data, 'adId');
        $result = $this->allowField(true)->save($data);
        if (false !== $result) {
            return SKReturn("新增成功", 1);
        }else{
            return SKReturn($this->getError());
        }
    }

    /**
     * 编辑
     */
    public function edit()
    {
        $data = input('post.');
        $result = $this->allowField(true)->save($data, ['adId' => (int)$data['adId']]);
        if (false !== $result) {
            return SKReturn("编辑成功", 1);
        } else {
            return SKReturn($this->getError());
        }
    }

    /**
     * 删除
     */
    public function del()
    {
        $id = (int)input('post.id/d');
        $result = $this->setField(['adId' => $id, 'dataFlag' => -1]);
        if (false !== $result) {
            return SKReturn("删除成功", 1);
        } else {
            return SKReturn($this->getError());
        }
    }

    /**
     * 修改广告排序
     */
    public function changeSort()
    {
        $id = (int)input('id');
        $adSort = (int)input('adSort');
        $result = $this->setField(['adId' => $id, 'adSort' => $adSort]);
        if (false !== $result) {
            return SKReturn("操作成功", 1);
        } else {
            return SKReturn($this->getError(), -1);
        }
    }

}
