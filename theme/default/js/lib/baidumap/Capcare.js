/**
 * Created by wangpeng on 2015/9/2.
 */
(function (win, $) {
    var capcare = win.Capcare = win.Capcare || {
           basePrefixURL: "http://api2.capcare.com.cn:1045",//http://test.capcare.com.cn:8013",
           //basePrefixURL: "http://172.169.0.69:8082",//http://test.capcare.com.cn:8013",
       // basePrefixURL: "http://172.169.0.95:8080",
        basePrefixImgUrl: "http://api.capcare.com.cn:1045/api/upload/",
       // basePrefixImgUrl: "http://172.169.0.69:8082/api/upload/",
            rootId: "#page",
            module: {},
            userId: cLib.base.getCookie('c_user_id') || null,
            //appName: 'M2616_BD',
            appName: cLib.base.getCookie('app_name')||'M2616_BD',
            token: cLib.base.getCookie('c_user_token') || null,
            language: cLib.base.getCookie('c_language') || null,
            name: cLib.base.getCookie('c_user_name') || null,
            ableEdit:0,//判断用户是否有编辑权限，0为不可编辑，1为可编辑
            bestRole:0,//判断用户是否为定制平台的超级管理员
            userInfo: null
        };
    capcare.init = function () {
        var mt = null;
        var manage = null;
        var analyse = null;
        var alarm = null;
        var member=null;
        var addModule=null;
        var tabNode = $('#tabNode');
       /* if (GlobalCfg.config) {
            window.setTimeout(function () {
                tabNode.find("li>a").eq(0).css("color", ("#" + GlobalCfg.config.custom.nativeBgColor));
            }, 1000);
        }*/
        if (GlobalCfg.config) {
            tabNode.find("li>a").removeClass("here");
            tabNode.on('mouseover', 'li>a', function () {
                tabNode.find("li>a").css("color", "#" + GlobalCfg.config.custom.nativeForeColor);
                tabNode.find("li>a").css("background", "");
                $(".nav_warn").css({"color": "#" + GlobalCfg.config.custom.nativeForeColor,"background": ""});
                $(this).css("color", ("#" + GlobalCfg.config.custom.nativeBgColor));
                $(this).css("background", "#" + GlobalCfg.config.custom.nativeForeColor);
                $('.handle,.handle .nav_warn').css({
                    "color": ("#" + GlobalCfg.config.custom.nativeBgColor),
                    "background" : ("#" + GlobalCfg.config.custom.nativeForeColor)
                });
                if($('.nav_warn').parent().hasClass('handle')) {
                    $('.nav_warn').css({"color": ("#" + GlobalCfg.config.custom.nativeBgColor),"background": ("#" + GlobalCfg.config.custom.nativeForeColor)});
                }else{
                    $(".nav_warn").css({"color": "#" + GlobalCfg.config.custom.nativeForeColor,"background": ""});
                }
                if ($(this).find(".nav_warn").size()) {
                    $(".nav_warn").css({"color": "#" + GlobalCfg.config.custom.nativeBgColor,"background": ""});
               }
        });
        tabNode.on('mouseout', 'li>a', function () {
            tabNode.find("li>a").css("background", "");
            $(this).css("color", ("#" + GlobalCfg.config.custom.nativeForeColor));
            $(this).css("background", "");
            $(".nav_warn").css({"color": "#" + GlobalCfg.config.custom.nativeForeColor,"background": ""});
            $('.handle,.handle .nav_warn').css({"color": ("#" + GlobalCfg.config.custom.nativeBgColor),"background" : ("#" + GlobalCfg.config.custom.nativeForeColor)});
           /* if ($(this).find(".nav_warn").size()) {
                if($(this).hasClass('handle')) {
                    $(this).find(".nav_warn").css("color", ("#" + GlobalCfg.config.custom.nativeBgColor));
                }else{
                    $(this).find(".nav_warn").css("color", ("#" + GlobalCfg.config.custom.nativeForeColor));
                }
            }*/
    });
}
tabNode.on('click', 'li>a', function () {
    var moduleId = $(this).attr("href");
    var loaded = $(this).attr("loaded");
    var elem = $(this); //GlobalCfg.config.custom.bj
    $('#page').show();
    $('#manageUserInfo').hide();
    if (GlobalCfg.config) {
        tabNode.find("li>a").removeClass('handle');
        $('.nav_warn').css({"color": ("#" + GlobalCfg.config.custom.nativeForeColor),"background" : ("#" + GlobalCfg.config.custom.nativeBgColor)});
        $(this).addClass('handle');
        $('.handle,.handle .nav_warn ').css({"color": ("#" + GlobalCfg.config.custom.nativeBgColor),"background" : ("#" + GlobalCfg.config.custom.nativeForeColor)});
        $(this).attr("active", "0");
        tabNode.find("li>a").css("color", "#" + GlobalCfg.config.custom.nativeForeColor);
        tabNode.find("li>a").css("background", "");
        elem.css("color", ("#" + GlobalCfg.config.custom.nativeBgColor));
        elem.css("background", "#" + GlobalCfg.config.custom.nativeForeColor);
        /*if (elem.find(".nav_warn").size()) {
            elem.find(".nav_warn").css("color", ("#" + GlobalCfg.config.custom.nativeForeColor));
        }*/
    } else {
        tabNode.find("li>a").removeClass("here");
        $(this).addClass("here");
    }
    if(moduleId == '#addModule'){
        if(Capcare.appName=="M2616_JL"){//如果是吉林的账号登陆，扩展页签换电话号码
            $('#contectPhone').text("4006568817");
        }
    }
$(moduleId).siblings().hide();
if (!loaded) {
    if ("#monitor" == moduleId) {
        mt = new Capcare.module.monitor(function () {
            elem.attr("loaded", "1");
            $(moduleId).show();
            if (GlobalCfg.config) {
                GlobalCfg.setConfigByParam({
                    value: "#" + GlobalCfg.config.custom.nativeBgColor,
                    type: "bg_color",
                    classId: "oper_down"
                });
                GlobalCfg.setConfigByParam({
                    value: "#" + GlobalCfg.config.custom.nativeBgColor,
                    type: "bg_color",
                    classId: "trackOpTimeWrap"
                });
                GlobalCfg.setConfigByParam({
                    value: "#" + GlobalCfg.config.custom.nativeBgColor,
                    type: "bg_color",
                    classId: "devAlarmInfo"
                });
                GlobalCfg.setConfigByParam({
                    value: "#fff",
                    type: "bd_top",
                    classId: "options"
                });
                GlobalCfg.setConfigByParam({
                    value: "#" + GlobalCfg.config.custom.nativeBgColor,
                    type: "fore_color",
                    classId: "back"
                });
            }
        });
        mt.init();
    } else if ('#alarm' == moduleId) {
        if (!alarm) {
            alarm = new Capcare.module.alarm(function () {
                $(moduleId).show();
                if (GlobalCfg.config) {
                    GlobalCfg.setConfigByParam({
                        value: "#" + GlobalCfg.config.custom.nativeBgColor,
                        type: "bg_color",
                        classId: "devAlarm"
                    });
                    GlobalCfg.setConfigByParam({
                        value: "#" + GlobalCfg.config.custom.nativeBgColor,
                        type: "bg_color",
                        classId: "tx"
                    });
                    GlobalCfg.setConfigByParam({
                        value: "#" + GlobalCfg.config.custom.nativeBgColor,
                        type: "bg_color",
                        classId: "mc"
                    });
                    GlobalCfg.setConfigByParam({
                        value: "#" + GlobalCfg.config.custom.nativeBgColor,
                        type: "bg_color",
                        classId: "lx"
                    });
                    GlobalCfg.setConfigByParam({
                        value: "#" + GlobalCfg.config.custom.nativeBgColor,
                        type: "bg_color",
                        classId: "sj"
                    });
                    GlobalCfg.setConfigByParam({
                        value: "#" + GlobalCfg.config.custom.nativeBgColor,
                        type: "bg_color",
                        classId: "wz"
                    });
                    GlobalCfg.setConfigByParam({
                        value: "#" + GlobalCfg.config.custom.nativeBgColor,
                        type: "bg_color",
                        classId: "mk"
                    });
                }
            })
        }
        alarm.init();
    } else if ('#manage' == moduleId) {
        if (!manage) {
            manage = new Capcare.module.manage(mt, function () {
                $(moduleId).show();
                if (GlobalCfg.config) {
                    window.setTimeout(function () {
                        GlobalCfg.setConfigByParam({
                            value: "#" + GlobalCfg.config.custom.nativeBgColor,
                            type: "bg_color",
                            classId: "tx"
                        });
                        GlobalCfg.setConfigByParam({
                            value: "#" + GlobalCfg.config.custom.nativeBgColor,
                            type: "bg_color",
                            classId: "mc"
                        });
                        GlobalCfg.setConfigByParam({
                            value: "#" + GlobalCfg.config.custom.nativeBgColor,
                            type: "bg_color",
                            classId: "bh"
                        });
                        GlobalCfg.setConfigByParam({
                            value: "#" + GlobalCfg.config.custom.nativeBgColor,
                            type: "bg_color",
                            classId: "sj"
                        });
                        GlobalCfg.setConfigByParam({
                            value: "#" + GlobalCfg.config.custom.nativeBgColor,
                            type: "bg_color",
                            classId: "wz"
                        });
                        GlobalCfg.setConfigByParam({
                            value: "#" + GlobalCfg.config.custom.nativeBgColor,
                            type: "bg_color",
                            classId: "ss"
                        });
                    }, 0)
                }
            });
        }
        manage.init();
    } else if ('#analyse' == moduleId) {
        if (!analyse) {
            analyse = new Capcare.module.analyse(function () {
                $(moduleId).show();
                if (GlobalCfg.config) {
                    window.setTimeout(function () {
                        GlobalCfg.setConfigByParam({
                            value: "#" + GlobalCfg.config.custom.nativeBgColor,
                            type: "bd_all",
                            classId: "specialTime"
                        });
                    }, 0)
                }
            })
        }
        analyse.init();
    }else if ('#member' == moduleId) {
        if (!member) {
            member = new Capcare.module.member(mt,function () {
                $(moduleId).show();
                if (GlobalCfg.config) {
                    window.setTimeout(function () {
                        GlobalCfg.setConfigByParam({
                            value: "#" + GlobalCfg.config.custom.nativeBgColor,
                            type: "bd_all",
                            classId: "specialTime"
                        });
                    }, 0)
                }
            })
        }
        member.init();
    }else if('#addModule'==addModule){

    }
} else {
    if (moduleId == '#monitor' && $('.dWrap2').css('display') == 'block') {
        $('#showGroup').click();
    } else if (moduleId == '#monitor' && $('.dWrap1').css('display') == 'block') {
        $('#allDevices').click();
    }
}

$(moduleId).show();

//隐藏滚动条
//moduleId == '#monitor' ? $('#mainContainer .nicescroll-rails').hide() : $('#mainContainer .nicescroll-rails').show();
return false;
});
tabNode.find("li>a").eq(0).trigger("click");
}
})(window, jQuery);

(function ($) {
    //中间部分高度自适应
    resetHigh();
    $(window).resize(function () {
        resetHigh();
    })
    function resetHigh() {
        var high = $(document.body).height() - $('.header').height() - $('.footer').height();
        $('#monitor,.pannerOp,#analyse,#shortMessage,#member,#groupShowWrap,#manageUserInfo,#addModule').height(high);
        $('#gContent').height(high-$('#groupShowWrap .groupTitleWrap').height());
        $('#manage,#alarm').height(high - 20);
        $('#monitorContainer .deviceList,#monitor .deviceList2').height(high - $('#monitor .search').height());
        $('#monitor .map,#monitor .warnWrap,#monitor .analyseSingle').height(high - $('.mainWrap .options').height() - 4);
    }

    var clientWidth = (document.body ? document.body.clientWidth : document.documentElement.clientWidth);
    //左侧菜单隐藏
    $('#monitor .devPanner .toggle').on('click', function () {
        if ($(this).hasClass('close')) {
            var _this = $(this);
            $('#monitor .pannerWrap').animate({ 'left': -$('#monitor .pannerWrap').width() }, 500, function () {
                $(_this).removeClass('close').addClass('open');
            });
            $('#monitor .mainWrap').animate({ 'margin-left': '0' }, 500);
            $(".map").css("width", clientWidth);
        } else {
            var _this = $(this);
            $('#monitor .pannerWrap').animate({ 'left': 0 }, 500, function () {
                $(_this).removeClass('open').addClass('close');
            });
            $('#monitor .mainWrap').animate({ 'margin-left': $('#monitor .pannerWrap').width() }, 500);
            $(".map").css("width", clientWidth - $('.pannerWrap').width());
        }
    });



    //充值
    $('#recharge').on('click', function () {
        $('.rechargeWrap').show();
        var busy = cLib.base.overlay();
        $('.rechargeWrap .mobile').val(lObj.recharge.enterNum);
        $('#MobileTip').text('');
        $('.conSuccess').hide();
        $('.ConInfo').show();
        $('.msgTipShow').hide();

        //取消
        $('.rechargeWrap .cancel').on('click', function () {
            if (busy) {
                busy.remove();
                $('.rechargeWrap').hide();
            }
        });
        //手机号
        $('.rechargeWrap .mobile').on('focus', function () {
            if ($(this).val() == lObj.recharge.enterNum) {
                $(this).val('');
            }
        });
        $('.rechargeWrap .mobile').on('blur', function () {
            var val = $(this).val();
            $(this).attr('pass', 'no')
            if (val == '') {
                $(this).val(lObj.recharge.enterNum);
            } else {
                if (!(/^1[3|4|5|8][0-9]\d{8}$/.test(val)) || val == lObj.recharge.enterNum) {
                    $('#MobileTip').text(lObj.recharge.errNum);
                    $(this).attr('pass', 'no');
                } else {
                    $('#MobileTip').text('');
                    $(this).attr('pass', 'yes');
                }
            }
        });
        $('.changeMoney span:not(".lableTittle")').on('click', function () {
            $(this).addClass('choose').siblings().removeClass('choose');
            $('#chooseVal').val($(this).text())
        });
    });
})(jQuery);
//提交充值
function subData() {
    var mobile = $('.rechargeWrap .mobile');
    var moneyVal = $('#chooseVal').val();
    if (mobile.attr('pass') != 'yes') {
        mobile.blur();
        return false;
    } else if (!moneyVal) {
        return false;
    } else {
        $.ajax({
            url: "http://industry.capcare.com.cn:1046/api/payController/check.do",
            dataType: 'jsonp',
            jsonp: 'callback',
            data: $('#forminfo').serialize(),
            success: function (data) {
                if (data.code == 0) {
                    var WIDout_trade_no = data.WIDout_trade_no;
                    $("#WIDout_trade_no").val(WIDout_trade_no);
                    $('#forminfo').attr('action', "http://industry.capcare.com.cn:1046/api/payController/payandredr.do")
                    $('#forminfo').submit();
                    $('.msgTipShow').show();

                    function next() {
                        setTimeout(function () {
                            $.ajax({
                                url: "http://industry.capcare.com.cn:1046/api/payController/checkstatus.do",
                                dataType: 'jsonp',
                                jsonp: 'callback',
                                data: { 'WIDout_trade_no': WIDout_trade_no },
                                success: function (data) {
                                    if (data.result == true) {
                                        $('.conSuccess').show();
                                        $('.ConInfo').hide();
                                        $('#moneyShow').text(data.amount);
                                    } else {
                                        if ($('.rechargeWrap').css('display') != 'none') {
                                            next();
                                        }
                                    }
                                }
                            });
                        }, 1000)
                    }
                    next();
                } else {
                    cLib.base.dialogTip('提示', data.desc);
                }
            },
            error: function (r) {
                //console.log(r)
            }
        });
    }
}