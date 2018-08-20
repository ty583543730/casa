<?php
/**
 * 文章列表 详情
 * User: Administrator
 * Date: 2018/5/31 0031
 * Time: 下午 4:37
 */

namespace app\api\model;

use think\Db;

class Articles extends Base
{
    /**
     * 文章列表
     * @author: yt 2018/5/31
     * @param $catId 分类ID
     */
    public function articlesList($param)
    {
        $must = ["catId"];
        $mustParams = mustParams($must, trimArray($param));
        if (!$mustParams) {
            return SKReturn("必填参数都不能为空");
        }

        $param['page'] = !empty($param['page']) ? $param['page'] : 1;
        $param['pageSize'] = !empty($param['pageSize']) ? $param['pageSize'] : 10;

        $list = Db::name("articles")
            ->field("logo,articleId,articleTitle,num,createTime")
            ->where(['catId' => $param['catId'], 'isShow' => 1, 'dataFlag' => 1])
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
     * 文章详情
     * @author: yt  2018/5/31
     * @param $articleId 自增ID
     */
    public function articlesDetail($param)
    {
        $must = ["articleId"];
        $mustParams = mustParams($must, trimArray($param));
        if (!$mustParams) {
            return SKReturn("必填参数都不能为空");
        }
        $data = Db::name("articles")
            ->field("articleTitle,articleContent,createTime")
            ->where(['articleId' => $param['articleId']])
            ->find();
        if (!empty($data)) {
            Db::name("articles")->where(['articleId' => $param['articleId']])->setInc('num', 1);
            return SKReturn("查询成功！", 1, $data);
        } else {
            return SKReturn("暂无数据！", -8);
        }
    }

    /**
     * 主流币行情列表
     * @author: zhouying 2018/6/12
     * @param coin 币种名称
     */
    public function coinList($param)
    {
        $must = ["coin"];
        $mustParams = mustParams($must, trimArray($param));
        if (!$mustParams) {
            return SKReturn("必填参数都不能为空");
        }
        $param['coin'] = strtolower($param['coin']);
        $coinName = strtoupper($param['coin']);
        if ($coinName == 'USDT') $param['coin'] = 'usd';
        $param['page'] = !empty($param['page']) ? $param['page'] : 1;
        $param['pageSize'] = !empty($param['pageSize']) ? $param['pageSize'] : 19;
        $coin[] = [
            'symbol' => 'CASA',
            'price' => '100' . $coinName,
            'price_cny' => '1200' . ' CNY',
            'src' => ''
        ];
        $data = Db::name("coinmarketcap_price")
            ->field("symbol,price_{$param['coin']} price,price_cny")
            ->order("rank asc")
            ->paginate($param['pageSize'], false, ['page' => $param['page']])
            ->toArray();
        foreach ($data['data'] as $key => $val) {
            if ($coinName == $val['symbol']) {
                unset($data['data'][$key]);
                continue;
            }
            $data['data'][$key]['price'] = $val['price'] . $coinName;
            $data['data'][$key]['price_cny'] = $val['price_cny'] . ' CNY';
            $data['data'][$key]['src'] = BASE_URL . 'static/images/coin/' . $val['symbol'] . '.png';
        }
        if ($param['page'] == 1) $data['data'] = array_merge($coin, $data['data']);
        if (!empty($data['data'])) {
            return SKReturn("查询成功！", 1, array_values($data['data']));
        } else {
            return SKReturn("暂无数据！", -8);
        }
    }

}