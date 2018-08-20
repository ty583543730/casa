<?php

namespace app\admin\model;

/**
 * 系统配置
 */
use think\Db;

class SysConfigs extends Base
{
    /**
     * 获取配置
     */
    public function getSysConfigs()
    {
        $configs = Db::name('sys_configs')->select();
        $data = [];
        foreach ($configs as $config) {
            if(!empty($config['optionValue'])){
                $config['optionValue'] =json_decode( $config['optionValue'],JSON_UNESCAPED_UNICODE);
            }
            if (isset($data[$config['belong']])) {
                $data[$config['belong']][] = $config;
            } else {
                $data[$config['belong']] = [];
                $data[$config['belong']][] = $config;
            }
        }
        return $data;
    }

    /**
     * 编辑
     */
    public function edit($isRequire = false)
    {
        $list = $this->field('configId,fieldCode,fieldValue')->select();
        Db::startTrans();
        try {
            foreach ($list as $key => $v) {
                $code = trim($v['fieldCode']);
                $val = Input('post.' . trim($v['fieldCode']));
                if ($isRequire && $val == '') continue;
                $this->update(['fieldValue' => $val], ['fieldCode' => $code]);
            }
            Db::commit();
            cache('SysConfig', null);
            return SKReturn("操作成功", 1);
        } catch (\Exception $e) {
            Db::rollback();
            return SKReturn($e->getMessage());
        }
    }
}
