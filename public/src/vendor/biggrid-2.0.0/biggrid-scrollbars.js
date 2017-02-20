/*
 * jQuery Big Grid Scrollbars
 * Copyright 2010-2011, Gold Prairie, Inc.
 */

;(function($, window, document, undefined) {
    'use strict';

    // compliments of Gold Prairie LLC
    if (typeof $.getScrollbarSize != 'function')
    {
        $.getScrollbarSize = function() {
            var $el = $('<div style="width: 100px; height: 100px; overflow: scroll; position: absolute; top: -2000px; left: -2000px;">&nbsp;</div>').appendTo('body');
            var el = $el.get(0);
            var w = el.offsetWidth - el.clientWidth;
            var h = el.offsetHeight - el.clientHeight;
            $el.remove();
            return { 'width': w, 'height': h };
        }
    }

    $.biggridscrollbars = function(_el, options)
    {
        this._generateUid = function()
        {
            return (Math.random().toString(36).substring(2) + Math.random().toString(36).substring(2)).substring(0,16);
        }

        // unique identifier and namespace for each biggrid scrollbar
        var uid = this._generateUid(),
            ns = 'biggridscrollbars-'+uid;

        // local variables for each instance of the plugin (avoids scope issues)
        var me = this,
            el = _el,
            $el = $(el),
            $doc = $(document),
            $html = $('html'),
            $body = $('body'),
            $el_parent = $el.parent(),
            $scrollbars = $(),
            $scrollbar_corner = $(),
            $scrollbar_thumbs = $(),
            $vert_scrollbar = $(),
            $vert_scrollbar_thumb = $(),
            $horz_scrollbar = $(),
            $horz_scrollbar_thumb = $(),
            $drag_el = $(),
            vert_scrollbar_top = undefined,
            vert_scrollbar_height = undefined,
            horz_scrollbar_left = undefined,
            horz_scrollbar_width = undefined,
            vert_thumb_top_before_drag = undefined,
            vert_thumb_top = undefined,
            vert_thumb_height = undefined,
            vert_thumb_proportional = undefined,
            horz_thumb_left_before_drag = undefined,
            horz_thumb_left = undefined,
            horz_thumb_width = undefined,
            horz_thumb_proportional = undefined,
            mouse_x = undefined,
            mouse_y = undefined,
            mousedown_x = undefined,
            mousedown_y = undefined,
            mousedrag_dx = undefined,
            mousedrag_dy = undefined,
            drag_direction = 'none', // 'horizontal' or 'vertical'
            dragging = false,
            scrollbar_hover = false,
            scrollbar_hover_timer = undefined,
            scroll_repeat_timer = undefined,
            show_vert = false,
            show_horz = false,
            native_scrollbars_visible = undefined,
            opts = this.options = $.extend({}, $.biggridscrollbars.defaults, options);

        // initialization function
        this.init = function()
        {
            // remove any existing data
            var old_me = $el.data('biggridscrollbars');
            if (old_me !== undefined)
                $el.removeData('biggridscrollbars');

            // add a reverse reference to the DOM element
            $el.data('biggridscrollbars', me);

            // render the scrollbar(s)
            me._render();

            // initialize all events
            me.initEvents();

            return me;
        }

        this.initEvents = function()
        {
            if (opts.forwardScrollEvent === true)
                $el.on('customscroll.biggridscrollbars.'+ns, me.onCustomScroll);

            $el.on('resize.biggridscrollbars.'+ns, me.onResize);
            $el.on('mousemove.biggridscrollbars.'+ns, me.onShowScrollbars);
            $el.on('scroll.biggridscrollbars.'+ns, me.onShowScrollbars);
            $scrollbar_thumbs.on('mousedown.biggridscrollbars.'+ns, me.onScrollbarThumbsMouseDown);
            $scrollbars.on('mousedown.biggridscrollbars.'+ns, me.onScrollbarsMouseDown);
            $scrollbars.on('mousemove.biggridscrollbars.'+ns, me.onScrollbarsMouseMove);
            $doc.on('mousemove.biggridscrollbars.'+ns, me.onDocumentMouseMove);
            $doc.on('mouseup.biggridscrollbars.'+ns, me.onDocumentMouseUp);

            return me;
        }

        this.uninitEvents = function()
        {
            $el.off('.'+ns);
            $scrollbar_thumbs.off('.'+ns);
            $doc.off('.'+ns);

            return me;
        }

        this.getScrollbarSize = function()
        {
            if (opts.docked !== true && opts.alwaysShow === false)
                return { 'width': 0, 'height': 0 };

            var w = $vert_scrollbar.width(),
                h = $horz_scrollbar.height();

            return { 'width': w, 'height': h };
        }

        this.update = function()
        {
            // percents
            var thumb_width_pct = Math.floor(el.clientWidth*1000000/el.scrollWidth)/1000000,
                thumb_height_pct = Math.floor(el.clientHeight*1000000/el.scrollHeight)/1000000,
                thumb_left_pct = Math.floor(el.scrollLeft*1000000/el.scrollWidth)/1000000,
                thumb_top_pct = Math.floor(el.scrollTop*1000000/el.scrollHeight)/1000000;

            // update member variables (in pixels)
            horz_scrollbar_left = $horz_scrollbar.offset().left;
            horz_scrollbar_width = $horz_scrollbar.width();
            horz_thumb_width = horz_scrollbar_width*thumb_width_pct;
            vert_scrollbar_top = $vert_scrollbar.offset().top;
            vert_scrollbar_height = $vert_scrollbar.height();
            vert_thumb_height = vert_scrollbar_height*thumb_height_pct;

            horz_thumb_proportional = true;
            vert_thumb_proportional = true;

            // if we're using a fixed size for the scrollbar thumbs, we have to subtract
            // that from the scrollbar length as the scrollbar thumb size no longer
            // proportionally reflects the viewport size

            if (horz_thumb_width < opts.thumbMinSize)
            {
                horz_thumb_width = opts.thumbMinSize;
                horz_scrollbar_width -= horz_thumb_width;
                horz_thumb_proportional = false;
            }

            if (vert_thumb_height < opts.thumbMinSize)
            {
                vert_thumb_height = opts.thumbMinSize;
                vert_scrollbar_height -= vert_thumb_height;
                vert_thumb_proportional = false;
            }

            // update member variables
            vert_thumb_top = vert_scrollbar_height*thumb_top_pct;
            horz_thumb_left = horz_scrollbar_width*thumb_left_pct;

            $vert_scrollbar_thumb.css({ 'height': vert_thumb_height+'px', 'top': vert_thumb_top+'px' });
            $horz_scrollbar_thumb.css({ 'width': horz_thumb_width+'px', 'left': horz_thumb_left+'px' });

            show_horz = (thumb_width_pct < 1) ? true : false;
            show_vert = (thumb_height_pct < 1) ? true : false;

            $el_parent[show_vert?'addClass':'removeClass'](opts.verticalVisibleStateCls);
            $el_parent[show_horz?'addClass':'removeClass'](opts.horizontalVisibleStateCls);

            return me;
        }

        this.destroy = function()
        {
            me.uninitEvents();

            // resize the client window so the native scrollbars are shown
            me._showNativeScrollbars(true);

            // remove elements from the DOM
            $scrollbars.remove();
            $scrollbar_corner.remove();

            // remove reverse reference from the DOM element
            $el.removeData('biggridscrollbars');

            return me;
        }

// -- private methods --

        this._startDrag = function(drag_el)
        {
            $html.addClass('bgg-scrollbar-state-scrolling');

            // establish dragging variables
            $drag_el = $(drag_el);
            drag_direction = ($drag_el.parent().hasClass(opts.horizontalCls) ? 'horizontal' : 'vertical');
            dragging = true;
            mousedrag_dx = 0;
            mousedrag_dy = 0;
            vert_thumb_top_before_drag = vert_thumb_top;
            horz_thumb_left_before_drag = horz_thumb_left;

            return me;
        }

        this._endDrag = function()
        {
            $html.removeClass('bgg-scrollbar-state-scrolling');

            // zero out dragging variables
            $drag_el = $();
            drag_direction = 'none';
            dragging = false;
            mousedrag_dx = undefined;
            mousedrag_dy = undefined;
            vert_thumb_top_before_drag = undefined;
            horz_thumb_left_before_drag = undefined;

            return me;
        }

        this._fadeInScrollbars = function()
        {
            if (opts.alwaysShow === true || opts.docked === true)
                return;

            if (show_vert === true)
                $vert_scrollbar.fadeIn(opts.fadeInDuration);

            if (show_horz === true)
                $horz_scrollbar.fadeIn(opts.fadeInDuration);

            return me;
        }

        this._fadeOutScrollbars = function()
        {
            if (opts.alwaysShow === true || opts.docked === true)
                return;

            if (dragging !== true && scrollbar_hover !== true)
                $scrollbars.fadeOut(opts.fadeOutDuration);

            return me;
        }

        this._doHorizontalDrag = function()
        {
            var tleft = horz_thumb_left_before_drag + mousedrag_dx,
                tright = (horz_thumb_left_before_drag + horz_thumb_width) + mousedrag_dx;

            // handle minimum position
            if (tleft <= 0)
                tleft = 0;

            // handle maximum position (proportional scrollbar)
            if (horz_thumb_proportional === true && tright >= horz_scrollbar_width)
                tleft = Math.ceil(horz_scrollbar_width - horz_thumb_width);

            // handle maximum position (non-proportional scrollbar)
            if (horz_thumb_proportional !== true && tleft >= horz_scrollbar_width)
                tleft = horz_scrollbar_width;

            // update thumb position
            $horz_scrollbar_thumb.css('left', tleft+'px');

            // if live dragging is enabled, fire a horizontal scroll event
            if (opts.horzLiveDrag === true)
            {
                var new_left = Math.floor(tleft*1000000/horz_scrollbar_width)/1000000;

                $el.trigger($.Event('customscroll.biggridscrollbars'), {
                    direction: 'horizontal',
                    scrollLeftPct: new_left,
                    scrollLeft: new_left * el.scrollWidth
                });
            }

            return me;
        }

        this._doVerticalDrag = function()
        {
            var ttop = vert_thumb_top_before_drag + mousedrag_dy,
                tbottom = (vert_thumb_top_before_drag + vert_thumb_height) + mousedrag_dy;

            // handle minimum position
            if (ttop <= 0)
                ttop = 0;

            // handle maximum position (proportional scrollbar)
            if (vert_thumb_proportional === true && tbottom >= vert_scrollbar_height)
                ttop = Math.ceil(vert_scrollbar_height - vert_thumb_height);

            // handle maximum position (non-proportional scrollbar)
            if (vert_thumb_proportional !== true && ttop >= vert_scrollbar_height)
                ttop = vert_scrollbar_height;

            // update thumb position
            $vert_scrollbar_thumb.css('top', ttop+'px');

            // if live dragging is enabled, fire a vertical scroll event
            if (opts.vertLiveDrag === true)
            {
                var new_top = Math.floor(ttop*1000000/vert_scrollbar_height)/1000000;

                $el.trigger($.Event('customscroll.biggridscrollbars'), {
                    direction: 'vertical',
                    scrollTopPct: new_top,
                    scrollTop: new_top * el.scrollHeight
                });
            }

            return me;
        }

        this._doHorizontalScroll = function()
        {
            var new_scrollleft,
                tleft = horz_scrollbar_left+horz_thumb_left,
                tright = horz_scrollbar_left+horz_thumb_left+horz_thumb_width;

            // we're inside the scrollbar thumb; don't scroll
            if (mouse_x >= tleft && mouse_x <= tright)
                return;

            // scroll left
            if (mouse_x < tleft)
                new_scrollleft = el.scrollLeft-(el.clientWidth*0.6);

            // scroll right
            if (mouse_x > tright)
                new_scrollleft = el.scrollLeft+(el.clientWidth*0.6);

            // fire vertical scroll event
            var new_left = Math.floor(new_scrollleft*1000000/el.scrollWidth)/1000000;

            $el.trigger($.Event('customscroll.biggridscrollbars'), {
                direction: 'horizontal',
                scrollLeftPct: new_left,
                scrollLeft: new_scrollleft
            });

            scroll_repeat_timer = setTimeout(me._doHorizontalScroll, opts.scrollRepeatDelay);

            return me;
        }

        this._doVerticalScroll = function()
        {
            var new_scrolltop,
                ttop = vert_scrollbar_top+vert_thumb_top,
                tbottom = vert_scrollbar_top+vert_thumb_top+vert_thumb_height;

            // we're inside the scrollbar thumb; don't scroll
            if (mouse_y >= ttop && mouse_y <= tbottom)
                return;

            // scroll up
            if (mouse_y < ttop)
                new_scrolltop = el.scrollTop-(el.clientHeight*0.9);

            // scroll down
            if (mouse_y > tbottom)
                new_scrolltop = el.scrollTop+(el.clientHeight*0.9);

            // fire vertical scroll event
            var new_top = Math.floor(new_scrolltop*1000000/el.scrollHeight)/1000000;

            $el.trigger($.Event('customscroll.biggridscrollbars'), {
                direction: 'vertical',
                scrollTopPct: new_top,
                scrollTop: new_scrolltop
            });

            scroll_repeat_timer = setTimeout(me._doVerticalScroll, opts.scrollRepeatDelay);

            return me;
        }

        this._showNativeScrollbars = function(val)
        {
            var show = (val === true) ? true : false;

            // already set; we're done
            if (show === native_scrollbars_visible)
                return;

            var s = undefined,
                w = undefined,
                h = undefined;

            // contract/expand window to show/hide native scrollbars (respectively)
            if (show === true)
            {
                w = 0;
                h = 0;

                $scrollbars.hide();
                $scrollbar_corner.hide();
            }
             else
            {
                s = $.getScrollbarSize();
                w = s.width;
                h = s.height;

                if (opts.docked === true)
                {
                    var vert_width = $scrollbar_corner.width(),
                        horz_height = $scrollbar_corner.height();

                    w -= vert_width;
                    h -= horz_height;
                }

                w *= -1;
                h *= -1;

                $scrollbars.show();
                $scrollbar_corner.show();
            }

            // calculate how much we need to expand the client window's width and
            // height to have the effect of hiding the scrollbars (provided the
            // client window's parent window has 'overflow: hidden' specified)
            $el.css('right', w+'px');
            $el.css('bottom', h+'px');

            // store state
            native_scrollbars_visible = show;

            return me;
        }

        this._render = function()
        {
            var scrollbars = '' +
                '<div class="'+opts.cls+' '+opts.verticalCls+'"><div class="'+opts.thumbCls+'"></div></div>' +
                '<div class="'+opts.cls+' '+opts.horizontalCls+'"><div class="'+opts.thumbCls+'"></div></div>';

            $scrollbars = $(scrollbars).prependTo($el_parent);
            $scrollbar_thumbs = $scrollbars.find('.'+opts.thumbCls);

            if (opts.alwaysShow === true || opts.docked === true)
                $scrollbars.show();

            $vert_scrollbar = $scrollbars.filter('.'+opts.verticalCls);
            $vert_scrollbar_thumb = $vert_scrollbar.find('.'+opts.thumbCls);
            $vert_scrollbar_thumb.css('min-height', opts.thumbMinSize+'px');

            $horz_scrollbar = $scrollbars.filter('.'+opts.horizontalCls);
            $horz_scrollbar_thumb = $horz_scrollbar.find('.'+opts.thumbCls);
            $horz_scrollbar_thumb.css('min-width', opts.thumbMinSize+'px');

            if (typeof opts.vertSize == 'number')
                $vert_scrollbar.width(opts.vertSize);

            if (typeof opts.horzSize == 'number')
                $horz_scrollbar.height(opts.horzSize);

            // force scrollbars to be flush with client window's right and bottom borders
            if (opts.docked === true)
            {
                $scrollbar_corner = $('<div class="'+opts.dockedCls+' '+opts.cornerCls+'"></div>').prependTo($el_parent);

                if (typeof opts.vertSize == 'number')
                    $scrollbar_corner.width(opts.vertSize);

                if (typeof opts.horzSize == 'number')
                    $scrollbar_corner.height(opts.horzSize);

                var vert_width = $scrollbar_corner.width(),
                    horz_height = $scrollbar_corner.height();

                $vert_scrollbar.addClass(opts.dockedCls).css({
                    'top': '0px',
                    'right': '0px',
                    'bottom': horz_height
                });

                $horz_scrollbar.addClass(opts.dockedCls).css({
                    'left': '0px',
                    'bottom': '0px',
                    'right': vert_width
                });
            }

            // resize the client window so the native scrollbars are hidden
            me._showNativeScrollbars(false);

            // initialize the scrollbar thumbs
            me.update();

            return me;
        }

// -- event handlers --

        this.onCustomScroll = function(evt, params)
        {
            if (params.direction == 'vertical')
                $el.scrollTop(params.scrollTop);
            if (params.direction == 'horizontal')
                $el.scrollLeft(params.scrollLeft);
        }

        this.onResize = function(evt)
        {
            me.update();
            evt.stopPropagation();
        }

        this.onShowScrollbars = function(evt)
        {
            me._fadeInScrollbars();
            clearTimeout(scrollbar_hover_timer);
            scrollbar_hover_timer = setTimeout(me._fadeOutScrollbars, opts.fadeOutDelay);
        }

        this.onScrollbarThumbsMouseDown = function(evt)
        {
            mousedown_x = evt.pageX;
            mousedown_y = evt.pageY;

            if (dragging !== true)
                me._startDrag(this);

            clearTimeout(scrollbar_hover_timer);

            // don't bubble up to scrollbar
            evt.stopPropagation();
        }

        this.onScrollbarsMouseDown = function(evt)
        {
            mousedown_x = evt.pageX;
            mousedown_y = evt.pageY;

            var scroll_dir = $(this).hasClass(opts.horizontalCls) ? 'horizontal' : 'vertical';

            if (dragging !== true && scroll_dir === 'horizontal')
            {
                // scroll right away and then scroll at the specified interval
                me._doHorizontalScroll();
            }

            if (dragging !== true && scroll_dir === 'vertical')
            {
                // scroll right away and then scroll at the specified interval
                me._doVerticalScroll();
            }

            // don't bubble up past ourself
            evt.stopPropagation();
        }

        me.onScrollbarsMouseMove = function(evt)
        {
            mouse_x = evt.pageX;
            mouse_y = evt.pageY;

            scrollbar_hover = true;

            // only bubble up to document if we're dragging
            if (dragging !== true)
                evt.stopPropagation();
        }

        this.onDocumentMouseMove = function(evt)
        {
            mouse_x = evt.pageX;
            mouse_y = evt.pageY;

            scrollbar_hover = false;

            mousedrag_dx = mouse_x - mousedown_x;
            mousedrag_dy = mouse_y - mousedown_y;

            if (dragging === true && drag_direction === 'horizontal')
                me._doHorizontalDrag();

            if (dragging === true && drag_direction === 'vertical')
                me._doVerticalDrag();
        }

        this.onDocumentMouseUp = function(evt)
        {
            if (dragging === true && drag_direction === 'horizontal')
                me._doHorizontalDrag();

            if (dragging === true && drag_direction === 'vertical')
                me._doVerticalDrag();

            mousedown_x = undefined;
            mousedown_y = undefined;

            if (dragging === true)
                me._endDrag();

            // stop the scroll repeat
            clearTimeout(scroll_repeat_timer);

            scrollbar_hover_timer = setTimeout(me._fadeOutScrollbars, opts.fadeOutDelay);
        }

// -- initialize ourself --

        me.init();
    };

    $.biggridscrollbars.defaults = {
        cls: 'bgg-scrollbar',
        dockedCls: 'bgg-scrollbar-docked',
        cornerCls: 'bgg-scrollbar-corner',
        verticalCls: 'bgg-scrollbar-vert',
        horizontalCls: 'bgg-scrollbar-horz',
        verticalVisibleStateCls: 'bgg-state-scrollbar-vert-visible',
        horizontalVisibleStateCls: 'bgg-state-scrollbar-horz-visible',
        thumbCls: 'bgg-scrollbar-thumb',
        thumbMinSize: 18,
        horzSize: 'auto',
        vertSize: 'auto',
        scrollRepeatDelay: 120,
        fadeInDuration: 300,
        fadeOutDuration: 600,
        fadeOutDelay: 1200,
        alwaysShow: false,
        forwardScrollEvent: true,
        horzLiveDrag: true,
        vertLiveDrag: true,
        docked: true
    };

    $.fn.biggridscrollbars = function(options) {
        return this.each(function() {
            (new $.biggridscrollbars(this, options));
        });
    };

})(jQuery, window, document);
