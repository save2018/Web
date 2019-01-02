(function (win) {
    function DrawMapTools(bdMap) {
        this.bdMap = bdMap;
        // 设置默认停靠位置和偏移量  
        this.defaultAnchor = BMAP_ANCHOR_TOP_RIGHT;
        this.defaultOffset = new BMap.Size(0, 0);
        this.distanceTool = new BMapLib.DistanceTool(this.bdMap.map);
    }
    // 通过JavaScript的prototype属性继承于BMap.Control  
    DrawMapTools.prototype = new BMap.Control();
    DrawMapTools.prototype.initialize = function () {
        var _self = this;
        //跟踪信息
        var gzxx = document.createElement("div");
        gzxx.id = 'gzxx';
        gzxx.className = "mapGzxx";
        var gzxxDjs = document.createElement('span');
        gzxxDjs.id = 'gzxxDjs';
        var gzxxSpeed = document.createElement('span');
        gzxxSpeed.id = 'gzxxSpeed';
        var gzxxAltitude = document.createElement('span');
        gzxxAltitude.id = 'gzxxAltitude';
        var gzxxAddr = document.createElement('span');
        gzxxAddr.id = 'gzxxAddr';
        var gzxxLocal = document.createElement('span');
        gzxxLocal.id = 'gzxxLocal';
        gzxxAddr.className = 'mapGzxxAddr';
        gzxx.appendChild(gzxxDjs);
        gzxx.appendChild(gzxxSpeed);
        gzxx.appendChild(gzxxAltitude);
        gzxx.appendChild(gzxxAddr);
        gzxx.appendChild(gzxxLocal);

        //退出跟踪
        var tcgz = document.createElement("div");
        tcgz.id = 'tcgz';
        tcgz.className = "mapTcgz mapTuichuGenZong";
        var tcgzText = document.createTextNode('退出跟踪');
        tcgz.appendChild(tcgzText);
        tcgz.onclick = function (event) {
            if (event.stopPropagation) {
                event.stopPropagation();
            } else {
                event.cancelBubble = true;
            }
            initTools(false, false);
            $(this).hide();
            _self.bdMap.monitor.deviceConverge.tcgzEvent();
        }

        //地图工具栏  
        var div = document.createElement("div");
        div.style.height = '36px';
        div.style.width = '184px';
        div.style.position = 'absolute';
        div.style.border = '1px solid #a0a0a0';
        div.style.backgroundColor = '#ffffff';

        // 地图  
        var dt = document.createElement("div");
        dt.id = 'dt';
        dt.style.left = '0px';
        dt.title = '普通地图';
        dt.className = 'mapToolsActive mapToolsActive_ditu';
        div.appendChild(dt);
        dt.onclick = function (event) {
            if (event.stopPropagation) {
                event.stopPropagation();
            } else {
                event.cancelBubble = true;
            }
            initTools(false, false);
            $("div[title='显示普通地图']").click();
            $('.mapToolsActive').removeClass('active');
            $(this).addClass('active');
        }

        // 卫星  
        var wx = document.createElement("div");
        wx.style.left = '37px';
        wx.title = '卫星影像';
        wx.className = 'mapToolsActive mapToolsActive_weixing';
        div.appendChild(wx);
        wx.onclick = function (event) {
            if (event.stopPropagation) {
                event.stopPropagation();
            } else {
                event.cancelBubble = true;
            }
            initTools(false, false);
            $("div[title='显示带有街道的卫星影像']").show();
            $("div[title='显示卫星影像']").click();
            $('.mapToolsActive').removeClass('active');
            $(this).addClass('active');
            //卫星盒子
            $("div[title='显示带有街道的卫星影像']").parent().css({
                top: "28px",
                right: "100px",
                display: "block"
            });
        }

        // 全景  
        var qj = document.createElement("div");
        qj.id = "qj";
        qj.style.left = '74px';
        qj.title = '全景';
        qj.className = 'mapToolsActive mapToolsActive_quanjing';
        div.appendChild(qj);
        qj.onclick = function (event) {
            if (event.stopPropagation) {
                event.stopPropagation();
            } else {
                event.cancelBubble = true;
            }
            initTools(true, false);
            $("div[title='进入全景']").click();
            $('.mapToolsActive').removeClass('active');
            $(this).addClass('active');
        }

        // 路况
        var lk = document.createElement("div");
        lk.id = "lk";
        lk.title = '显示实时路况';
        lk.style.left = '111px';
        lk.className = 'mapToolsActive mapToolsActive_lukuang';
        div.appendChild(lk);
        lk.onclick = function (event) {
            if (event.stopPropagation) {
                event.stopPropagation();
            } else {
                event.cancelBubble = true;
            }
            initTools(false, true);
            $('#tcWrap').show();
            $('#tcBtn').click();
            _self.bdMap.trafficControl.show();
            $('.mapToolsActive').removeClass('active');
            $(this).addClass('active');
            setTimeout(function () {
                $("#tcClose").unbind().bind("click", function () {
                    $('#tcWrap').hide();
                    _self.bdMap.trafficControl.hide();
                });
            }, 100)
        }

        // 测距
        var cj = document.createElement("div");
        cj.title = '测距';
        cj.style.left = '148px';
        cj.className = 'mapToolsActive mapToolsActive_ceju';
        div.appendChild(cj);
        cj.onclick = function (event) {
            if (event.stopPropagation) {
                event.stopPropagation();
            } else {
                event.cancelBubble = true;
            }
            initTools(false, false);
            $('.mapToolsActive').removeClass('active');
            $(this).addClass('active');
            _self.distanceTool.open();
        }

        // 分享
        //var fx = document.createElement("div");

        // 添加DOM元素到地图中
        _self.bdMap.map.getContainer().appendChild(div);
        _self.bdMap.map.getContainer().appendChild(tcgz);
        _self.bdMap.map.getContainer().appendChild(gzxx);

        // 将DOM元素返回  
        return div;

        function initTools(isQjFlag, isLkFlag) {
            //关闭全景
            if (!isQjFlag) {
                if (_self.bdMap.panoramaControl) {
                    //console.log($("#dt").hasClass("active"));
                    if (!$("#qj").hasClass("active")) {
                        $("div[title='进入全景']").click();
                        $("div[title='进入全景']").click();
                    } else {
                        $("div[title='进入全景']").click();
                    }
                }
            }
            //显示地图
            $("div[title='显示普通地图']").click();

            //关闭卫星
            $("div[title='显示带有街道的卫星影像']").hide();

            //关闭鼠标测距
            $("span[title='清除本次测距']").click();
            if (_self.distanceTool) {
                _self.distanceTool.close();
            }
            //关闭路况
            if (_self.bdMap.trafficControl) {
                $('#tcWrap').hide();
                _self.bdMap.trafficControl.hide();
            }
        }
    }

    Capcare.module["drawMapTools"] = DrawMapTools;
})(window)