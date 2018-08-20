<?php

namespace app\admin\model;

use think\Cache;
use think\Db;
use think\Exception;

/**
 * 权限业务处理
 */
class Privileges extends Base
{
    /**
     * 加载指定菜单的权限
     */
    public function listQuery($parentId)
    {
        return $this->where(['menuId' => $parentId, 'dataFlag' => 1])->order('privilegeId', 'asc')->select();
    }

    /**
     * 获取指定权限
     */
    public function getById($id)
    {
        return $this->get(['privilegeId' => $id, 'dataFlag' => 1]);
    }

    /**
     * 新增
     */
    public function add()
    {
        try{
            $result = $this->validate('Privileges.add')->allowField(true)->save(input('post.'));
            if (false !== $result) {
                Cache::clear('Background_Authority');
                return SKReturn("新增成功", 1);
            } else {
                return SKReturn($this->getError(), -1);
            }
        }catch (Exception $e){
            return SKReturn($e->getMessage());
        }
    }

    /**
     * 编辑
     */
    public function edit()
    {
        try{
            $id = input('post.id/d');
            $result = $this->validate('Privileges.edit')->allowField(true)->save(input('post.'), ['privilegeId' => $id]);
            if (false !== $result) {
                Cache::clear('Background_Authority');
                return SKReturn("编辑成功", 1);
            } else {
                return SKReturn($this->getError(), -1);
            }
        }catch (Exception $e){
            return SKReturn($e->getMessage());
        }
    }

    /**
     * 删除
     */
    public function del()
    {
        $id = input('post.id/d');
        $data = [];
        $data['dataFlag'] = -1;
        $result = $this->update($data, ['privilegeId' => $id]);
        if (false !== $result) {
            Cache::clear('Background_Authority');
            return SKReturn("删除成功", 1);
        } else {
            return SKReturn($this->getError(), -1);
        }
    }

    /**
     * 检测权限代码是否存在
     */
    public function checkPrivilegeCode()
    {
        $code = input('code');
        if ($code == '') return SKReturn("", 1);
        $rs = $this->where(['privilegeCode' => $code, 'dataFlag' => 1])->Count();
        if ($rs == 0) return SKReturn("", 1);
        return SKReturn("该权限代码已存在!", -1);
    }

    /**
     * 加载权限并且标用户的权限
     */
    public function listQueryByRole($id)
    {
        $mrs = Db::name('menus')->alias('m')
            ->join('privileges p', 'm.menuId= p.menuId and isMenuPrivilege=1 and p.dataFlag=1', 'left')
            ->where(['parentId' => $id, 'm.dataFlag' => 1])
            ->field('m.menuId id,m.menuName name,p.privilegeCode,1 as isParent')
            ->order('menuSort', 'asc')
            ->select();
        $prs = $this->where(['dataFlag' => 1, 'menuId' => $id])->field('privilegeId id,privilegeName name,privilegeCode,0 as isParent')->select();
        if ($mrs) {
            if ($prs) {
                foreach ($prs as $v) {
                    array_unshift($mrs, $v);
                }
            }
        } else {
            if ($prs) $mrs = $prs;
        }
        if (!$mrs) return [];
        $privileges = session('sk_staff.privileges');
        if (count($privileges) > 0) {
            foreach ($mrs as $key => $v) {
                if ($v['isParent'] == 1) {
                    $mrs[$key]['isParent'] = true;
                    $mrs[$key]['open'] = true;
                } else {
                    $mrs[$key]['id'] = 'p' . $v['id'];
                }
            }
        }
        return $mrs;
    }

    /**
     * 加载全部权限
     */
    public function getAllPrivileges()
    {
        return $this->alias('a')
            ->join('menus b','a.menuId=b.menuId')
            ->where(['a.dataFlag' => 1])
            ->field('a.menuId,a.privilegeName,a.privilegeCode,a.privilegeUrl,a.otherPrivilegeUrl,b.menuName')
            ->select();
    }

    /**
     * 加载系统访问路径
     */
    public function visitPrivilege()
    {
        $list = $this->getAllPrivileges();
        $listenUrl = [];
        foreach ($list as $v) {
            if ($v['privilegeUrl'] == '') continue;
            $listenUrl[strtolower($v['privilegeUrl'])] = ['code' => $v['privilegeCode'],
                'url' => $v['privilegeUrl'],
                'name' => $v['privilegeName'],
                'isParent' => true,
                'menuId' => $v['menuId'],
                'menuName' => $v['menuName']
            ];
            if (strpos($v['otherPrivilegeUrl'], '/') !== false) {
                $t = explode(',', $v['otherPrivilegeUrl']);
                foreach ($t as $vv) {
                    if (strpos($vv, '/') !== false) {
                        $listenUrl[strtolower($vv)] = ['code' => $v['privilegeCode'],
                            'url' => $vv,
                            'name' => $v['privilegeName'],
                            'isParent' => false,
                            'menuId' => $v['menuId'],
                            'menuName' => $v['menuName']
                        ];
                    }
                }
            }
        }
        return $listenUrl;
    }
}
