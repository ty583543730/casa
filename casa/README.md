钱包币钱包 代码规范
===============

## 写作必读

+ 代码方法必须有注释，注释规则：功能说明，传参返回，制作人，制作时间。


+ 写作列表，详情请参照也有功能：复制，粘贴，替换相关字，相关字段名。请保持代码书写一致。

+ 写作前请先查看application/common.php和public/static/js/common.js两个公共方法。公共方法写作，请按照注释规则注释。

## php公共方法 application/common.php

+ 获取系统配置表里的数据  SysConfig("skUploads");

## js公共方法 public/static/js/common.js

```javascript
url = SK.U('admin/ads/toedit', 'id=' + id);

SK.popupRight('LAY_PopupArt', url, '600px');

SK.popupOpen('LAY_PopupArt', url, '600px');
```



## 列表制作

+ 列表制作，请参照文章功能的列表：admin/Article.php中的index方法；模版文件文件：public/templates/admin/article/index.html。

## 详情页制作

+ 详情页制作，请参照文章分类详情页：admin/ArticleCats.php中的toEdit方法；模版文件：public/templates/admin/articlecats/edit.html

+ 页面单个字段需要图片上传 请参照： public/templates/admin/ads/edit.html

+ 页面多个字段需要图片上传 请参照： public/templates/admin/sysconfigs/edit.html

+ 使用富文本框 请参照：public/templates/admin/articles/edit.html

+ 监控select框字段变化 请参照： public/templates/admin/ads/edit.html

## 后台管理 权限代码
+ 模版代码
```php
{if SKgrant('WZGL_01')}
    <button class="btn" onclick='javascript:toEdit(0)'>新增</button>
{/if}   
```
+ JS代码
```javascript
if(SK.GRANT.GGGL_02) h += "<a href='xxx.com'>修改</a> ";
```