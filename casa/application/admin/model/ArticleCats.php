<?php

namespace app\admin\model;

/**
 * 文章分类业务处理
 */
use think\Db;

class ArticleCats extends Base
{
    /**
     * 获取树形分类
     */
    public function pageQuery()
    {
        return $this->alias('a')
            ->join('article_cats b', 'a.parentId= b.catId', 'left')
            ->field('a.*,b.catName as parentName')
            ->where(['a.dataFlag' => 1])
            ->order('catId asc')
            ->paginate(input('limit/d'))
            ->toArray();

    }

    /**
     * 获取列表
     */
    public function listQuery($parentId)
    {
        $rs = $this->where(['dataFlag' => 1, 'parentId' => $parentId])->order('catSort asc,catName asc')->select();
        if (count($rs) > 0) {
            foreach ($rs as $key => $v) {
                $rs[$key]['childrenurl'] = url('admin/articlecats/listQuery', array('parentId' => $v['catId']));
                $rs[$key]['children'] = [];
                $rs[$key]['isextend'] = false;
            }
        }
        return $rs;
    }

    /**
     * 获取指定对象
     */
    public function getById($id)
    {
        return $this->get(['dataFlag' => 1, 'catId' => $id]);
    }

    /**
     *  获取文章分类列表
     */
    public function getAllArtCats()
    {
        return $this->where(['dataFlag' => 1, 'isShow' => 1])->field('catId,catName,parentId')->order('catId asc')->select();
    }

    /**
     * 显示是否显示/隐藏
     */
    public function editiIsShow()
    {
        $ids = array();
        $id = input('post.id/d');
        $ids = $this->getChild($id);
        $isShow = input('post.isShow/d') ? 1 : 0;
        $result = $this->where("catId in(" . implode(',', $ids) . ")")->update(['isShow' => $isShow]);
        if (false !== $result) {
            return SKReturn("操作成功", 1);
        } else {
            return SKReturn($this->getError(), -1);
        }
    }

    /**
     * 迭代获取下级
     * 获取一个分类下的所有子级分类id
     */
    public function getChild($pid = 1)
    {
        $data = $this->where("dataFlag=1")->select();
        //获取该分类id下的所有子级分类id
        $ids = $this->_getChild($data, $pid, true);//每次调用都清空一次数组
        //把自己也放进来
        array_unshift($ids, $pid);
        return $ids;
    }

    public function _getChild($data, $pid, $isClear = false)
    {
        static $ids = array();
        if ($isClear)//是否清空数组
            $ids = array();
        foreach ($data as $k => $v) {
            if ($v['parentId'] == $pid && $v['dataFlag'] == 1) {
                $ids[] = $v['catId'];//将找到的下级分类id放入静态数组
                //再找下当前id是否还存在下级id
                $this->_getChild($data, $v['catId']);
            }
        }
        return $ids;
    }

    /**
     * 新增
     */
    public function add()
    {
        $data = input('post.');
        SKUnset($data, 'catId,catType,dataFlag');
        $data['createTime'] = date('Y-m-d H:i:s');
        $result = $this->validate('ArticleCats.add')->allowField(true)->save($data);
        if (false !== $result) {
            return SKReturn("新增成功", 1);
        } else {
            return SKReturn($this->getError(), -1);
        }
    }

    /**
     * 编辑
     */
    public function edit()
    {
        $catId = input('post.catId/d');
        $data = input('post.');
        $result = $this->validate('ArticleCats.edit')->allowField(['catName', 'isShow', 'catSort', 'parentId'])->save(input('post.'), ['catId' => $catId]);
        if (false !== $result) {
            return SKReturn("修改成功", 1);
        } else {
            return SKReturn($this->getError(), -1);
        }
    }

    /**
     * 删除
     */
    public function del()
    {
        $ids = array();
        $id = input('post.id/d');
        $ids = $this->getChild($id);
        $data = [];
        $data['dataFlag'] = -1;
        Db::startTrans();
        try {
            $result = $this->where("catId in(" . implode(',', $ids) . ")")->update($data);
            if (false !== $result) {
                Db::name('articles')->where(['catId' => ['in', $ids]])->update(['dataFlag' => -1]);
            }
            Db::commit();
            return SKReturn("删除成功", 1);
        } catch (\Exception $e) {
            Db::rollback();
            return SKReturn('删除失败', -1);
        }
    }
}