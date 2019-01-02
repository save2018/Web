/*!
 * Item Name    : 地图操作类
 * Creator      : 孙则浩
 * Email        : zh.sun@chinalbs.org
 * Created Date : 2015.09.06
 * @param mapId容器id
 * @param 地图基础参数
 */
(function (win) {
    function BdMap(mapId, opts, monitor) {
        this.mapId = mapId;//地图容器id
        this.map = opts.map || null;//百度地图对象
        this.lng = opts.lng;//经度
        this.lat = opts.lat//维度
        this.zoom = opts.zoom || 11;//地图缩放级别
        this.minZoom = opts.minZoom || 1,
        this.maxZoom = opts.maxZoom || 19,
        this.enableScrollWheelZoom = !!opts.enableScrollWheelZoom;//滚轮缩放
        this.enableNavigationControl = !!opts.enableNavigationControl;//地图平移缩放控件，PC端默认位于地图左上方，它包含控制地图的平移和缩放的功能。移动端提供缩放控件，默认位于地图右下方。
        this.enableOverviewMapControl = !!opts.enableOverviewMapControl;//缩略地图控件，默认位于地图右下方，是一个可折叠的缩略地图。
        this.enableScaleControl = !!opts.enableScaleControl;//比例尺控件，默认位于地图左下方，显示地图的比例关系。
        this.enableMapTypeControl = !!opts.enableMapTypeControl; //地图类型控件，默认位于地图右上方。
        this.enableCopyrightControl = !!opts.enableCopyrightControl;//版权控件，默认位于地图左下方。
        this.enableDrawingTool = !!opts.enableDrawingTool;//工具栏控件。
        this.disableDragging = !!opts.disableDragging;//禁止拖拽
        this.enableContextMenu = !!opts.enableContextMenu;//允许右键菜单。
        this.enableTrafficControl = !!opts.enableTrafficControl;//允许显示路况
        this.enablePanoramaControl = !!opts.enablePanoramaControl;//允许全景控件
        this.mapGeo = null;//地图解析器
        this.contextMenu = null;
        this.trafficControl = null;
        this.panoramaControl = null; //全景图对象
        this.monitor = monitor;
        this.callback = opts.callback;//回调函数
    };

    BdMap.prototype = {
        //地图构造器
        constructor: BdMap,
        isMapCreated: false,
        //初始化地图
        create: function () {
            var self = this;
            self.isMapCreated =true;
            //enableMapClick 地图可不可以点击
            self.map = new BMap.Map(self.mapId, { minZoom: self.minZoom, maxZoom: self.maxZoom }, { enableMapClick: false });
            //console.log(self.lng); console.log(self.lat);

            if (self.enableDrawingTool) {
                self.map.addEventListener('load', function () {
                    //console.log(self.map.isLoaded()); 
                    if (self.enableMapTypeControl) {
                        setTimeout(function () {
                            var $dt = $("div[title='显示普通地图']");
                            var $wx = $("div[title='显示卫星影像']");
                            var $wxBox = $("div[title='显示带有街道的卫星影像']");
                            //地图
                            $dt.css({
                                display: 'none',
                                boxShadow: "none",
                                border: "1px #a0a0a0 solid",
                                height: "36px",
                                width: "36px",
                                position: "absolute",
                                borderRadius: "",
                                padding: "0",
                                background: "url(images/maptools.png) no-repeat scroll -88px -14px",
                                backgroundColor: '#ffffff'
                            });
                            //卫星
                            $wx.css({
                                visibility: 'hidden',
                                boxShadow: "none",
                                border: "1px #a0a0a0 solid",
                                height: "36px",
                                width: "36px",
                                position: "absolute",
                                left: "36px",
                                borderRadius: "",
                                padding: "0",
                                background: "url(images/maptools.png) no-repeat scroll -117px -14px",
                                backgroundColor: '#ffffff'
                            });
                        }, 1000);
                    }

                    if (self.enablePanoramaControl) {
                        setTimeout(function () {
                            var $jrqj = $("div[title='进入全景']");
                            var $tcqj = $("div[title='退出全景']");
                            $jrqj.css({
                                display: 'none',
                                height: "36px",
                                width: "36px",
                                boxShadow: "none",
                                bottom: "",
                                border: "1px #a0a0a0 solid",
                                borderRight: "0px",
                                position: "absolute",
                                zIndex: -1,
                                top: "10px",
                                right: "85px",
                                background: "url(images/maptools.png) no-repeat scroll -148px -15px",
                                backgroundColor: '#ffffff'
                            });
                            $tcqj.unbind().bind("click", function () {
                                //console.log($("#dt").hasClass("active"));
                                if (!$("#qj").hasClass("active")) {
                                    $("div[title='进入全景']").click();
                                    $("div[title='进入全景']").click();
                                } else {
                                    $("div[title='进入全景']").click();
                                }
                                $("#dt").click();
                            });
                        }, 1000);
                    }
                });
            }
            self.map.centerAndZoom(self.getPoint(self.lng, self.lat), self.zoom);  // 初始化地图,设置中心点坐标和地图级别
            self.createMapGeo();

            if (self.enableScrollWheelZoom) {
                self.map.enableScrollWheelZoom();
            }
            if (self.enableNavigationControl) {
                self.map.addControl(new BMap.NavigationControl());
            }
            if (self.enableOverviewMapControl) {
                self.map.addControl(new BMap.OverviewMapControl({ isOpen: true }));
            }
            if (self.enableScaleControl) {
                self.map.addControl(new BMap.ScaleControl());
            }
            if (self.enableMapTypeControl) {
                //self.map.addControl(new BMap.MapTypeControl({ mapTypes: [BMAP_NORMAL_MAP, BMAP_HYBRID_MAP, BMAP_SATELLITE_MAP], anchor: BMAP_ANCHOR_TOP_RIGHT, offset: new BMap.Size(194, 10) }));
                self.map.addControl(new BMap.MapTypeControl({ mapTypes: [BMAP_NORMAL_MAP, BMAP_HYBRID_MAP, BMAP_SATELLITE_MAP], anchor: BMAP_ANCHOR_TOP_RIGHT, offset: new BMap.Size(10, 10) }));
            }
            if (self.disableDragging) {
                self.map.disableDragging();
            }
            if (self.enablePanoramaControl) {
                self.panoramaControl = new BMap.PanoramaControl(); //构造全景控件
                //stCtrl.setOffset(new BMap.Size(20, 60));
                self.panoramaControl.setOffset(new BMap.Size(10, 10));
                self.map.addControl(self.panoramaControl);//添加全景控件
            }
            if (self.enableTrafficControl) {
                self.trafficControl = new BMapLib.TrafficControl({
                    showPanel: true //是否显示路况提示面板
                });
                self.map.addControl(self.trafficControl);
                self.trafficControl.setAnchor(BMAP_ANCHOR_BOTTOM_RIGHT);
                $('#tcBtn').css({
                    display: 'none',
                    left: '',
                    right: '48px',
                    top: '10px',
                    width: '36px',
                    height: '36px',
                    border: '1px solid #a0a0a0',
                    borderRight: '0px',
                    background: "url(images/maptools.png) no-repeat scroll -178px -15px",
                    backgroundColor: '#ffffff'
                });

                $('#tcBtn').attr('title', '显示实时路况');
                $("#tcWrap").css({ position: 'absolute', top: '38px', height: '50px', zIndex: 20, right: "1px" });
                $('#tcPredition').css({ border: '1px solid #8ba4d8', borderTop: '0px', position: 'absolute', backgroundColor: '#ffffff', width: '250px', top: '40px', right: '-1px' });

            }
            if (self.enableContextMenu) {
                self.contextMenu = new BMap.ContextMenu();
            }
            if (self.enableDrawingTool) {
                // 创建控件实例  
                var tools = new Capcare.module.drawMapTools(self);
                // 添加到地图当中  
                self.map.addControl(tools);
                $("#dt").click();
            }7
            if (self.enableCopyrightControl) {
                self.map.addControl(new BMap.CopyrightControl());
            } else {
                //this.map.removeCopyright(this.mapId);
                win.setTimeout(function () {
                    $("#" + self.mapId).find(".anchorBL").remove(); // 删除版权
                }, 100);
            }
            self.callback && self.callback();
        },
        //重新设置地图，恢复地图初始化时的中心点和级别。
        resetMap: function (lnglat) {
            this.map.setCenter(this.getPoint(lnglat.lng, lnglat.lat));
            //this.map.reset();
            //this.clearOverlays();
            //this.centerAndZoom({lng:this.lng,lat:this.lat}, this.zoom);
        },
        getPoint: function (lng, lat) {
            return new BMap.Point(lng, lat);
        },
        //添加瓦片
        addTileLayer: function (tileLayer) {
            this.map.addTileLayer(tileLayer);
        },
        //删除瓦片
        removeTileLayer: function (tileLayer) {
            this.map.removeTileLayer(tileLayer);
        },
        //获取地图当前级别
        getMapLevel: function () {
            return this.map.getZoom();
        },
        //设置地图当前级别
        setMapLevel: function (zoom) {
            this.map.setZoom(zoom);
        },
        //添加覆盖物
        addOverlay: function (overlay) {
            this.map.addOverlay(overlay);
        },
        //删除指定覆盖物
        removeOverlay: function (overlay) {
            if (this.map) {
                this.map.removeOverlay(overlay);
            }
        },
        /**
         * 判断marker在地图上是否可见
         * @param oMarker
         * @returns {*}
         */
        markerIsVisible: function (oMarker) {
            return oMarker.isVisible();
        },
        /**
         * 清除地图上的所有覆盖物
         */
        clearOverlays: function () {
            if (this.map) {
                this.map.clearOverlays();
                gOverlays = [];
            }
        },
        /**
         * 创建圆，并添加在地图上
         * @param oLnglat
         * @param sRadius
         */
        createCircle: function (oLnglat, sRadius) {
            var circle = {}
            circle = new BMap.Circle(new BMap.Point(oLnglat.lng, oLnglat.lat), sRadius, {
                strokeColor: '#0da39a',
                fillColor: '#cee6e1',
                strokeWeight: 4,
                fillOpacity: 0.2
            });
            this.map.addOverlay(circle);
            gOverlays.push(circle);
            return circle;
        },
        /**
         * 绘制矩形
         * @param aPoints
         * @returns {{}}
         */
        createRectangle: function (aPoints) {
            var polygon = {};
            var points = [];
            for (var i in aPoints) {
                points.push(new BMap.Point(aPoints[i].lng, aPoints[i].lat));
            }
            polygon = new BMap.Polygon(points, {
                strokeColor: "#0da39a",
                fillColor: '#cee6e1',
                strokeWeight: 4,
                fillOpacity: 0.2
            });
            this.map.addOverlay(polygon);
            gOverlays.push(polygon);
            return polygon;
        },
        /**
         * 绘制多边形
         * @param aPoints
         */
        createPolygon: function (aPoints) {
            var polygon = {};
            var points = [];
            for (var i in aPoints) {
                points.push(new BMap.Point(aPoints[i].lng, aPoints[i].lat));
            }
            polygon = new BMap.Polygon(points, {
                strokeColor: "#0da39a",
                fillColor: '#cee6e1',
                strokeWeight: 4,
                fillOpacity: 0.2
            });
            this.map.addOverlay(polygon);
            gOverlays.push(polygon);
            return polygon;
        },
        /**
         * 添加地图缩放事件
         * @param oFun
         */
        addZoomChangeEvent: function (oFun) {
            this.map.addEventListener('zoomend', oFun);
        },
        /**
         * 在地图上画折线
         * @param aPoints
         * @param oOption
         */
        createPolyline: function (aPoints, oOption) {
            var gPoints = [];
            var lineStyle = '';
            var color = '#779be7';
            var bDashed = false;
            if (oOption) {
                if (oOption.color) {
                    color = oOption.color;
                }
                if (oOption.dashed) {
                    bDashed = oOption.dashed;
                }
            }

            for (var i in aPoints) {
                gPoints.push(new BMap.Point(aPoints[i].lng, aPoints[i].lat));
            }
            if (bDashed) {
                lineStyle = 'dashed';
                color = '#09a1a4';
            }
            this.map.addOverlay(new BMap.Polyline(gPoints, { strokeColor: color, strokeStyle: lineStyle, strokeWeight: 4, strokeOpacity: 1 }));

        },
        /**
         * set地图视野
         * @param oMap
         * @param aPoints
         */
        setViewPort: function (aPoints) {
            var geoPoints = [];
            for (var i in aPoints) {
                var bpoint = new BMap.Point(aPoints[i].lng, aPoints[i].lat);
                geoPoints.push(bpoint);
            }
            this.map.setViewport(geoPoints);
        },
        //gps坐标到百度或火星坐标，如果是国外并且使google地图，则不做转换
        convertCoord: function (oLnglat) {
            var lnglat = {};
            var corG = convertWgsToGcj02(oLnglat.lng, oLnglat.lat);
            if (corG != false) {
                var corP = convertGcj02ToBd09(corG.longitude, corG.latitude);
                lnglat = { lng: corP.longitude, lat: corP.latitude };
            } else {
                lnglat = oLnglat;
            }
            return lnglat;
        },
        //百度坐标转wgs84，google国内转百度再转wgs84,google国外不做任何转换
        antiConvertCoord: function (oLnglat) {
            var lnglat = {};
            lnglat = convertBd09ToWgs(oLnglat.lng, oLnglat.lat);
            return lnglat;
        },
        //火星坐标转百度坐标
        bd_encrypt:function( gg_lat,  gg_lon)
        {
            //var lat = 31.22997,lon = 121.640756;
            //var x_pi = 3.14159265358979324 * 3000.0 / 180.0;
            var x_PI=52.36;
            var x = parseFloat(gg_lon), y = parseFloat(gg_lat);
            var z = Math.sqrt(x * x + y * y) + 0.00002 * Math.sin(y * x_pi);
            var theta = Math.atan2(y, x) + 0.000003 * Math.cos(x * x_pi);
            var bd_lon = z * Math.cos(theta) + 0.0065;
            var bd_lat = z * Math.sin(theta) + 0.006;
           var lnglat = new BMap.Point(bd_lon, bd_lat);
            return lnglat;
        },
        //坐标直接插入
        gd_encrypt:function(gg_lat,  gg_lon){
            var lnglat = new BMap.Point(gg_lon ,gg_lat);
            return lnglat;
        },
    /**
         * 创建marker，并添加在地图上
         * @param oLnglat
         * @param sIconImgUrl
         * @param oIconSizeXY
         * @param sLabel
         * @returns {{}}
         */
        createMarker: function (oLnglat, sIconImgUrl, oIconSizeXY, sLabel) {
            var icon = {};
            var marker = {};
            //NetPing(sIconImgUrl);
            icon = new BMap.Icon(sIconImgUrl, new BMap.Size(oIconSizeXY.x, oIconSizeXY.y), { imageOffset: new BMap.Size(0, 0) });
            marker = new BMap.Marker(new BMap.Point(oLnglat.lng, oLnglat.lat), { icon: icon });
            //marker.setIcon(icon);
            var label = new BMap.Label(sLabel, { offset: new BMap.Size(-20, -48) });
            label.setStyle({
                'backgroundColor': 'transparent',
                'border': 'none'
            });
            marker.setLabel(label);
            marker.lableInstance = label;
            this.map.addOverlay(marker);
            return marker;
        },
        /**
         * 获取当前视野范围
         * @returns {{}}
         */
        getMapBounds: function () {
            var boundArea = {};
            bounds = this.map.getBounds();
            boundArea.wlon = bounds.zc + 0.0002;
            boundArea.elon = bounds.wc - 0.0002;
            boundArea.nlat = bounds.vc - 0.0004;
            boundArea.slat = bounds.yc + 0.0002;
            return boundArea;
        },
        /**
         * 平移地图到指定位置
         * @param oMap
         * @param oLnglat
         */
        pantoPosition: function (oLnglat) {
            this.map.panTo(new BMap.Point(oLnglat.lng, oLnglat.lat));
        },
        /**
         * 判断marker是否在地图视野范围内
         * @param oLnglat
         * @param oBounds
         * @returns {boolean}
         */
        markerOutfoBounds: function (oLnglat, oBounds) {
            if (!(oLnglat.lng > oBounds.wlon && oLnglat.lng < oBounds.elon) ||
                !(oLnglat.lat > oBounds.slat && oLnglat.lat < oBounds.nlat)) {
                return true
            } else {
                return false;
            }
        },
        /**
         * marker置顶
         * @param oMarker
         */
        setMarkerTop: function (oMarker, flag) {
            oMarker.setTop(flag);
        },
        /**
         * 创建带文字的标注，并标注在地图上
         * @param oMap
         * @param oLnglat
         * @param nText
         */
        createTextMarker: function (oLnglat, nText) {
            var style = [
                {
                    url: 'images/icon_depart_point.png',
                    size: new BMap.Size(36, 35),
                    textSize: 15,
                    textColor: 'white'
                }
            ];
            var point = new BMap.Point(oLnglat.lng, oLnglat.lat);
            var rm = new BMapLib.TextIconOverlay(point, nText, { styles: style });
            this.map.addOverlay(rm);
        },
        /**
         * 改变marker图案
         * @param oMarker
         * @param sIconUrl
         * @param oIconSizeXY
         */
        changeMarkerIcon: function (oMarker, sIconUrl, oIconSizeXY) {
            // 创建图标对象
            var iconObj = new BMap.Icon(sIconUrl, new BMap.Size(oIconSizeXY.x, oIconSizeXY.y), { imageOffset: new BMap.Size(0, 0) });
            oMarker.setIcon(iconObj);
        },
        /**
         * 更新marker label content
         * @param oMarker
         * @param sLabel
         */
        updateMarkerLabel: function (oMarker, sLabel) {
            oMarker.setLabel(sLabel);
        },
        /**
         * 更新marker在地图上位置
         * @param oMarker
         * @param oLnglat
         */
        updateMarkerPosition: function (oMarker, oLnglat) {
            oMarker.setPosition(new BMap.Point(oLnglat.lng, oLnglat.lat));
        },
        /**
         * 更新地图标注
         * @param points
         * @param opts
         */
        updateMarkerPositionOnMap: function (marker, point) {
            marker.setPosition(point);
            this.map.panTo(point);
            // //判断标注是否在视野内 否则更新到视野内
            // if (thi.markerOutfoBounds(lnglat, bounds)) {
            // _self.bdMap.pantoPosition(lnglat);
            // }
        },
        /**
         * 清除地图上的指定覆盖物
         * @param oMap
         */
        clearOverlay: function (oItem) {
            this.map.removeOverlay(oItem);
        },

        /**
         * 清除地图上的所有覆盖物
         * @param oMap
         */
        clearAllOverlay: function () {
            this.map.clearOverlays();
            gOverlays = [];
        },
        /**
         * 根据经纬度置中
         * @param oLnglat
         * @param nZoom
         */
        centerAndZoom: function (oLnglat, nZoom) {
            this.map.centerAndZoom(new BMap.Point(oLnglat.lng, oLnglat.lat), nZoom);
        },
        /**
         * 获取marker坐标
         * @param oMarker
         * @returns {*}
         */
        getMarkerPosition: function (oMarker) {
            var position = oMarker.getPosition();
            return { lng: position.lng, lat: position.lat }
        },
        /**
         * 在地图上添加marker
         * @param oMarker
         * @param oMap
         */
        addMarkerOnMap: function (oMarker) {
            this.map.addOverlay(oMarker);
            oMarker.setLabel(oMarker.lableInstance);
            gOverlays.push(oMarker);
        },
        /**
         * 获取绘制工具栏
         * @param oMap
         * @returns {{}}
         */
        getDrawingManager: function () {
            var drawingManager = {};
            var styleOptions = {
                strokeColor: "#0da39a",    //边线颜色。
                fillColor: "#cee6e1",      //填充颜色。当参数为空时，圆形将没有填充效果。
                strokeWeight: 4,       //边线的宽度，以像素为单位。
                fillOpacity: 0.2   //填充的透明度，取值范围0 - 1。
            };
            drawingManager = new BMapLib.DrawingManager(this.map, {
                isOpen: false, //是否开启绘制模式
                circleOptions: styleOptions, //圆的样式
                rectangleOptions: styleOptions //矩形的样式
            });
            return drawingManager;
        },

        /**
         *
         * @param obj
         */
        disableEdit: function (obj) {
            obj.disableEditing();
        },

        /**
         *
         * @param obj
         * @param sType
         */
        enableEdit: function (obj, sType) {
            obj.enableEditing();
            if (sType == 2) {
                //矩形围栏
                var changeFlag = false;
                var initPoints = obj.getPath();
                obj.addEventListener('click', function () {
                    obj.enableEditing();
                    changeFlag = false
                });

                obj.addEventListener('lineupdate', function () {
                    if (!changeFlag) {
                        var cornerPoints = obj.getPath();
                        var flag = false;
                        var index = 0;
                        for (var i in cornerPoints) {
                            if (!flag) {
                                for (var j in initPoints) {
                                    if (cornerPoints[i].lng == initPoints[j].lng && cornerPoints[i].lat == initPoints[j].lat) {
                                        flag = false;
                                        break;
                                    } else {
                                        flag = true;
                                        index = i;
                                    }
                                }
                            }
                        }
                        switch (index) {
                            case '0':
                                initPoints[0] = cornerPoints[0];
                                initPoints[1].lat = cornerPoints[0].lat;
                                initPoints[3].lng = cornerPoints[0].lng;
                                break;
                            case '1':
                                initPoints[1] = cornerPoints[1];
                                initPoints[0].lat = cornerPoints[1].lat;
                                initPoints[2].lng = cornerPoints[1].lng;
                                break;
                            case '2':
                                initPoints[2] = cornerPoints[2];
                                initPoints[1].lng = cornerPoints[2].lng;
                                initPoints[3].lat = cornerPoints[2].lat;
                                break;
                            case '3':
                                initPoints[3] = cornerPoints[3];
                                initPoints[0].lng = cornerPoints[3].lng;
                                initPoints[2].lat = cornerPoints[3].lat;
                                break;
                        }
                        changeFlag = true;
                        obj.setPath(initPoints);
                        obj.disableEditing();
                    }
                })
            }
        },
        /**
         * 获取rectangle顶点
         * @param oRect
         * @returns {Array}
         */
        getRectanglePath: function (oRect) {
            var points = [];
            var bPoints = oRect.getPath();
            points.push({ lng: bPoints[1].lng, lat: bPoints[1].lat });
            points.push({ lng: bPoints[3].lng, lat: bPoints[3].lat })
            return points;
        },
        /**
         * 获取圆心坐标
         * @param oCircle
         * @returns {{}}
         */
        getCircleCenter: function (oCircle) {
            var lnglat = {};
            var center = oCircle.getCenter();
            lnglat = { lng: center.lng, lat: center.lat }
            return lnglat;
        },
        /**
         * 获取半径
         * @param oCircle
         */
        getCircleRadius: function (oCircle) {
            return oCircle.getRadius();
        },
        /**
         * 获取多边形顶点数组
         * @param oPolygon
         * @returns {Array}
         */
        getPolygonPath: function (oPolygon) {
            var points = [];
            var bPoints = oPolygon.getPath();
            for (var i in bPoints) {
                points.push({ lng: bPoints[i].lng, lat: bPoints[i].lat })
            }
            return points;
        },
        /**
         * 创建圆，并添加在地图上
         * @param sMapType
         * @param oMap
         * @param oLnglat
         * @param sRadius
         */
        createSimpleCircle: function (oLnglat, opts, sRadius) {
            var circle = new BMap.Circle(new BMap.Point(oLnglat.lng, oLnglat.lat), sRadius, {
                strokeColor: opts.strokeColor,
                strokeWeight: opts.strokeWeight,
                strokeOpacity: opts.fillOpacity,
                fillColor: opts.fillColor,
                fillOpacity: opts.fillOpacity
            });
            this.map.addOverlay(circle);
            return circle;
        },
        //修改圆的配制
        changeCircleMarker: function (circle, opts) {
            circle.setStrokeColor(opts.strokeColor);
            circle.setStrokeWeight(opts.strokeWeight);
            circle.setStrokeOpacity(opts.strokeOpacity);
            circle.setFillColor(opts.fillColor);
            circle.setFillOpacity(opts.fillOpacity);
        },
        /**
         * 添加地图事件
         * @param oMap
         * @param oFun
         */
        addMapEvent: function (funcName, cbFun) {
            this.map.addEventListener(funcName, cbFun);
        },
        /**
         * 移除地图事件
         * @param oMap
         * @param oFun
         */
        removeMapEvent: function (funcName, cbFun) {
            this.map.removeEventListener(funcName, cbFun);
        },
        /**
         * 添加地图事件
         * @param oMap
         * @param oFun
         */
        addZoomChangeEvent: function (funcName, cbFun) {
            this.map.addEventListener(funcName, cbFun);
        },
        /**
         * 移除地图事件
         * @param oMap
         * @param oFun
         */
        removeZoomEvent: function (funcName, cbFun) {
            this.map.removeEventListener(funcName, cbFun);
        },
        /**
         * 打开自定义信息窗口
         * @param options
         */
        openSearchInfoWindow: function (options) {
            if (!options) return;
            var searchInfoWindow = new BMapLib.SearchInfoWindow(this.map, options.content, {
                title: options.title,      //标题
                width: options.width,             //宽度
                height: options.height,              //高度
                panel: "panel",         //检索结果面板
                enableAutoPan: options.enableAutoPan, //自动平移
                enableSendToPhone: options.enableSendToPhone, //是否显示发送到手机按钮
                offset: new BMap.Size(options.x, options.y),
                searchTypes: [
                ]
            });

            //信息窗口位置
            //searchInfoWindow.setPosition(new BMap.Point(lng, lat));

            //打开信息窗口
            searchInfoWindow.open(new BMap.Point(options.lng, options.lat));

            return searchInfoWindow;
        },
        //地图缩放事件
        onMapScrollWheelZoom: function (flag) {
            if (flag) {
                //启用滚轮放大缩小，默认禁用。
                this.map.enableScrollWheelZoom();
            } else {
                //禁用滚轮放大缩小。
                this.map.disableScrollWheelZoom();
            }
        },
        /**
         * 添加右键菜单项
         * @param txtMenuItem
         * @param openCallBack
         * @param closeCallBack
         */
        addContextMenuItems: function (txtMenuItem, openCallBack, closeCallBack) {
            if (!txtMenuItem || !this.contextMenu) return;
            var menuItems = [];
            for (var i = 0; i < txtMenuItem.length; i++) {
                var menuItem = new BMap.MenuItem(txtMenuItem[i].text, txtMenuItem[i].callback, txtMenuItem[i].opts);
                this.contextMenu.addItem(menuItem);
                menuItem.disable();
                menuItems.push(menuItem);
            }
            this.contextMenu.addEventListener('open', function () {
                if (openCallBack) openCallBack();
            })
            this.contextMenu.addEventListener('close', function () {
                if (closeCallBack) closeCallBack();
            })
            this.map.addContextMenu(this.contextMenu);
            return menuItems;
        },
        /**
         * 移除右键菜单项
         * @param txtMenuItem
         */
        removeContextMenuItems: function (txtMenuItem) {
            if (!txtMenuItem || !this.contextMenu) return;
            for (var i = 0; i < txtMenuItem.length; i++) {
                this.contextMenu.removeItem(txtMenuItem[i]);
            }
        },
        /**
         * 地图解析器
         * @param txtMenuItem
         */
        createMapGeo: function () {
            var _self = this;
            _self.mapGeo = new BMap.Geocoder();
        },
        /**
         * 地图添加折线
         * @param points
         * @param opts
         */
        addPolyline: function (points, opts) {
            var polyline = new BMap.Polyline(points, {
                strokeColor: (opts.color || "#779be7"),
                strokeWeight: (opts.weight || 4),
                strokeOpacity: (opts.opacity || 1)
            });
            this.map.addOverlay(polyline);
            return polyline;
        },
    };
    //Capcare.module["BdMap"] = BdMap;
	//var map1 = new BMap.Map("BdMap");
})(window);