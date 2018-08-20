<?php
/**
 * 邮件短信发送记录
 * User: sky
 * Date: 2018/6/20
 * Time: 16:12
 */

namespace app\admin\model;

use think\Db;

class EmailSms extends Base
{
    /**
     * 列表分页
     * @return array
     */
    public function pageQuery()
    {

        $where = [];

        $type = input('get.type/d', 0);
        if ($type !== 0) {
            $where['es.type'] = $type;
        }
        $to = trim(input('get.to'));
        if ($to != '') $where['es.to'] = ['like', '%' . $to . '%'];

        $res = $this->alias('es')
            //  ->join('users u', 'es.userId = u.userId', 'left')
            ->field('es.type,es.to,es.userId,es.content,es.returnCode,es.returnMsg,es.ip,es.createTime')
            ->where($where)
            ->order('es.id desc')
            ->paginate(input('limit/d'))
            ->toArray();
        return $res;
    }
}