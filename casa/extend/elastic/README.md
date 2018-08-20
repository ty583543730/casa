常用方法
===============
谁用到了，麻烦更新一下方法 yt

## 单条查询条件查询

```
use elastic\Native;

$es =new Native();

$res=$es->find('log','operates','staffId',100);

```

## 多条查询条件查询

```
use elastic\Native;

$data = [
    'must' => [
         ['term' => ['staffId' => 100]],
         ['range' => ['operateTime'=>["lt"=> "2018-06-07 00:00:00"]]]
    ]
];

$es =new Native();

$res=$es->find('log','operates','staffId',100);

```
