<?php
/**
 * ElasticSearch的接口文件
 * User: yt
 * Date: 2018/6/6 0006
 * Time: 下午 6:40
 */

namespace elastic;

use think\Db;

class Native
{
    private $es;

    /**
     * 初始化
     */
    public function __construct()
    {
        Vendor('elasticsearch.autoload');
        //host数组可配置多个节点
        $params = array(
            '127.0.0.1:9200'
        );
        $this->es = \Elasticsearch\ClientBuilder::create()->setHosts($params)->build();
    }
    /**
     * 查询总接口（普通查询或者聚合查询 安装elasticsearch的json变成数组传递）
     */
    public function search($index, $type, $body)
    {
        $params = [
            'index' => $index,
            'type' => $type,
            'body' => $body
        ];
        return $this->es->search($params);
    }
    /**
     * 多个条件查询匹配（bool查询）
     * 搜索条件：must = and must_not= not should=or
     * $data =[
     *      'should' => [
     *                      ['term'=>['age'=>19]],
     *                      ['term'=>['name'=>'xiaoxi']],
     *                  ],
     *      'must' => [
     *                      ['term'=>['age'=>19]],
     *                      ['term'=>['name'=>'xiaoxi']],
     *                  ],
     * ]
     * 排序：$sort=['price'=>['orders'=>'desc'],'id'=>['orders'=>'asc']] 其中orders是固定的
     * 返回显示的字段：$columns =['name','age']
     */
    public function select($index, $type, $data, $sort = false, $columns = false, $start = 0, $limit = 10)
    {
        $params = [
            'index' => $index,
            'type' => $type,
            'body' => [
                'query' => [
                    'bool' => $data
                ],
                'from' => $start,
                'size' => $limit
            ],
        ];
        if ($columns != false) {
            $params['body']['_source'] = $columns;
        }
        if ($sort != false) {
            $params['body']['sort'] = $sort;
        }
        return $this->es->search($params);
    }

    /**
     * 单个条件查询匹配
     * 排序：$sort=['price'=>['orders'=>'desc'],'id'=>['orders'=>'asc']] 其中orders是固定的
     * 返回显示的字段：$columns =['name','age']
     */
    public function find($index, $type, $field, $value, $sort = false, $columns = false, $start = 0, $limit = 10)
    {
        $params = [
            'index' => $index,
            'type' => $type,
            'body' => [
                'query' => [
                    'constant_score' => [ //非评分模式执行
                        'filter' => [ //过滤器，不会计算相关度，速度快
                            'term' => [ //精确查找，不支持多个条件
                                $field => $value
                            ]
                        ]
                    ]
                ],
                'from' => $start, //从第几条开始查询
                'size' => $limit //返回结果数量 limit
            ]
        ];
        if ($columns != false) {
            $params['body']['_source'] = $columns;
        }
        if ($sort != false) {
            $params['body']['sort'] = $sort;
        }
        return $this->es->search($params);
    }

    /**
     * 单个条件多值查询匹配（类似mysql的in）
     * 排序：$sort=['price'=>['orders'=>'desc'],'id'=>['orders'=>'asc']] 其中orders是固定的
     * 返回显示的字段：$columns =['name','age']
     */
    public function findin($index, $type, $field, $values, $sort = false, $columns = false, $start = 0, $limit = 10)
    {
        $params = [
            'index' => $index,
            'type' => $type,
            'body' => [
                'query' => [
                    'constant_score' => [ //非评分模式执行
                        'filter' => [ //过滤器，不会计算相关度，速度快
                            'terms' => [
                                $field => $values
                            ]
                        ]
                    ]
                ],
                'from' => $start, //从第几条开始查询
                'size' => $limit //返回结果数量 limit
            ]
        ];
        if ($columns != false) {
            $params['body']['_source'] = $columns;
        }
        if ($sort != false) {
            $params['body']['sort'] = $sort;
        }
        return $this->es->search($params);
    }

    /**
     * 单个条件范围查询匹配（类似mysql的between）
     * $data =['gt' => 16, 'lt' => 18 ]  gt大于,gte大于等于,lt小于,lte小于等于
     * 排序：$sort=['price'=>['orders'=>'desc'],'id'=>['orders'=>'asc']] 其中orders是固定的
     * 返回显示的字段：$columns =['name','age']
     */
    public function findbetween($index, $type, $field, $data, $sort = false, $columns = false, $start = 0, $limit = 10)
    {
        $params = [
            'index' => $index,
            'type' => $type,
            'body' => [
                'query' => [
                    'constant_score' => [ //非评分模式执行
                        'filter' => [ //过滤器，不会计算相关度，速度快
                            'range' => [
                                $field => $data
                            ]
                        ]
                    ]
                ],
                'from' => $start, //从第几条开始查询
                'size' => $limit //返回结果数量 limit
            ]
        ];
        if ($columns != false) {
            $params['body']['_source'] = $columns;
        }
        if ($sort != false) {
            $params['body']['sort'] = $sort;
        }
        return $this->es->search($params);
    }

    /**
     * 插入单条Document
     * $data 要插入定的数据
     */
    public function insert($index, $type, $data)
    {
        $params = [
            'index' => $index,
            'type' => $type,
            'body' => $data
        ];
        return $this->es->index($params);
    }

    /**
     * 插入多条Document
     */
    public function insertAll($index, $type, $data)
    {
        $params = [];
        foreach ($data as $v) {
            $params['body'][] = [
                'index' => [
                    '_index' => $index,
                    '_type' => $type,
                ]
            ];
            $params['body'][] = $v;
        }
        return $this->es->bulk($params);
    }

    /**
     * 通过id获取Document
     */
    public function getDocById($index, $type, $id)
    {
        $params = [
            'index' => $index,
            'type' => $type,
            'id' => $id
        ];
        return $this->es->get($params);
    }

    /**
     * 通过id更新Document
     */
    public function updateDocById($index, $type, $id, $data)
    {
        $params = [
            'index' => $index,
            'type' => $type,
            'id' => $id,
            'body' => $data
        ];
        return $this->es->update($params);
    }

    /**
     * 通过id删除Document
     */
    public function deleteDocById($index, $type, $id)
    {
        $params = [
            'index' => $index,
            'type' => $type,
            'id' => $id
        ];
        return $this->es->delete($params);
    }

    /**
     * 创建索引
     */
    public function createIndex($index, $type, $properties)
    {
        $params = [
            'index' => $index, //索引名称
            'body' => [
                'settings' => [ //配置
                    'number_of_shards' => 3,//主分片数
                    'number_of_replicas' => 1 //主分片的副本数
                ],
                'mappings' => [  //映射
                    $type => [ //默认配置，每个类型缺省的配置使用默认配置
                        '_all' => [   //  关闭所有字段的检索
                            'enabled' => 'false'
                        ],
                        '_source' => [   //  存储原始文档
                            'enabled' => 'true'
                        ],
                        'properties' => $properties
                    ]
                ],
            ]
        ];
        return $this->es->indices()->create($params);
    }

    /**
     * 删除索引
     */
    public function deleteIndex($index)
    {
        $params = [
            'index' => $index
        ];
        return $this->es->indices()->delete($params);
    }

    /**
     * 查看索引
     */
    public function getMappings($index)
    {
        $params = [
            'index' => $index
        ];
        return $this->es->indices()->getMapping($params);
    }

    /**
     * 修改索引
     */
    public function updateMappings($index, $type, $properties)
    {
        $params = [
            'index' => $index,
            'type' => $type,
            'body' => [
                $type => [
                    'properties' => $properties
                ]
            ]
        ];
        return $this->es->indices()->putMapping($params);
    }

}