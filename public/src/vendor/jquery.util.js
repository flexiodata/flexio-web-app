/*
 * Flex.io jQuery Utility Functions
 * Copyright 2010-2012, Gold Prairie, Inc.
 */

;(function($, window, document, undefined) {
    'use strict';

    // translate token function
    function _T(s) { return s; }

    /*
     * jQuery resize event - v1.1 - 3/14/2010
     * http://benalman.com/projects/jquery-resize-plugin/
     *
     * Copyright (c) 2010 "Cowboy" Ben Alman
     * Dual licensed under the MIT and GPL licenses.
     * http://benalman.com/about/license/
     */
    ;(function($,h,c){var a=$([]),e=$.resize=$.extend($.resize,{}),i,k="setTimeout",j="resize",d=j+"-special-event",b="delay",f="throttleWindow";e[b]=250;e[f]=true;$.event.special[j]={setup:function(){if(!e[f]&&this[k]){return false}var l=$(this);a=a.add(l);$.data(this,d,{w:l.width(),h:l.height()});if(a.length===1){g()}},teardown:function(){if(!e[f]&&this[k]){return false}var l=$(this);a=a.not(l);l.removeData(d);if(!a.length){clearTimeout(i)}},add:function(l){if(!e[f]&&this[k]){return false}var n;function m(s,o,p){var q=$(this),r=$.data(this,d);r.w=o!==c?o:q.width();r.h=p!==c?p:q.height();n.apply(this,arguments)}if($.isFunction(l)){n=l;return m}else{n=l.handler;l.handler=m}}};function g(){i=h[k](function(){a.each(function(){var n=$(this),m=n.width(),l=n.height(),o=$.data(this,d);if(m!==o.w||l!==o.h){n.trigger(j,[o.w=m,o.h=l])}});g()},e[b])}})(jQuery,window);

    /*!
     * jQuery serializeObject - v0.2 - 1/20/2010
     * http://benalman.com/projects/jquery-misc-plugins/
     *
     * Copyright (c) 2010 "Cowboy" Ben Alman
     * Dual licensed under the MIT and GPL licenses.
     * http://benalman.com/about/license/
     */

    // Whereas .serializeArray() serializes a form into an array, .serializeObject()
    // serializes a form into an (arguably more useful) object.
    $.fn.serializeObject = function(options)
    {
        // make sure we have options
        options = $.extend({}, options);

        var obj = {};

        /*
        if (options.standardizeBoolean !== false)
        {
            // make sure checkboxes post as true/false
            $(this).find('input[type="checkbox"]').each(function() {
                if (this.checked)
                    this.value = "true";
                     else
                    this.value = "false";
            });
        }
        */

        $.each(this.serializeArray(), function(i,o) {
            var n = o.name,
                v = o.value;

            // trim strings
            if (options.trimStrings !== false && typeof v == 'string')
                v = v.trim();

            obj[n] = obj[n] === undefined ? v
                : $.isArray(obj[n]) ? obj[n].concat(v)
                : [obj[n], v];
        });

        return obj;
    }

    // from http://stackoverflow.com/questions/946534/insert-text-into-textarea-with-jquery/946556
    $.fn.insertAtCaret = function(val)
    {
        return this.each(function() {
            if (document.selection)
            {
                // IE
                this.focus();
                var selection = document.selection.createRange();
                selection.text = val;
                this.focus();
            }
             else if (this.selectionStart || this.selectionStart == '0')
            {
                // FF, Chrome, etc.
                var start_pos = this.selectionStart;
                var end_pos = this.selectionEnd;
                var scroll_top = this.scrollTop;
                this.value = this.value.substring(0, start_pos) + val + this.value.substring(end_pos, this.value.length);
                this.focus();
                this.selectionStart = start_pos + val.length;
                this.selectionEnd = start_pos + val.length;
                this.scrollTop = scroll_top;
            }
             else
            {
                this.value += val;
                this.focus();
            }
        });
    }

    $.fn.htmlPlaceholder = function()
    {
        return this.each(function() {
            var $el = $(this),
                $input = $el.find('input'),
                $placeholder_el = $el.find('.html-placeholder');

            $input.keydown(function(evt) {
                // enter
                if (evt.which == 13)
                {
                    setTimeout(function() { $input.blur(); }, 10);
                    $el.trigger($.Event('submit.htmlplaceholder'));

                    evt.preventDefault();
                    return false;
                }
            });

            $input.blur(function(evt) {
                if (this.value.length == 0)
                {
                    $(this).addClass('hidden');
                    $placeholder_el.removeClass('hidden');
                }
                $el.trigger($.Event('blur.htmlplaceholder'));
            });

            $placeholder_el.click(function(evt) {
                $(this).addClass('hidden');
                $input.val('').removeClass('hidden').focus();
                $el.trigger($.Event('focus.htmlplaceholder'));
            });
        });
    }

    $.fn.numberInput = function(opts)
    {
        var selector = '';

        if (opts !== undefined && opts.selector !== undefined)
            selector = opts.selector;

        function onKeyDown(evt)
        {
            var $el = $(this),
                k = evt.keyCode;

            /*
            var dec_separator = FX_DECIMAL_SEPARATOR || '.';
            if (dec_separator.length == 0)
                dec_separator = '.';
            */

            // allow: backspace, delete, tab, escape, enter
            var allowed_keycodes = [46, 8, 9, 27, 13];

            var num_type = $el.data('number-type'),
                num_sign = $el.data('number-sign');

            // allow: '.'
            if (num_type !== 'integer' && num_type !== 'whole')
                allowed_keycodes = allowed_keycodes.concat([110, 190]);

            // whole numbers, by definition, are positive
            if (num_type !== 'whole')
            {
                // allow: '-'
                if (num_sign !== 'positive')
                    allowed_keycodes = allowed_keycodes.concat([109, 173, 189]);
            }

            if ($.inArray(k, allowed_keycodes) !== -1 ||
                 // allow: Ctrl+A, Command+A
                (k == 65 && (evt.ctrlKey === true || evt.metaKey === true )) ||
                 // allow: home, end, left, right, down, up
                (k >= 35 && k <= 40)) {
                     // let it happen, don't do anything
                     return;
            }

            // if it's not a number, stop the keypress
            if ((evt.shiftKey || (k < 48 || k > 57)) && (k < 96 || k > 105))
                evt.preventDefault();
        }

        function onKeyPress(evt)
        {
            var c = String.fromCharCode(evt.charCode);

            // if the entire string is selected, we're done
            if (this.selectionStart == 0 && this.selectionEnd && this.selectionEnd == this.value.length)
                return;

            // only allow a negative sign at the beginning of the string
            if (c == '-' && this.selectionEnd && this.selectionEnd > 0)
                evt.preventDefault();

            // only allow one negative sign
            if (c == '-' && this.value.indexOf('-') != -1)
                evt.preventDefault();

            // only allow one period decimal separator
            if (c == '.' && this.value.indexOf('.') != -1)
                evt.preventDefault();
        }

        function onKeyUp(evt)
        {
            var $el = $(this),
                min = $el.data('number-min'),
                max = $el.data('number-max');

            if (min === undefined && max === undefined)
                return;

            var val = this.value,
                num_val = Util.parseFloat(val),
                min_val = Util.parseFloat(min),
                max_val = Util.parseFloat(max);

            if (val.length > 0)
            {
                if (min !== undefined && num_val < min_val)
                    this.value = min_val;

                if (max !== undefined && num_val > max_val)
                    this.value = max_val;
            }
              else
            {
                if ($el.data('number-required') === true)
                    this.value = ''+min_val;
            }
        }

        return this.each(function() {
            var $el = $(this);

            if (selector.length > 0)
            {
                $el.on("keydown", selector, onKeyDown);
                $el.on("keypress", selector, onKeyPress);
                $el.on("keyup", selector, onKeyUp);
            }
             else
            {
                $el.on("keydown", onKeyDown);
                $el.on("keypress", onKeyPress);
                $el.on("keyup", onKeyUp);
            }
        });
    }

    $.fn.restrictInput = function(opts, regex)
    {
        var selector = '';

        if (opts !== undefined && opts.selector !== undefined)
            selector = opts.selector;

        function restrictChars(evt)
        {
            function isCharAllowed(keycode, _regex)
            {
                // the following are always allowed
                if (   keycode == 8                     // backspace
                    || keycode == 46                    // delete
                    || keycode == 9                     // tab
                    || keycode == 13                    // enter
                    || (keycode >= 35 && keycode <= 40 && !evt.shiftKey) // arrow keys/home/end
                   )
                {
                    return true;
                }

                // prevent the event for characters that aren't allowed
                var regexp = new RegExp(_regex),
                    c = String.fromCharCode(keycode);

                if (!regexp.test(c))
                    return false;

                return true;
            }

            // get the keycode of the key that was pressed
            var k = evt.keyCode || evt.which;
            if (!isCharAllowed(k, regex))
                evt.preventDefault();
        }

        return this.each(function() {
            if (selector.length > 0)
            {
                $(this).on("keypress", selector, restrictChars);
            }
             else
            {
                $(this).on("keypress", restrictChars);
            }
        });
    }

    $.fn.identifierInput = function(opts)
    {
        // allow alpha-numeric characters and underscores
        return $(this).restrictInput(opts, /^[0-9a-z\_]+$/gi);
    }

    $.fn.usernameInput = function(opts)
    {
        // allow alpha-numerics, underscores, periods, dashes and the @ symbol
        return $(this).restrictInput(opts, /^[0-9a-z\_\.\-\@]+$/gi);
    }

    $.fn.adjacent = function(selector)
    {
        var $el = $(this),
            $adj_el;

        $adj_el = $el.next(selector);
        if ($adj_el.length > 0)
            return $adj_el;

        $adj_el = $el.prev(selector);
        if ($adj_el.length > 0)
            return $adj_el;

        return $();
    }

    $.fn.prevUntil2 = function(selector, include_self, include_until)
    {
        var $el = $(this),
            $prev_el = $(),
            $ret_els = (include_self === true) ? $el : $();

        $prev_el = $el.prevAll(selector);
        if ($prev_el.length > 0)
        {
            if (include_until === true)
                return $ret_els.add($el.prevUntil($prev_el)).add($prev_el);
                 else
                return $ret_els.add($el.prevUntil($prev_el));
        }

        return $ret_els;
    }

    $.fn.nextUntil2 = function(selector, include_self, include_until)
    {
        var $el = $(this),
            $next_el = $(),
            $ret_els = (include_self === true) ? $el : $();

        $next_el = $el.nextAll(selector);
        if ($next_el.length > 0)
        {
            if (include_until === true)
                return $ret_els.add($el.nextUntil($next_el)).add($next_el);
                 else
                return $ret_els.add($el.nextUntil($next_el));
        }

        return $ret_els;
    }

    $.fn.adjacentUntil = function(selector, include_self, include_until)
    {
        var $el = $(this),
            $adj_el = $(),
            $ret_els = (include_self === true) ? $el : $();

        $adj_el = $el.nextAll(selector);
        if ($adj_el.length > 0)
            return $el.nextUntil2(selector, include_self, include_until);

        $adj_el = $el.prevAll(selector);
        if ($adj_el.length > 0)
            return $el.prevUntil2(selector, include_self, include_until);

        return $ret_els;
    }

    // set the focus to the specified input/textarea
    // and move the cursor to the end
    $.fn.focusEnd = function()
    {
        function caretEnd()
        {
            var el = this,
                $el = $(this),
                val = $el.val();

            el.selectionStart = val.length;
            el.selectionEnd = val.length;
            $el.focus();
        }

        return this.each(caretEnd);
    }

    // set the focus to the specified input/textarea
    // and select all text
    $.fn.focusSelectAll = function()
    {
        function selectAll()
        {
            var el = this,
                $el = $(this),
                val = $el.val();

            el.selectionStart = 0;
            el.selectionEnd = val.length;
            $el.focus();
        }

        return this.each(selectAll);
    }

    // set the focus to the specified child input/textarea
    $.fn.focusChild = function(focus_type, selector)
    {
        if (focus_type === undefined)
            focus_type = 'end';

        if (selector === undefined)
            selector = 'input[type=text], input[type=password], input[type=email], input[type=search], input[type=number], textarea, select';

        function setFocusChild()
        {
            var $el = $(this);

            var focus_fn = 'focusEnd';

            switch (focus_type)
            {
                default:
                case 'focus':     focus_fn = 'focus';          break;
                case 'end':       focus_fn = 'focusEnd';       break;
                case 'selectall': focus_fn = 'focusSelectAll'; break;
            }

            $el.find(selector).not('[type=hidden], .disabled, :disabled').first()[focus_fn]();
        }

        return this.each(setFocusChild);
    }

    // set the focus to the specified element after a certain
    // period of time (useful for fixing problems with IE8 focus)
    $.fn.deferredFocus = function(timeout)
    {
        if (timeout === undefined)
            timeout = 10;

        function deferFocus()
        {
            var $el = $(this);
            setTimeout(function() { $el.focus(); }, timeout);
        }

        return this.each(deferFocus);
    }

    $.fn.pageMessage = function(msg, cls, wait)
    {
        if (cls === undefined || cls.length == 0)
            cls = 'alert-info';

        if (wait === undefined)
            wait = 3000;

        function showMessage()
        {
            var $msg = $('' +
                '<div class="alert alert-dismissable pos-fill fx-square fx-alert '+cls+'" ' +
                    'style="top: -1px; bottom: auto; z-index: 1000">' +
                    msg +
                '</div>');

            $(this).find('.alert')
                   .remove()
                   .end()
                   .prepend($msg);

            setTimeout(function() {
                $msg.fadeOut(300, function() { $msg.remove(); });
            }, wait);
        }

        return this.each(showMessage);
    }

    // same as $.fn.show() except with 'visibility' CSS property
    $.fn.visible = function(val)
    {
        return this.each(function() {
            if (val === false)
                $(this).css('visibility', 'hidden');
                 else
                $(this).css('visibility', 'visible');
        });
    }

    // same as $.fn.hide() except with 'visibility' CSS property
    $.fn.invisible = function()
    {
        return this.each(function() {
            $(this).css('visibility', 'hidden');
        });
    }

    $.fn.enable = function(val, read_only)
    {
        var attr = (read_only === true) ? 'readonly' : 'disabled',
            cls = (read_only === true) ? '' : 'disabled';

        return this.each(function() {
            if (val === false)
                $(this).addClass(cls).attr(attr, '');
                 else
                $(this).removeClass(cls).removeAttr(attr);
        });
    }

    $.fn.disable = function(read_only)
    {
        return this.each(function() {
            $(this).enable(false, read_only);
        });
    }

    $.fn.formEnable = function(val, read_only) {
        return this.each(function() {
            $(this).find('input[type="text"], input[type="password"], input[type="checkbox"], select, textarea').enable(val, read_only);
        });
    }

    $.fn.formDisable = function(read_only) {
        return this.each(function() {
            $(this).formEnable(false, read_only);
        });
    }

    $.fn.formError = function(msg, cls)
    {
        cls = cls || '';

        function showError()
        {
            var $msg = $('' +
                '<div class="alert alert-danger alert-dismissable fx-alert '+cls+'">' +
                    '<button type="button" class="close" data-dismiss="alert" aria-label="Close">' +
                        '<span aria-hidden="true">&times;</span>' +
                    '</button>' +
                    msg +
                '</div>');

            $(this).find('.alert')
                   .remove()
                   .end()
                   .prepend($msg);
        }

        return this.each(showError);
    }

    $.fn.formMessage = function(msg, cls, wait)
    {
        if (cls === undefined || cls.length == 0)
            cls = 'alert-info';

        if (wait === undefined)
            wait = 3000;

        function showMessage()
        {
            var $msg = $('<div class="alert fx-alert '+cls+'">'+msg+'</div>');
            $(this).find('.alert')
                   .remove()
                   .end()
                   .prepend($msg);

            setTimeout(function() {
                $msg.fadeOut(300, function() { $msg.remove(); });
            }, wait);
        }

        return this.each(showMessage);
    }

    $.fn.formValidate = function(successCb, failCb)
    {
        var alert_cls = '';

        // handle options object as parameter
        if (_.isObject(successCb))
        {
            var opts = successCb;

            if (typeof opts.success == 'function')
                successCb = opts.success;
            if (typeof opts.error == 'function')
                failCb = opts.error;
            if (typeof opts.alertCls == 'string')
                alert_cls = opts.alertCls;
        }

        function validate()
        {
            var $form = $(this),
                msg = '';

            var $fields = $form.find('.form-group');

            var _getFieldname = function($el, $input)
            {
                var fieldname = $el.find('.control-label').text();

                // new BS 3.x way
                if (fieldname.length == 0)
                    fieldname = $el.find('label').text();

                // for forms where there is no field label
                if (fieldname.length == 0)
                    fieldname = $input.attr('placeholder');

                var error_fieldname = $input.data('fieldname');
                if (error_fieldname && error_fieldname.length > 0)
                    fieldname = error_fieldname;

                return fieldname;
            }

            // remove the old error list, if it exists
            $form.find('.ta-error-list').closest('.alert').remove();

            // check for required fields
            $fields.filter('[data-required], .fx-required').each(function() {
                var $el = $(this),
                    $input = $el.find('input[type="text"], input[type="password"], select, textarea');

                // select2 inputs are handled by their corresponding <select> element
                $input = $input.not('.select2-input, .select2-focusser, .select2-offscreen');

                var fieldname = _getFieldname($el, $input),
                    input_msg = '<li>' + Util.sprintf(_T("%p1 is required"), '<strong>'+fieldname+'</strong>') + '</li>';

                // text input, password input and textarea
                if ($input.length > 0 && $input.val().trim().length == 0)
                    msg += input_msg;

                // file input
                $input = $el.find('input[type="file"]');
                if ($input.length > 0 && $input.get(0).value == 0)
                    msg += input_msg;
            });

            // check for minimum length
            $fields.filter('[data-min-length]').each(function() {
                var $el = $(this),
                    $input = $el.find('input[type="text"], input[type="password"], textarea');

                var fieldname = _getFieldname($el, $input),
                    min_length = $el.data('min-length'),
                    input_msg = '<li>' + Util.sprintf(_T("%p1 must be at least %p2 characters"), '<strong>'+fieldname+'</strong>', min_length) + '</li>';

                if ($input.length > 0 && $input.val().length < min_length)
                    msg += input_msg;
            });

            // validate email addresses in select2 tag input
            var multi_email_error_found = false;
            $fields.filter('[data-multi-email]').each(function() {
                var $el = $(this),
                    $email_input = $el.find('.select2-container-multi'),
                    $email_items = $email_input.find('.select2-search-choice');

                // validate all entries for valid email addresses
                var errors_found = false;
                $email_items.removeClass('danger').each(function() {
                    var $item = $(this),
                        data = $item.data('select2-data'),
                        email = data.id;

                    if (!Util.validateEmail(email))
                    {
                        // add error state to any invalid email addresses
                        $item.addClass('danger');
                        errors_found = true;
                    }
                });

                if (errors_found === true && multi_email_error_found === false)
                {
                    msg += '<li>' + _T('One or more of the email addresses you entered are invalid') + '</li>';
                    multi_email_error_found = true; // only show one of these messages
                }
            });

            if (msg.length > 0)
                $form.formError('<ul class="list-unstyled ta-error-list">'+msg+'</ul>', alert_cls);

            if (successCb !== undefined && msg.length == 0)
                successCb();

            if (failCb !== undefined && msg.length > 0)
                failCb();
        }

        return this.each(validate);
    }

    // convert regular dropdown button into a <select> type dropdown
    $.fn.buttonSelect = function(input_name)
    {
        return this.each(function() {
            var $btn = $(this),
                $input = $();

            if (input_name === undefined)
                input_name = '';

            // create our input
            $input = $('<input type="hidden" name="'+input_name+'" value="" />').insertAfter($btn);

            function updateVal(text, val, title)
            {
                if ($btn.find('.btn-text').length > 0)
                    $btn.find('.btn-text').text(text);
                     else
                    $btn.text(text);

                $input.val(val);
                $btn.data('val', val);
                if (title === undefined)
                    $btn.removeAttr('title');
                     else
                    $btn.attr('data-original-title', title);
            }

            var $li = $btn.parent().find('li'),
                $selected_li = $li.filter('[selected]');

            // update button text and value based on the list item with the 'selected' attribute
            if ($selected_li.length > 0)
            {
                var $a = $selected_li.find('a'),
                    text = $a.find('.btn-text').text() || $a.text(),
                    title = $a.find('.btn-title').text() || undefined,
                    val = $a.data('val');

                updateVal(text, val, title);
            }

            // update button text and value based on which list item is clicked
            $li.click(function() {
                var $selected_li = $(this).attr('selected', true),
                    $a = $selected_li.find('a'),
                    text = $a.find('.btn-text').text() || $a.text(),
                    title = $a.find('.btn-title').text() || undefined,
                    val = $a.data('val');

                $selected_li.siblings('li').removeAttr('selected');

                updateVal(text, val, title);
            });
        });
    }

    $.fn.updateTitleAttr = function(title)
    {
        return this.each(function() {
            var $el = $(this);

            if (title === undefined)
                $el.removeAttr('title').removeAttr('data-original-title').removeData('original-title');
                 else
                $el.removeAttr('title').attr('data-original-title', title).data('original-title', title);
        });
    }

    $.fn.makeSquare = function()
    {
        return this.each(function() {
            var $el = $(this),
                w = $el.width(),
                h = $el.height(),
                s = Math.max(w, h);

            $el.width(s).height(s);
        });
    }

    $.fn.positionCenter = function()
    {
        return this.each(function() {
            var $el = $(this),
                w = $el.outerWidth(),
                h = $el.outerHeight();

            $el.css({
                'position': 'absolute',
                'top': '50%',
                'left': '50%',
                'margin-left': Math.ceil(-1*w/2)+'px',
                'margin-top': Math.ceil(-1*h/2)+'px'
            });
        });
    }

    $.fn.dragHover = function(opts)
    {
        var selector = '';

        if (opts !== undefined && opts.selector !== undefined)
            selector = opts.selector;

        return this.each(function() {
            var $el = $(this),
                $collection = $();

            if (selector.length > 0)
            {
                $el.on('dragenter', selector, function(evt) {
                    if ($collection.length === 0)
                        $(this).trigger('draghoverstart');

                    $collection = $collection.add(evt.target);
                });

                $el.on('dragleave drop', selector, function(evt) {
                    $collection = $collection.not(evt.target);

                    if ($collection.length === 0)
                        $(this).trigger('draghoverend');
                });
            }
             else
            {
                $el.on('dragenter', function(evt) {
                    if ($collection.length === 0)
                        $el.trigger('draghoverstart');

                    $collection = $collection.add(evt.target);
                });

                $el.on('dragleave drop', function(evt) {
                    $collection = $collection.not(evt.target);

                    if ($collection.length === 0)
                        $el.trigger('draghoverend');
                });
            }
        });
    }

    $.fn.multiTagInput = function(options)
    {
        // make sure we have options
        options = _.extend({}, options);

        return this.each(function() {
            var $input = $(this),
                select2_instance;

            // default separators
            var separators = [' ',',',';','|','\t','\n'];

            if ($.isArray(options.tokenSeparators))
                separators = options.tokenSeparators;

            var paste_separators = _.without(separators, '\n');

            var default_opts = {
                tags: [],
                tokenSeparators: separators,
                dropdownCssClass: 'hidden',
                // this option requires the drop mask to be visible, which we
                // don't want; solution is to select highlighted on blur below
                //selectOnBlur: true,
                tokenizer: function(input, selection, callback) {
                    // custom tokenizer allows copy/paste of items to work properly
                    var found = false;
                    _.each(paste_separators, function(s) {
                        if (input.indexOf(s) >= 0)
                            found = true;
                    });

                    if (!found)
                        return;

                    // trim and fix any badly formatted values (for copy/paste scenarios)
                    _.each(paste_separators, function(s) {
                        if (s == ' ')
                            return;

                        var regex = new RegExp(Util.escapeRegExp(s), 'g');
                        input = input.replace(regex, ' ');
                    });

                    // now trim extra whitespace (as a failsafe)
                    input = input.replace(/\s\s+/g, ' ');
                    input = input.trim();

                    // make our list a comma-delimited list
                    input = input.replace(/ /g, ',');

                    var found = false,
                        parts = input.split(',');

                    // disregard any duplicate items that are in the parts array
                    parts = _.uniq(parts);

                    // try to add each item, always being sure to check
                    // for items that already exist with the same id
                    // as the one we're trying to add
                    _.each(parts, function(part) {
                        var p = part.trim();

                        var _found = _.find(selection, function(s) {
                            return s.id.toUpperCase() === p.toUpperCase();
                        });

                        if (_found)
                            found = true;
                             else
                            callback({ id: p, text: p });
                    });

                    // clear out input if the item is a duplicate
                    if (found)
                        return '';
                }
            };

            var opts = $.extend({}, default_opts, options);

            // these options are for this plugin
            // and should not be passed to the select2
            opts = _.omit(opts, 'useDropMask');

            // create the select2 control
            $input.select2(opts);

            // store reference to select2 instance
            select2_instance = $input.data('select2');

            if (options.useDropMask !== true)
            {
                $input.on('select2-open', function() {
                    // remove all vestiges of a drop mask for this control
                    $('#select2-drop-mask').remove();

                    // make sure the select2 plugin thinks we're always closed
                    // (since we don't ever show a dropdown for this control)
                    $(this).select2('container').removeClass('select2-dropdown-open');
                });

                $input.on('select2-blur', function() {
                    // select2 needs to "think" we're open for this to work...
                    select2_instance.selectHighlighted({ noFocus: true });
                    select2_instance.removeHighlight();

                    // make sure we don't leave these classes around (even though it shouldn't hurt)
                    $(this).select2('container').removeClass('select2-dropdown-open select2-container-active');
                });

                // hiding the dropdown makes <enter> keydown functionality fail
                // in the select2 plugin since there are no "selected" items
                // in the dropdown, so handle adding the selected item here
                select2_instance.search.on('keydown', function(evt) {
                    if (evt.which != 13)
                        return;

                    // select2 needs to "think" we're open for this to work...
                    select2_instance.selectHighlighted({ noFocus: true });
                    select2_instance.removeHighlight();

                    // make sure we don't leave these classes around (even though it shouldn't hurt)
                    $(this).select2('container').removeClass('select2-dropdown-open select2-container-active');

                    evt.preventDefault();
                    evt.stopPropagation();
                });
            }

            // old, problematic way of making the select2 think we're always closed
            //select2_instance.opened = function() { return false; }

            // don't allow first character in search string to be a token
            select2_instance.search.on('keypress', function(evt) {
                if (this.value.length == 0)
                {
                    var c = String.fromCharCode(evt.which);
                    if (_.indexOf(select2_instance.opts.tokenSeparators, c) != -1)
                        evt.preventDefault();
                }
            });
        });
    }

    $.fn.matchWidth = function(options)
    {
        options = $.extend({}, options);

        var w = 0;
        this.each(function() {
            w = Math.max(w, $(this).css('width', 'auto').outerWidth());
        });

        if (typeof options.min == 'number')
            w = Math.max(options.min, w);

        if (typeof options.max == 'number')
            w = Math.min(options.max, w);

        if (typeof options.pad == 'number')
            w += options.pad;

        options['final'] = w;

        return this.each(function() {
            $(this).outerWidth(w);
        });
    }

    $.fn.matchHeight = function(options)
    {
        options = $.extend({}, options);

        var h = 0;
        this.each(function() {
            h = Math.max(h, $(this).css('height', 'auto').outerHeight());
        });

        if (typeof options.min == 'number')
            h = Math.max(options.min, h);

        if (typeof options.max == 'number')
            h = Math.min(options.max, h);

        if (typeof options.pad == 'number')
            h += options.pad;

        options['final'] = h;

        return this.each(function() {
            $(this).outerHeight(h);
        });
    }

    $.getScrollbarSize = function()
    {
        var $el = $('<div style="width: 100px; height: 100px; overflow: scroll; position: absolute; top: -2000px; left: -2000px;">&nbsp;</div>').appendTo('body');
        var el = $el.get(0);
        var w = el.offsetWidth - el.clientWidth;
        var h = el.offsetHeight - el.clientHeight;
        $el.remove();
        return { 'width': w, 'height': h };
    }

    $.fn.isInDom = function() {
        return $.contains(document, this[0]) ? true : false;
    }

    // performs a regular (non-AJAX) form submit
    $.formSubmit = function(action, method, target, params)
    {
        method = method.toUpperCase();

        var $form = $('<form>', {
            action: action,
            method: method,
            target: target,
            style: 'display: none;'
        });

        if (params !== undefined)
        {
            $.each(params, function (key, val) {
                $('<input>', {
                    type: 'text',
                    name: key,
                    value: val
                }).appendTo($form);
            });
        }

        $form.appendTo('body').submit();
        $form.remove();
    }

    $.getHumanIdle = function()
    {
        return window.human_idle;
    }

    $.startHumanIdleTimer = function(idle_start, interval)
    {
        // default to 5 minutes to detect human idle
        if (idle_start === undefined)
            idle_start = 5;

        // default to a 10 minute interval
        if (interval === undefined)
            interval = 10;

        window.human_idle = 0;

        var idle_interval,
            $win = $(window),
            $doc = $(document);

        function incrementIdle()
        {
            window.human_idle = window.human_idle + interval;

            if (window.human_idle == idle_start)
                $win.trigger($.Event('starthumanidle'), [window.human_idle, interval]);

            if (window.human_idle >= idle_start)
                $win.trigger($.Event('humanidle'), [window.human_idle, interval]);
        }

        function resetIdle()
        {
            if (window.human_idle >= idle_start)
                $win.trigger($.Event('endhumanidle'), [window.human_idle, interval]);

            window.human_idle = 0;

            // clear out the onld interval and start another one
            clearInterval(idle_interval);
            idle_interval = setInterval(incrementIdle, interval*60000);
        }

        // increment idle counter
        idle_interval = setInterval(incrementIdle, interval*60000);

        // zero the idle timer on mouse movement
        $doc.mousemove(function (evt) {
            resetIdle();
        });

        // zero the idle timer on key press
        $doc.keypress(function (evt) {
            resetIdle();
        });
    }

    $.encodeHtml = function(val)
    {
        if (val) {
            return $('<div />').text(val).html();
        } else {
            return '';
        }
    }

    $.decodeHtml = function(val)
    {
        if (val) {
            return $('<div />').html(val).text();
        } else {
            return '';
        }
    }

    $.globalStyle = function(id, style_str)
    {
        var $style = $('#'+id);

        if (!style_str)
        {
            // no style specified; remove existing style
            $style.remove();
            return $();
        }

        if ($style.length > 0)
        {
            // the style already exists; update its contents
            $style.text(style_str);
        }
         else
        {
            // the style does not exist; create it and append to <head>
            var style = '<style id="'+id+'" type="text/css">'+style_str+'</style>';
            $style = $(style).appendTo('head');
        }

        return $style;
    }

})(jQuery, window, document);
