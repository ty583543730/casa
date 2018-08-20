var SK = SK || {};

/**
 * 获取页面节点的值
 *@param {string} obj 节点对象 .class
 *@return {object}
 * */
SK.getParams = function (obj, all) {
    if(!all)  all = 0;
    var params = {};
    var chk = {}, s;
    $(obj).each(function () {
        if ($(this)[0].type == 'hidden' || $(this)[0].type == 'number' || $(this)[0].type == 'tel' || $(this)[0].type == 'password' || $(this)[0].type == 'select-one' || $(this)[0].type == 'textarea' || $(this)[0].type == 'text') {
            if (($(this).val() != '' && $(this).attr('id')) || all == 0) {
                params[$(this).attr('id')] = $.trim($(this).val());
            }
        } else if ($(this)[0].type == 'radio') {
            if ($(this).attr('name')) {
                params[$(this).attr('name')] = $('input[name=' + $(this).attr('name') + ']:checked').val();
            }
        } else if ($(this)[0].type == 'checkbox') {
            if ($(this).attr('name') && !chk[$(this).attr('name')]) {
                s = [];
                chk[$(this).attr('name')] = 1;
                $('input[name=' + $(this).attr('name') + ']:checked').each(function () {
                    s.push($(this).val());
                });
                params[$(this).attr('name')] = s.join(',');
            }
        }
    });
    chk = null, s = null;
    return params;
};

/**
 * 解析URL
 * @param  {string} url 被解析的URL
 * @return {object}     解析后的数据
 */
SK.parse_url = function (url) {
    var parse = url.match(/^(?:([a-z]+):\/\/)?([\w-]+(?:\.[\w-]+)+)?(?::(\d+))?([\w-\/]+)?(?:\?((?:\w+=[^#&=\/]*)?(?:&\w+=[^#&=\/]*)*))?(?:#([\w-]+))?$/i);
    parse || $.error("url格式不正确！");
    return {
        "scheme": parse[1],
        "host": parse[2],
        "port": parse[3],
        "path": parse[4],
        "query": parse[5],
        "fragment": parse[6]
    };
};
SK.parse_str = function (str) {
    var value = str.split("&"),
        vars = {},
        param;
    for (var i = 0; i < value.length; i++) {
        param = value[i].split("=");
        vars[param[0]] = param[1];
    }
    return vars;
};
/**
 * 生成url
 *@param  {string} url 被解析的URL
 *@param  {string} vars 参数 'a=8&b=9'
 *@return {string}     url地址
 * */
SK.U = function (url, vars) {
    if (!url || url == '') return '';
    var info = this.parse_url(url),
        path = [],
        reg;
    /* 验证info */
    info.path || $.error("url格式错误！");
    url = info.path;
    /* 解析URL */
    path = url.split("/");
    path = [path.pop(), path.pop(), path.pop()].reverse();
    path[1] || $.error("SK.U(" + url + ")没有指定控制器");

    /* 解析参数 */
    if (typeof vars === "string") {
        vars = this.parse_str(vars);
    }
    /* 解析URL自带的参数 */
    info.query && $.extend(vars, this.parse_str(info.query));
    if ($.isPlainObject(vars)) {
        url += "?" + $.param(vars);
    }
    //url = url.replace(new RegExp("%2F","gm"),"+");
    url = window.conf.APP + "/" + url;
    return url;
};
/**
 * 支付转成json对象
 *@param  {string} str
 *@param  {object} json
 * */
SK.toJson = function (str) {
    var json = {};
    try {
        if (typeof(str) == "object") {
            json = str;
        } else {
            json = eval("(" + str + ")");
        }
        if (json.status && json.status == '-999') {
            SK.msg('对不起，您已经退出系统！请重新登录', {icon: 5}, function () {
                if (window.parent) {
                    window.parent.location.reload();
                } else {
                    location.reload();
                }
            });
        } else if (json.status && json.status == '-998') {
            SK.msg('对不起，您没有操作权限，请与管理员联系');
            return;
        }
    } catch (e) {
        SK.msg("系统发生错误:" + e.getMessage, {icon: 5});
        json = {};
    }
    return json;
}
SK.msg = function (msg, options, func) {
    var opts = {};
    //有抖動的效果,第二位是函數
    if (typeof(options) != 'function') {
        opts = $.extend(opts, {time: 1000, shade: [0.4, '#000000'], offset: '200px'}, options);
        return layer.msg(msg, opts, func);
    } else {
        return layer.msg(msg, options);
    }
};
/**
 * 右侧弹出iframe
 *@param  {string} id 用于控制弹层唯一标识
 *@param  {string} url  iframe的网址
 *@param  {inter} width  宽度
 * */
SK.popupRight = function (id, url, width) {
    layer.open({
        id: id,
        type: 2,
        anim: -1,
        title: false,
        closeBtn: false,
        offset: 'r',
        shade: 0.8,
        shadeClose: true,
        skin: 'layui-anim layui-anim-rl layui-layer-adminRight',
        area: width,
        content: url,
    });
};

SK.popupOpen = function (id, url, width, height, title) {
    if(!height) height = '800px';
    if(!title) title = '';
    layer.open({
        id: id,
        type: 2,
        title: title,
        shade: 0.8,
        shadeClose: true,
        area: [width, height],
        fixed: false, //不固定
        maxmin: true,
        content: url
    });
}

/**
 * 设置表单值
 * */
SK.setValue = function (name, value) {
    var first = name.substr(0, 1),
        input, i = 0,
        val;
    if ("#" === first || "." === first) {
        input = $(name);
    } else {
        input = $("[name='" + name + "']");
    }

    if (input.eq(0).is(":radio")) { //单选按钮
        input.filter("[value='" + value + "']").each(function () {
            this.checked = true;
        });
    } else if (input.eq(0).is(":checkbox")) { //复选框
        if (!$.isArray(value)) {
            val = new Array();
            val[0] = value;
        } else {
            val = value;
        }
        for (i = 0, len = val.length; i < len; i++) {
            input.filter("[value='" + val[i] + "']").each(function () {
                this.checked = true
            });
        }
    } else { //其他表单选项直接设置值
        input.val(value);
    }
};
/**
 * 设置多个表单值
 * */
SK.setValues = function (obj) {
    var input, value, val;
    for (var key in obj) {
        if ($("[name='" + key + "']")[0]) {
            SK.setValue(key, obj[key]);
        } else if ($('#' + key)[0]) {
            SK.setValue('#' + key, obj[key]);
        }
    }
};

/**
 * 设置访问权限到js中
 */
SK.setGrants = function(grant){
    SK['GRANT'] = {};
    if(!grant)return;
    var str = grant.split(',');
    for(var i=0;i<str.length;i++){
        SK['GRANT'][str[i]] = true;
    }
};
SK.msg = function(msg, options, func){
	var opts = {};
	//有抖動的效果,第二位是函數
	if(typeof(options)!='function'){
		opts = $.extend(opts,{time:1000,offset: '200px'},options);
		return layer.msg(msg, opts, func);
	}else{
		return layer.msg(msg, options);
	}
}
SK.getVerify = function(id){
	$(id).attr('src',SK.U('home/index/getVerify','rnd='+Math.random()));
}
SK.toJson = function(str){
	var json = {};
	try{
		if(typeof(str )=="object"){
			json = str;
		}else{
			json = eval("("+str+")");
		}
		if(json.status && json.status=='-999'){
			SK.msg('对不起，您已经退出系统！请重新登录',{icon:5},function(){
				if(window.parent){
					window.parent.location.reload();
				}else{
					location.reload();
				}
			});
		}else if(json.status && json.status=='-998'){
			SK.msg('对不起，您没有操作权限，请与管理员联系');
			return;
		}
	}catch(e){
		SK.msg("系统发生错误:"+e.getMessage,{icon:5});
		json = {};
	}
	return json;
}
/**
 * 表单placeholder值在低版本浏览器显示
 * */
$(function(){
    // 兼容IE9下的placeholder
    function placeholderSupport() {
        return 'placeholder' in document.createElement('input');
    }
    if(!placeholderSupport()){   // 判断浏览器是否支持 placeholder
        $("[placeholder]").each(function(){
            var _this = $(this);
            var left = _this.css("padding-left");
            _this.parent().append('<span class="placeholder">' + _this.attr("placeholder") + '</span>');
            if(_this.val() != ""){
                _this.parent().find("span.placeholder").hide();
            }
            else{
                _this.parent().find("span.placeholder").show();
            }
        }).on("focus", function(){
            $(this).parent().find("span.placeholder").hide();
        }).on("blur", function(){
            var _this = $(this);
            if(_this.val() != ""){
                _this.parent().find("span.placeholder").hide();
            }
            else{
                _this.parent().find("span.placeholder").show();
            }
        });
        // 点击表示placeholder的标签相当于触发input
        $("span.placeholder").on("click", function(){
            $(this).hide();
            $(this).siblings("[placeholder]").trigger("click");
            $(this).siblings("[placeholder]").trigger("focus");
        });
    }
});
