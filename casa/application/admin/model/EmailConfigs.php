<?php
/**
 * 邮件配置
 * User: sky
 * Date: 2018/6/20
 * Time: 14:25
 */

namespace app\admin\model;

use think\Db;

class EmailConfigs extends Base
{
    /**
     * 获取树形分类
     */
    public function pageQuery()
    {
        return $this->alias('a')
            ->field('a.*')
            ->where(['a.dataFlag' => 1])
            ->order('id asc')
            ->paginate(input('limit/d'))
            ->toArray();

    }

    /**
     * 获取指定对象
     */
    public function getById($id)
    {
        return $this->where(['dataFlag' => 1, 'id' => $id])->field('*')->find();
    }

    /**
     * 新增
     */
    public function add()
    {
        $data = input('post.');
        SKUnset($data, 'id,dataFlag');
        $data['createTime'] = date('Y-m-d H:i:s');
        $result             = $this->validate('Emailconfigs.add')->allowField(true)->save($data);
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
        $id         = input('post.id/d');
        $allowField = ['mailSmtp', 'mailPort', 'mailAuth', 'mailAddress', 'mailUserName', 'mailPassword', 'mailSendTitle', 'useTime'];
        $result     = $this->validate('Emailconfigs.edit')->allowField($allowField)->save(input('post.'), ['id' => $id]);
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
        $id               = input('post.id/d');
        $data             = [];
        $data['dataFlag'] = -1;
        try {
            DB::name('email_configs')->where(['id' => $id])->update($data);
            return SKReturn("删除成功", 1);
        } catch (\Exception $e) {
            return SKReturn('删除失败', -1);
        }
    }
}