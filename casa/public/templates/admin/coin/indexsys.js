layui.use(['form', 'upload', 'laydate', 'layer', 'table'], function () {
    var upload = layui.upload,
        laydate = layui.laydate,
        layer = layui.layer,
        table = layui.table;
    form = layui.form;
    laydate.render({
        elem: '#startTime'
    });
    laydate.render({
        elem: '#endTime'
    });
    //渲染数据表格
    table.render({
        elem: '#coinTable',
        height: 'full-90',
        url: SK.U('admin/coin/indexSys'),
        id: 'tables',
        unresize: true,
        limit: 20,
        limits: [20, 40, 60, 80, 100, 120],
        loading: true,
        page: true,
        edit: false,
        text: {
            none: '暂无相关数据'
        },
        cols: [[ //标题栏
            {
                field: 'sid', title: '类型', align: 'center', width: '15%', templet: function (d) {
                    return '<span title=" ' + d.sid + '">' + d.sid + '</span>';
                }
            },
            {
                field: 'coin', title: '英文简称', align: 'center', width: '10%', templet: function (d) {
                    return '<span title=" ' + d.coin + '">' + d.coin + '</span>';
                }
            },
            {
                field: 'addr', title: '货币地址', align: 'center', width: '15%', templet: function (d) {
                    return '<span title=" ' + d.addr + '">' + d.addr + '</span>';
                }
            },
            {
                field: 'name', title: '账户名(BTC专用)', align: 'center', width: '15%', templet: function (d) {
                    return '<span title=" ' + d.name + '">' + d.name + '</span>';
                }
            },
            {
                field: 'black', title: '可用余额', align: 'center', width: '15%', templet: function (d) {
                    return '<span title=" ' + d.black + '">' + d.black + '</span>';
                }
            },
            {
                field: 'total', title: '累计金额', align: 'center', width: '15%', templet: function (d) {
                    return '<span title=" ' + d.total + '">' + d.total + '</span>';
                }
            },
            {
                field: 'createTime', title: '创建时间', align: 'center', width: '15%', templet: function (d) {
                    return '<span title=" ' + d.createTime + '">' + d.createTime + '</span>';
                }
            },
            // {fixed: 'right', title: '操作', align: 'center', width: '10%', toolbar: '#toolBar'}
        ]],
    });
    table.on('tool(coinTable)', function (obj) { //注：tool是工具条事件名，test是table原始容器的属性 lay-filter="对应的值"
        var data = obj.data; //获得当前行数据
        var layEvent = obj.event; //获得 lay-event 对应的值（也可以是表头的 event 参数对应的值）
        var tr = obj.tr; //获得当前行 tr 的DOM对象
        if (layEvent === 'in') {
            window.location = SK.U('admin/coin/change',{id: data.id,status:1})
        }else if(layEvent === 'out'){
            window.location = SK.U('admin/coin/change',{id: data.id,status:-1})
        } else if (layEvent === 'open') { //查看
            layer.confirm('确认启用', {title: '启用', skin: 'demo-class'},
                function (index) {
                    updateStatus(data.id, 1);
                    layer.close(index);
                });
        } else if (layEvent === 'close') {
            layer.confirm('确认禁用', {title: '禁用', skin: 'demo-class'},
                function (index) {
                    updateStatus(data.id, -1);
                    layer.close(index);
                });
        }
    });
    var $ = layui.$, active = {
        reload: function () {
            var where = SK.getParams('.ipt');
            table.reload('tables', {
                page:{
                    curr:1//从第一页重新开始
                },
                where: where
            });
        }
    }
    //查询按钮
    $('.query').on('click', function () {
        var type = $(this).data('type');
        active[type] ? active[type].call(this) : '';
    });

    //更新状态
    function updateStatus(id, status) {
        $.post(SK.U('admin/coin/changeStatus'), {id: id, status: status}, function (data) {
            var json = SK.toJson(data);
            if (json.status == 1) {
                layer.msg(json.msg, {
                    icon: 1,
                    time: 2000,
                    end: function () {
                        window.location.reload();
                    }
                });
            } else {
                layer.msg(json.msg, {
                    icon: 5,
                    time: 2000
                });
            }
        })
    }

    var uploadInst = upload.render({
        elem: '#sendCode', //绑定元素
        accept: {
            extensions: 'gif,jpg,jpeg,png',
            mimeTypes: 'image/jpg,image/jpeg,image/png,image/gif'
        },
        data: {dir: 'coin'},
        url: '/admin/index/uploadPic', //上传接口
        done: function (json) {//上传完毕回调
            var html = '<img width="100" height="100" src="/' + json.savePath + json.thumb + '"/>';
            $(".coinImgBox").html(html);
            $("#img").val(json.savePath + json.thumb);
        },
        error: function () {
            //请求异常回调
        }
    });
    //新增、编辑用户钱包地址
    form.on('submit(coinForm)', function (data) {
        $('#submitCoin').attr('disabled', true);
        var param = data.field;
        param.status = $("#status").val();
        param.id = $("#id").val();
        if(param.black < 0){
            layer.msg("请输入大于0的数量", {
                icon: 5,
                time: 3000
            });
            return false;
        }
        var loading = SK.msg('正在提交处理中，请稍后...', {
            time: 600000
        });

        $.post(SK.U('admin/coin/doChange'), param, function (data) {
            var json = SK.toJson(data);
            if (json.status == 1) {
                layer.msg(json.msg, {
                    icon: 1,
                    time: 2000,
                    end: function () {
                        window.location = SK.U('admin/coin/indexSys');
                    }
                });
            } else {
                layer.msg(json.msg, {
                    icon: 5,
                    time: 3000
                });
                $('#submitCoin').attr('disabled', false);
            }
            layer.close(loading);
        });

    });

});

