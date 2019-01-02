/*!
 * Item Name    : 复杂的自定义覆盖物
 * Creator      : 孙则浩
 * Email        : zh.sun@chinalbs.org
 * Created Date : 2015.11.10
 */
(function ($, win) {
    function ComplexCustomOverlay(options) {
        this.id         = options.id || null;//id
        this.index      = options.index || null;//index
        this.text       = options.text || null;
        this.point      = options.point || null;
        this.divStyle   = options.divStyle || null;//div样式
        this.spans      = options.spans || null;//span样式
        this.arrowStyle = options.arrowStyle || null;//arrow样式
        this.bdMap      = options.map;
        this.div        = null;
        this.arrow      = null;
    }

    ComplexCustomOverlay.prototype = new BMap.Overlay();
    ComplexCustomOverlay.prototype.initialize = function () {
        var _self = this;
        //div
        var div = document.createElement("div");
        if (_self.id) div.id                                          = _self.id;
        if (_self.point) div.style.zIndex                             = BMap.Overlay.getZIndex(_self.point.lat);
        if (_self.divStyle.position) div.style.position               = _self.divStyle.position;
        if (_self.divStyle.height) div.style.height                   = _self.divStyle.height;
        if (_self.divStyle.width) div.style.width                     = _self.divStyle.width;
        if (_self.divStyle.imgurl) div.style.background               = _self.divStyle.imgurl;
        if (_self.divStyle.backgroundColor) div.style.backgroundColor = _self.divStyle.backgroundColor;
        if (_self.divStyle.filter) div.style.filter                   = _self.divStyle.filter;
        if (_self.divStyle.opacity) div.style.opacity                 = _self.divStyle.opacity;
        if (_self.divStyle.border) div.style.border                   = _self.divStyle.border;
        if (_self.divStyle.borderRadius) div.style.borderRadius = _self.divStyle.borderRadius;
        if (_self.divStyle.textAlign) div.style.textAlign = _self.divStyle.textAlign;
        
        _self.div = div;

        //span
        if (_self.spans) {
            for (var i = 0; i < _self.spans.length; i++) {
                var spanStyle = _self.spans[i];
                var span = document.createElement("span");
                if (spanStyle.position) span.style.position               = spanStyle.position;
                if (spanStyle.padding) span.style.padding = spanStyle.padding;
                if (spanStyle.paddingTop) span.style.paddingTop = spanStyle.paddingTop;
                if (spanStyle.color) span.style.color                     = spanStyle.color;
                if (spanStyle.fontWeight) span.style.fontWeight           = spanStyle.fontWeight;
                if (spanStyle.lineHeight) span.style.lineHeight           = spanStyle.lineHeight;
                if (spanStyle.top) span.style.top                         = spanStyle.top;
                if (spanStyle.left) span.style.left                       = spanStyle.left;
                if (spanStyle.border) span.style.border                   = spanStyle.border;
                if (spanStyle.borderRadius) span.style.borderRadius       = spanStyle.borderRadius; 
                if (spanStyle.height) span.style.height                   = spanStyle.height;
                if (spanStyle.width) span.style.width                     = spanStyle.width;
                if (spanStyle.whiteSpace) span.style.whiteSpace           = spanStyle.whiteSpace;
                if (spanStyle.borderColor) span.style.borderColor         = spanStyle.borderColor;
                if (spanStyle.borderStyle) span.style.borderStyle         = spanStyle.borderStyle;
                if (spanStyle.marginLeft) span.style.marginLeft           = spanStyle.marginLeft;
                if (spanStyle.borderTopColor) span.style.borderTopColor   = spanStyle.borderTopColor;
                if (spanStyle.borderWidth) span.style.borderWidth         = spanStyle.borderWidth;
                if (spanStyle.bottom) span.style.bottom                   = spanStyle.bottom; 
                if (spanStyle.backgroundColor) span.style.backgroundColor = spanStyle.backgroundColor;
                if (spanStyle.textAlign) span.style.textAlign = spanStyle.textAlign;
                if (spanStyle.textOverflow) span.style.textOverflow = spanStyle.textOverflow;
                if (spanStyle.overflow) span.style.overflow = spanStyle.overflow;
                if (spanStyle.display) span.style.display = spanStyle.display;
                if (spanStyle.borderTop) span.style.borderTop = spanStyle.borderTop;
                if (spanStyle.wordBreak) span.style.wordBreak = spanStyle.wordBreak;
                if (spanStyle.title) span.title = spanStyle.title;
                if (spanStyle.filter) span.style.filter = spanStyle.filter;
                if (spanStyle.opacity) span.style.opacity = spanStyle.opacity;

                div.appendChild(span);
                if (spanStyle.text) span.appendChild(document.createTextNode(spanStyle.text));
            }
        }

        //箭头
        var arrow = document.createElement("div");
        if (_self.arrowStyle.position) arrow.style.position = _self.arrowStyle.position;
        if (_self.arrowStyle.width) arrow.style.width       = _self.arrowStyle.width;
        if (_self.arrowStyle.height) arrow.style.height     = _self.arrowStyle.height;
        if (_self.arrowStyle.top) arrow.style.top           = _self.arrowStyle.top;
        if (_self.arrowStyle.left) arrow.style.left         = _self.arrowStyle.left;
        div.appendChild(arrow);

        _self.arrow = arrow;

        _self.bdMap.getPanes().labelPane.appendChild(div);

        return div;
    }
    ComplexCustomOverlay.prototype.draw = function () {
        var _self = this;
        var pixel = _self.bdMap.pointToOverlayPixel(_self.point);
        _self.div.style.left = pixel.x - parseInt(_self.arrow.style.left) + "px";
        _self.div.style.top = pixel.y - 30 + "px";
    }

    Capcare.module["ComplexCustomOverlay"] = ComplexCustomOverlay;
})(jQuery, window);