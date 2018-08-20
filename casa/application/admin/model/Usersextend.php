<?php
/**
 * 用户扩展
 * User: tuyi
 * Date: 2018/8/13
 */

namespace app\admin\model;

use think\Db;
use think\Exception;
use think\Log;

class Usersextend extends Base
{
    /**
     * 用户扩展
     * @return array
     * @author tuyi
     * Date: 2018/8/13
     */
    public function pageQuery()
    {
        $param = array_filter(trimArray(input('get.')));
        $where = [];
        $pageSize = isset($param['limit']) ? $param['limit'] : 10;
        if (!empty($param['userId'])) {
            $where['userId'] = $param['userId'];
        }
        $page = Db::name('users_extend')
            ->field('id,userId,num,binding,totalNum,totalBinding,recommendNum,computingPower')
            ->where($where)
            ->order('id asc')
            ->paginate($pageSize)
            ->toArray();
        return $page;
    }

    /**
     * 手动修改积分
     * User: tuyi
     */
    public function changeScore()
    {
        $param = input('post.');
        $must = ['userPhone', 'score', 'operate', 'num'];
        if (!mustParams($must, $param)) {
            return SKReturn("必填参数不能为空");
        }
        $user = Db::name('users')->field('userId')->where('userPhone', '=', $param['userPhone'])->find();
        $userId = $user['userId'];
        $data = Db::name('users_extend')->field('num,binding')->where("userId=$userId")->find();
        if (empty($userId)) {
            return SKReturn('不存在此用户!');
        }

        //1表示增加
        if ($param['operate'] == 1) {
            $operate = '+';
            $insert['type'] = 6;
            $insert['num'] = $param['num'];
            $insert['binding'] = $param['num'];
        } else if ($param['operate'] == 2) {
            $operate = '-';
            $insert['type'] = 7;
            $insert['num'] = $param['num'] * -1;
            $insert['binding'] = $param['num'] * -1;
        }

        switch ($param['score']) {
            case 1://操作可用积分
                $update['num'] = Db::raw('num' . $operate . $param['num']);
                $update['totalNum'] = Db::raw('totalNum' . $operate . $param['num']);
                $insert['binding'] = 0;
                $insert['remark'] = '手动修改可用积分';
                break;
            case 2://操作绑定积分
                $update['binding'] = Db::raw('binding' . $operate . $param['num']);
                $update['totalBinding'] = Db::raw('totalBinding' . $operate . $param['num']);
                $insert['num'] = 0;
                $insert['remark'] = '手动修改绑定积分';
                break;
        }
        $insert['orderNo'] = SKorderSn('s');
        $insert['userId'] = $userId;
        $insert['preNum'] = $data['num'];
        $insert['preBinding'] = $data['binding'];
        $insert['dataFlag'] = 1;
        $insert['createTime'] = date('Y-m-d H:i:s');

        Db::startTrans();
        try {
            $score_res = Db::name('users_extend')->where(['userId' => $userId])->update($update);
            $log_res = Db::name('log_score')->insert($insert);
            if ($score_res > 0 && $log_res > 0) {
                Db::commit();
                return SKReturn('修改成功!', 1);
            } else {
                Log::logger('Usersextend', '用户id：' . $userId . '积分手动修改失败', 'Usersextend');
                return SKReturn('修改失败!');
            }
        } catch (Exception $e) {
            Db::rollback();
            Log::logger('Usersextend', '用户id：' . $userId . '积分手动修改失败' . ' 失败原因：' . $e->getMessage(), 'Usersextend');
            return SKReturn('修改失败!');
        }
    }

    /**
     * 检测用户是否存在
     * User: tuyi
     */
    public function checkUser()
    {
        $param = input('post.');
        if (empty($param['userPhone'])) {
            return SKReturn('手机号长度必须为11位');
        }
        if (!empty($param['userPhone']) && strlen($param['userPhone']) == "11") {
            $n = preg_match_all("/13[123569]{1}\d{8}|15[1235689]\d{8}|188\d{8}/", $param['userPhone'], $array);
            if ($n === false && $n == 0) {
                return SKReturn('请填写正确的手机号!');
            }
        } else {
            return SKReturn('手机号长度必须为11位!');
        }
        $user = Db::name('users')->where(['userPhone' => $param['userPhone']])->field('userPhone')->find();
        if (empty($user)) {
            return SKReturn('不存在此用户!');
        }
        return SKReturn('校验通过!', 1);
    }

}