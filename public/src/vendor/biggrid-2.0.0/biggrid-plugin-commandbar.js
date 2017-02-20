/*
 * Big Grid Plugin - Control Bar
 * Copyright 2010-2011, Gold Prairie, Inc.
 */

;(function($, window, document, undefined) {
    'use strict';

    // translate token function
    function _T(s) { return s; }

    biggrid.plugin.commandbar = function(options)
    {
        this._generateUid = function()
        {
            return (Math.random().toString(36).substring(2) + Math.random().toString(36).substring(2)).substring(0,16);
        }

        // unique identifier and namespace
        var uid = this._generateUid(),
            ns = 'bgg-commandbar-'+uid;

        var me = this,
            opts = $.extend({}, biggrid.plugin.commandbar.defaults, options),
            grid = undefined,
            $el = $(),
            $grid_el = $(),
            $grid_utility_bar = $();

// -- public methods --

        this.init = function(biggrid)
        {
            grid = biggrid;
            $grid_el = grid.getElement();
            $grid_utility_bar = $grid_el.find('.bgg-utility-bar');

            me._render();
            me._initEvents();

            return me;
        }

        this.destroy = function()
        {
            $el.remove();
            $el = $();

            return me._uninitEvents();
        }

        this.getName = function()
        {
            return 'commandbar';
        }

        this.register = function()
        {
            if (grid instanceof $.biggrid)
                grid.registerPlugin('commandbar', me);

            return me;
        }

        this.unregister = function()
        {
            if (grid instanceof $.biggrid)
                grid.unregisterPlugin('commandbar');

            return me;
        }

// -- private methods --

        this._initEvents = function()
        {
            me._uninitEvents();

            //$el.find('.fx-btn-workspace-navigate-back').on('click.'+ns,    function(evt) { $grid_el.trigger($.Event('navigatebackclick.biggrid'));          });
            //$el.find('.fx-btn-workspace-navigate-forward').on('click.'+ns, function(evt) { $grid_el.trigger($.Event('navigateforwardclick.biggrid'));       });
            //$el.find('.fx-btn-workspace-refresh').on('click.'+ns,          function(evt) { $grid_el.trigger($.Event('refreshclick.biggrid'));               });
            $el.find('.fx-btn-workspace-sort').on('click.'+ns,             function(evt) { $grid_el.trigger($.Event('sortclick.biggrid'));                  });
            $el.find('.fx-btn-workspace-filter').on('click.'+ns,           function(evt) { $grid_el.trigger($.Event('filterclick.biggrid'));                });
            $el.find('.fx-btn-workspace-group').on('click.'+ns,            function(evt) { $grid_el.trigger($.Event('groupclick.biggrid'));                 });
            //$el.find('.fx-btn-workspace-define').on('click.'+ns,           function(evt) { $grid_el.trigger($.Event('defineclick.biggrid'));                });
            //$el.find('.fx-btn-workspace-import').on('click.'+ns,           function(evt) { $grid_el.trigger($.Event('importclick.biggrid'));                });
            //$el.find('.fx-btn-workspace-export').on('click.'+ns,           function(evt) { $grid_el.trigger($.Event('exportclick.biggrid'));                });
            //$el.find('.fx-btn-workspace-email').on('click.'+ns,            function(evt) { $grid_el.trigger($.Event('emailclick.biggrid'));                 });
            $el.find('.fx-btn-workspace-format').on('click.'+ns,           function(evt) { $grid_el.trigger($.Event('conditionalformattingclick.biggrid')); });
            $el.find('.fx-btn-workspace-custom-job').on('click.'+ns,       function(evt) { $grid_el.trigger($.Event('customjobclick.biggrid'));             });

            return me;
        }

        this._uninitEvents = function()
        {
            $el.off('.'+ns);

            return me;
        }

        this._render = function()
        {
            $el = $(opts.markup);

            if (opts.appendTo !== undefined)
                $(opts.appendTo).append($el);
                 else
                $grid_utility_bar.append($el.addClass('bgg-utility-bar-inner'));

            return me;
        }

// -- event handlers --

    }

    biggrid.plugin.commandbar.defaults = {
        appendTo: undefined,
        markup: '' +
            '<div class="bgg-commandbar">' +
                '<form class="form">' +
                    '<div class="input-group">' +
                        '<div class="input-group-btn">' +
                            '<button type="button" class="btn btn-default btn-lg fx-btn-workspace-undo tipsy" title="'+_T('Undo')+'" data-placement="bottom"><span class="fa fa-arrow-left"></span></button>' +
                        '</div>' +
                        '<div class="input-group-btn">' +
                            '<button type="button" class="btn btn-default btn-lg fx-btn-workspace-redo tipsy" title="'+_T('Redo')+'" data-placement="bottom"><span class="fa fa-arrow-right"></span></button>' +
                        '</div>' +
                        '<div class="input-group-btn hidden">' +
                            '<button type="button" class="btn btn-default btn-lg fx-btn-workspace-navigate-back tipsy" title="'+_T('Back')+'" data-placement="bottom"><span class="fa fa-arrow-left"></span></button>' +
                        '</div>' +
                        '<div class="input-group-btn hidden">' +
                            '<button type="button" class="btn btn-default btn-lg fx-btn-workspace-navigate-forward tipsy" title="'+_T('Forward')+'" data-placement="bottom"><span class="fa fa-arrow-right"></span></button>' +
                        '</div>' +
                        '<div class="input-group-btn hidden">' +
                            '<button type="button" class="btn btn-default btn-lg fx-btn-workspace-refresh tipsy" title="'+_T('Refresh')+'" data-placement="bottom"><span class="el-icon el-icon-refresh" style="top: 0; font-size: 16px"></span></button>' +
                        '</div>' +
                        '<div class="input-group-btn">' +
                            '<button type="button" class="btn btn-default btn-lg dropdown-toggle" data-toggle="dropdown"><span class="glyphicon glyphicon-menu-hamburger"></span></button>' +
                            '<ul class="dropdown-menu bgg-dropdown-menu bgg-caret-dropdown-menu bgg-commandbar-dropdown-menu" style="margin-left: -5px">' +
                                '<li><a data-anchor="btn" href="#" class="fx-btn-workspace-sort"><span class="fa fa-fw fa-sort-alpha-asc"></span> '+_T('Sort...')+'</a></li>' +
                                '<li><a data-anchor="btn" href="#" class="fx-btn-workspace-filter"><span class="fa-fw glyphicon glyphicon-filter"></span> '+_T('Filter...')+'</a></li>' +
                                '<li class="divider"></li>' +
                                '<li><a data-anchor="btn" href="#" class="fx-btn-workspace-group"><span class="fa fa-fw fa"></span> '+_T('Summarize...')+'</a></li>' +
                                //'<li><a data-anchor="btn" href="#" class="fx-btn-workspace-define"><span class="fa fa-fw glyphicon glyphicon-pencil"></span> '+_T('Define...')+'</a></li>' +
                                //'<li class="divider"></li>' +
                                //'<li><a data-anchor="btn" href="#" class="fx-btn-workspace-import"><span class="fa fa-fw glyphicon glyphicon-import"></span> '+_T('Import...')+'</a></li>' +
                                //'<li><a data-anchor="btn" href="#" class="fx-btn-workspace-export"><span class="fa fa-fw glyphicon glyphicon-export"></span> '+_T('Export...')+'</a></li>' +
                                //'<li><a data-anchor="btn" href="#" class="fx-btn-workspace-email"><span class="fa fa-fw fa-envelope"></span> '+_T('Email...')+'</a></li>' +
                                //'<li class="divider"></li>' +
                                //'<li><a data-anchor="btn" href="#" class="fx-btn-workspace-format"><span class="fa-fw el-icon el-icon-brush"></span> '+_T('Conditional Formatting...')+'</a></li>' +
                                //'<li><a data-anchor="btn" href="#" class="fx-btn-workspace-custom-job"><span class="fa-fw fa"></span> '+_T('Custom Job...')+'</a></li>' +
                            '</ul>' +
                        '</div>' +
                        '<input type="text" class="form-control bgg-commandbar-input invisible" />' +
                        '<span class="input-group-btn bgg-commandbar-input-group-submit-cancel-btn hidden">' +
                            '<button type="button" class="btn btn-sm btn-default fx-btn-workspace-formula-cancel">'+_T('Cancel')+'</button>' +
                            '<button type="button" class="btn btn-sm btn-primary fx-btn-workspace-formula-submit">'+_T('Save')+'</button>' +
                        '</span>' +
                    '</div>' +
                '</form>' +
            '</div>'
    };

})(jQuery, window, document);
