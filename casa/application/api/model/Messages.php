<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/5/31 0031
 * Time: 下午 2:36
 */

namespace app\api\model;

use think\Db;
use think\Log;
class Messages extends Base
{
    /**
     * 用户消息列表
     * @param $userId 用户Id
     */
    public function messageList($param)
    {
        $param['userId'] = userId();
        $param['page'] = !empty($param['page']) ? $param['page'] : 1;
        $param['pageSize'] = !empty($param['pageSize']) ? $param['pageSize'] : 10;
        $list = Db::name("sys_msgs")
            ->where(['userId' => $param['userId'], 'dataFlag' => 1])
            ->order("createTime desc")
            ->paginate($param['pageSize'], false, ['page' => $param['page']])
            ->toArray();
        if (!empty($list['data'])) {
            return SKReturn("查询成功！", 1, $list['data']);
        } else {
            return SKReturn("暂无数据！", -8);
        }
    }

    /**
     * 获取某一条消息详情
     * @param $userId  用户Id
     * @param $id  消息id
     */
    public function messageDetail($param)
    {
        $must = ["id"];
        $mustParams = mustParams($must, trimArray($param));
        if (!$mustParams) {
            return SKReturn("必填参数都不能为空");
        }
        $param['userId'] = userId();
        $data = Db::name("sys_msgs")
            ->field("msgStatus,msgTitle,msgContent,createTime")
            ->where(['id' => $param['id'], 'userId' => $param['userId']])
            ->find();
        if (!empty($data) && $data['msgStatus'] == 0) {
            Db::name("sys_msgs")->where('id', $param['id'])->setField('msgStatus', 1);
        }
        if (!empty($data)) {
            return SKReturn("查询成功！", 1, $data);
        } else {
            return SKReturn("暂无数据！", -8);
        }
    }
}