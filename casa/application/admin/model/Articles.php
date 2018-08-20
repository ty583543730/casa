<?php

namespace app\admin\model;

use think\Db;
use think\Loader;

/**
 * 文章业务处理
 */
class Articles extends Base
{
    /**
     * 分页
     */
    public function pageQuery()
    {
        $where = [];
        $where['a.dataFlag'] = 1;

        $catId = (int)input('get.catId', 0);
        if ($catId != 0) $where['a.catId'] = $catId;

        $key = trim(input('get.key'));
        if ($key != '') $where['a.articleTitle'] = ['like', '%' . $key.'%'];

        if (timeTerm(input('startDate'), input('endDate')) != false) {
            $where['a.createTime'] = timeTerm(input('startDate'), input('endDate'));
        }
        $page = Db::name('articles')->alias('a')
            ->join('article_cats ac', 'a.catId= ac.catId', 'left')
            ->join('staffs s', 'a.staffId= s.staffId', 'left')
            ->where($where)
            ->field('a.articleId,a.catId,a.articleTitle,a.articleTitleEn,a.isShow,a.createTime,ac.catName,s.staffName')
            ->order('a.articleId', 'desc')
            ->paginate((input('limit/d')))
            ->toArray();
        return $page;
    }

    /**
     * 获取指定对象
     */
    public function getById($id)
    {
        $single = $this->where(['articleId' => $id, 'dataFlag' => 1])->find();
        $singlec = Db::name('article_cats')->where(['catId' => $single['catId'], 'dataFlag' => 1])->field('catName')->find();
        if(!empty($single['catName']))$single['catName'] = $singlec['catName'];
        if(!empty($single['articleContent']))$single['articleContent'] = htmlspecialchars_decode(($single['articleContent']));
        return $single;
    }

    /**
     * 新增
     */
    public function add()
    {
        $data = input('post.');
        SKUnset($data, 'id,file');
        $data["staffId"] = (int)session('sk_staff.staffId');
        $data['createTime'] = date('Y-m-d H:i:s');
        if (empty($data['logo']))
        $data['logo'] = $this->getImgFromArticle(htmlspecialchars_decode($data['articleContent']), 0);
        $result = $this->validate('Articles.add')->allowField(true)->save($data);
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
        $articleId = input('post.id/d');
        $data = input('post.');
        SKUnset($data, 'id,file');
        $data["staffId"] = (int)session('sk_staff.staffId');
        if (empty($data['logo']))
        $data['logo'] = $this->getImgFromArticle(htmlspecialchars_decode($data['articleContent']), 0);
        $validate = Loader::validate("articles", "validate", false, "common");
        $result = $validate->scene('edit')->check($data);
        if (!$result) {
            return SKReturn($validate->getError());
        }
        $result = $this->save($data, ['articleId' => $articleId]);
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
        $id = input('post.id/d');
        $data = [];
        $data['dataFlag'] = -1;
        $result = $this->where(['articleId' => $id])->update($data);
        if (false !== $result) {
            return SKReturn("删除成功", 1);
        } else {
            return SKReturn($this->getError(), -1);
        }
    }

    /**
     * 从内容中抓取图片
     * @param $content
     * @param string $order 全部图片
     * @return string
     */
    public function getImgFromArticle($content, $order = 'ALL')
    {
        $pattern = "/<img.*?src=[\'|\"](.*?(?:[\.gif | \.jpg| \.png]))[\'|\"].*?[\/]?>/";
        preg_match_all($pattern, $content, $match);
        if (isset($match[1]) && !empty($match[1])) {
            if ($order === 'ALL') {
                return $match[1];
            }
            if (is_numeric($order) && isset($match[1][$order])) {
                return $match[1][$order];
            }
        }
        //如果没有 则显示默认图片
        return "article/6c44caa050ba094cac5a6d84b4939d6f.jpg";
    }
}