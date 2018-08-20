<?php

namespace app\admin\model;

use think\Cache;
use think\Db;

/**
 * 菜单业务处理
 */
class Menus extends Base
{
    protected $insert = ['dataFlag' => 1];

    /**
     * 获取菜单列表
     */
    public function listQuery($parentId = -1)
    {
        if ($parentId == -1) return ['id' => 0, 'name' => config('mallName'), 'isParent' => true, 'open' => true];
        $rs = $this->where(['parentId' => $parentId, 'dataFlag' => 1])->field('menuId id,menuName name')->order('menuSort', 'asc')->select();
        if (count($rs) > 0) {
            foreach ($rs as $key => $v) {
                $rs[$key]['isParent'] = true;
            }
        };
        return $rs;
    }

    /**
     * 获取所有的菜单
     */
    public function getAllMenus()
    {
        $data = Db::name('menus')->where(['dataFlag' => 1])->field('menuId id,menuName name,parentId')->order('menuSort', 'asc')->select();
        return $this->getTree($data, 0);
    }

    /**
     * 迭代获取下级 得到树形数据 yt
     */
    function getTree($data, $pId)
    {
        $tree = [];
        foreach ($data as $k => $v) {
            if ($v['parentId'] == $pId) {
                $v['children'] = self::getTree($data, $v['id']);
                unset($v['parentId']);
                $tree[] = $v;
            }
        }
        return $tree;
    }

    /**
     * 获取菜单
     */
    public function getById($id)
    {
        return $this->get(['dataFlag' => 1, 'menuId' => $id]);
    }

    /**
     * 新增菜单 新增子目录
     */
    public function add()
    {
        $data = input('post.');
        if ($data['ischild'] == 1) {
            $data['parentId'] = $data['menuId'];
            unset($data['menuId']);
        }
        unset($data['ischild']);
        $result = $this->validate('Menus.add')->save($data);
        if (false !== $result) {
            Cache::clear('Background_Authority');
            return SKReturn("新增成功", 1);
        } else {
            return SKReturn($this->getError(), -1);
        }
    }

    /**
     * 编辑菜单
     */
    public function edit()
    {
        $data = input('post.');
        unset($data['ischild']);
        $result = $this->validate('Menus.edit')->allowField(['menuName', 'alias', 'menuSort'])->save($data, ['menuId' => $data['menuId']]);
        if (false !== $result) {
            Cache::clear('Background_Authority');
            return SKReturn("编辑成功", 1);
        } else {
            return SKReturn($this->getError(), -1);
        }
    }

    /**
     * 删除菜单
     */
    public function del()
    {
        $menuId = input('post.id/d');
        $data = [];
        $data['dataFlag'] = -1;
        $result = $this->update($data, ['menuId' => $menuId]);
        if (false !== $result) {
            Cache::clear('Background_Authority');
            return SKReturn("删除成功", 1);
        } else {
            return SKReturn($this->getError(), -1);
        }
    }

    /**
     * 获取用户菜单
     */
    public function getMenus()
    {
        $staff = session('sk_staff');
        $menus = cache('background_menus_'.$staff['staffId']);
        if (empty($menus)) {
            $menus = Db::name('menus')->where(['parentId' => 0, 'dataFlag' => 1, 'menuId' => ['in', $staff['menuIds']]])
                ->field('menuId,alias,menuName')
                ->order('menuSort', 'asc')
                ->select();
            foreach ($menus as $k => $v) {
                $menus[$k]['sub'] = $this->getSubMenus($v['menuId']);
            }
            cache('background_menus_' . $staff['staffId'], $menus, 86400, 'Background_Authority');
        }
        return $menus;
    }

    /**
     * 获取子菜单
     */
    public function getSubMenus($parentId)
    {
        //用户权限判断
        $STAFF = session('sk_staff');
        $rs2 = Db::name('menus')
            ->where(['parentId' => $parentId, 'dataFlag' => 1, 'menuId' => ['in', $STAFF['menuIds']]])
            ->field('menuId,menuName')
            ->order('menuSort', 'asc')
            ->select();
        foreach ($rs2 as $key2 => $v2) {
            if (!in_array($v2['menuId'], $STAFF['menuIds'])) continue;
            $rs3 = Db::name('menus')->alias('m')
                ->join('privileges p', 'm.menuId= p.menuId and isMenuPrivilege=1 and p.dataFlag=1', 'inner')
                ->where(['parentId' => $v2['menuId'], 'm.dataFlag' => 1, 'm.menuId' => ['in', $STAFF['menuIds']]])
                ->field('m.menuId,m.menuName,privilegeUrl')
                ->order('menuSort', 'asc')
                ->select();
            if (!empty($rs3)) {
                $rs2[$key2]['sub'] = $rs3;
            } else {
                $privilegeUrl = Db::name('privileges')
                    ->where(['menuId' => $v2['menuId'], 'isMenuPrivilege' => 1, 'dataFlag' => 1])
                    ->value('privilegeUrl');
                if (empty($privilegeUrl)) {
                    unset($rs2[$key2]);
                } else {
                    $rs2[$key2]['privilegeUrl'] = $privilegeUrl;
                }
            }
        }
        return $rs2;
    }
}
