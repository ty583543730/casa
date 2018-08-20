$(function() {
	layui.use(['layer', 'form'], function() {
		"use strict";
		var form = layui.form,
			layer = layui.layer;
		form.verify({
			tellphone : function(value) {
                if (value.length == 0) {
                    return '请填写手机号码';
                }else {
                	var reg=/^[1][3,4,5,7,8][0-9]{9}$/;
                	if (!reg.test(value)) {
		                return '请填写有效的手机号码';
		            }
                }
            },
            verify: function(value){
            	if (value.length == 0) {
                    return '请填写右侧验证码';
                }else {
                	var reg=/^[0-9a-zA-Z]*$/g;
                	if (!reg.test(value)) {
		                return '右侧验证码必须为数字或字母';
		            }
                }
            },
            message: function(value){
            	if (value.length == 0) {
                    return '请填写短信验证码';
                }else {
                	var reg=/^[0-9a-zA-Z]*$/g;
                	if (!reg.test(value)) {
		                return '短信验证码必须为数字或字母';
		            }
                }
            },
			pass: [/^(?![0-9]+$)(?![a-zA-Z]+$)(?!([^(0-9a-zA-Z)]|[\(\)])+$)([^(0-9a-zA-Z)]|[\(\)]|[a-zA-Z]|[0-9]){6,18}$/, '6-18位字母、数字或字符组合'],
			regPwd:function(value) {
				var pwd = $("#loginPwd").val();
				if(!new RegExp(pwd).test(value)) {
					return '两次输入的密码不一致';
				}
			},
			content: function(value) {
				layedit.sync(editIndex);
			}
		});
		form.on('submit(reg)', function(data) {
			if(!!window.ActiveXObject || "ActiveXObject" in window) {
				var check = $('#protocol').attr('checked');
			} else {
				var check = $('#protocol').attr('checked');
			}
			if(!check) {
				SK.msg("请勾选用户注册协议", {
					icon: 5
				});
				return false;
			}
            data.field.regTerminal = 1;
			$.post(SK.U('home/login/registerSumbit'), data.field, function(data, textStatus) {
				var json = SK.toJson(data);
				if(json.status > 0) {
					SK.msg('注册成功!', {
						icon: 6
					}, function() {
						if(json.data == 0) {
													location.href = SK.U('home/login/download');
						} else if(json.data == 1) {
													location.href = SK.U('home/login/register');
						}
					});
				} else {
					SK.getVerify('#verifyImg');
					SK.msg(json.msg, {
						icon: 5
					});
				}
			});
			return false;
		});
		form.render();
	});
});
$('body').on('touchend', '.isCheck',function(e) {
	e.preventDefault();
	var a = $(this).attr('id');
	var chk = $('.isCheck .chks');
	if(a == 1) {
		chk.removeAttr("checked");
		$(this).attr('id', 0);
		$('#protocol').val(0);
	} else {
		chk.attr("checked", "checked");
		$(this).attr('id', 1);
		$('#protocol').val(1);
	}
});
/*
 * 发送手机短信验证码(1)
 */
function phoneVerify(sendCode) {
	var param = new Object();
	param['phoneArea'] = $('#phoneArea').val();
	param['userPhone'] = $('#userPhone').val();
	param['imageCode'] = $('#verifyCode').val();
	param['sendCode'] = sendCode;
	param['isImage'] = 1;
	if(param['userPhone'] == '') {
		SK.msg('请输入手机号码!', {
			icon: 5
		});
		return;
	}
	if(param['imageCode'] == "" || param['imageCode'].length != 4) {
		SK.msg("图形验证码错误", {
			icon: 5
		});
		return;
	}
	phoneSend(param);
}
/*
 * 发送手机短信验证码(2)
 */
function phoneSend(param) {
	SK.msg('正在发送短信，请稍后...', {
		time: 600000
	});
	var time = 0;
	$.post(SK.U('home/site/sendSmsWithCode'), param, function(data, textStatus) {
		$('.sendDisabled').show();
		$('.sendTimeTrue').hide();
		var json = SK.toJson(data);
		if(json.status != 1) {
			SK.msg(json.msg, {
				icon: 5
			});
			SK.getVerify('#verifyImg');
			time = 0;
			$('.sendDisabled').hide();
			$('.sendTimeTrue').show();
		}
		if(json.status == 1) {
			$('.sendDisabled').show();
			$('.sendTimeTrue').hide();
			SK.msg('短信已发送，请注意查收');
			layer.closeAll('page');
			time = 120;
			$('#timeTips').attr('disabled', 'disabled').css('background', '#ccc');
			$('#timeTips').html('' + time + "秒后获取");
			var task = setInterval(function() {
				$('.sendTimeTrue').hide();
				$('.sendDisabled').show();
				$('#timeTips').html('' + time + "秒后获取");
				time--;
				if(time == 0) {
					$('.sendTimeTrue').show();
					$('.sendDisabled').hide();
					clearInterval(task);
					$('#timeTips').removeAttr('disabled').css('background', '#5187df');
				}
			}, 1000);
		}
	});
}
//设置根字体大小
var docEl = document.documentElement,
	resizeEvt = 'orientationchange' in window ? 'orientationchange' : 'resize',
	recalc = function() {
		if(docEl.clientWidth < 640 && docEl.clientWidth > 320) {
			docEl.style.fontSize = 10 * (docEl.clientWidth / 320) + 'px';
		} else if(docEl.clientWidth >= 640) {
			docEl.style.fontSize = '20px';
		} else if(docEl.clientWidth <= 320) {
			docEl.style.fontSize = '10px';
		}
	};
window.addEventListener(resizeEvt, recalc, false);
document.addEventListener('DOMContentLoaded', recalc, false);