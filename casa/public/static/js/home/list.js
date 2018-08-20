$(function () {
	if(localStorage.getItem('lang')) {
	    $('.islang').find('.textlang').html(localStorage.getItem('lang'));
		if(localStorage.getItem('lang')==='English') {
			localStorage.setItem('more','LoadMore');
			localStorage.setItem('nomore','No More');
		}else {
			localStorage.setItem('more','加载更多');
			localStorage.setItem('nomore','没有更多了');
		}
	}else {
	    $('.islang').find('.textlang').html('简体中文');
	    localStorage.setItem('more','加载更多');
		localStorage.setItem('nomore','没有更多了');
	}
    layui.use('flow', function () {
        var flow = layui.flow;

        flow.load({
            elem: '#adTable'
            , scrollElem: '#adTable'
            , done: function (page, next) { //执行下一页的回调
                setTimeout(function () {
                    $.post(SK.U('home/Index/noticePage'), {pageSize: 10, page: page}, function (data) {
                        var json = SK.toJson(data);
                        var lis = [];
                        layui.each(json.data.data, function(index, item){
                            lis.push('<li class="layui-timeline-item">');
                            lis.push('<div class="layui-timeline-title">'+item.createTime+'</div>');
                            lis.push('<i class="layui-icon layui-timeline-axis"></i>');
                            lis.push('<div class="layui-timeline-content layui-text">');
                            lis.push('<a class="color-999 v-ellipsis-3" href="'+SK.U('home/Index/info')+'?id='+item.articleId+'">'+ item.articleTitle +'</a>');
                            lis.push('</div>');
                            lis.push('</li>');
                        });
                        next(lis.join(''), page < json.data.last_page); //假设总页数为 10
                    })
                }, 500);
            }
        });

    });
});