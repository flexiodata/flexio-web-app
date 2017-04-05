<template>
    <div class="relative w-100 h-100 overflow-hidden"></div>
</template>

<script>
  import '../vendor/underscore.util.js'
  import '../vendor/jquery.util.js'

  import '../vendor/appgridmodel.js'
  import '../vendor/biggrid-2.0.0/biggrid.js'
  import '../vendor/biggrid-2.0.0/biggrid-plugin-defaultdropdown.js'
  import '../vendor/biggrid-2.0.0/biggrid-plugin-columnhighlight.js'
  //import '../vendor/biggrid-2.0.0/biggrid-plugin-columndragreorder.js'
  //import '../vendor/biggrid-2.0.0/biggrid-plugin-columnlist.js'
  //import '../vendor/biggrid-2.0.0/biggrid-scrollbars.js'

  import {
    TASK_TYPE_DISTINCT,
    TASK_TYPE_FILTER,
    TASK_TYPE_FIND_REPLACE,
    TASK_TYPE_GROUP,
    TASK_TYPE_RENAME,
    TASK_TYPE_SEARCH,
    TASK_TYPE_SELECT,
    TASK_TYPE_SORT,
    TASK_TYPE_TRANSFORM
  } from '../constants/task-type'

  export default {
    props: ['stream-eid', 'content-url', 'task-json'],
    watch: {
      streamEid() {
        this.tryFetchGrid()
      }
    },
    mounted() {
      this.tryFetchGrid()
    },
    methods: {
      tryFetchGrid() {
        var me = this

        this.grid_view = {}

        this.grid_model = new window.FxAppGridModel({
          rowsUrl: this.contentUrl,
          colsUrl: this.contentUrl
        })

        this.grid_model.getColumns({
          callback: function(res) {
            me.renderGrid(me.grid_model, me.grid_view)
          }
        })
      },
      renderGrid(model, view) {
        var grid_opts = {
          model: this.grid_model,
          showLoading: false,
          allowColumnRename: false, // until we can reconcile display name vs. field name, this will cause problems with calculated field references
          allowColumnDropdown: false,
          colInvalidWidth: 100,
          loadingOverlayText: 'Loading...'
        }

        this.grid = $(this.$el).biggrid(grid_opts).data('biggrid')
        this.initColumnHighlight()
      },
      initColumnHighlight() {
        if (!_.isObject(this.taskJson))
          return

        var cols = {}
        var style_obj = {}

        var COLOR_WHITE = '#fff'
        var COLOR_GREEN = '#65c16f'
        var COLOR_ORANGE = '#f4823a'

        var task_type = _.get(this.taskJson, 'type')
        var task_params = _.get(this.taskJson, 'params', {})

        switch (task_type)
        {
          case TASK_TYPE_DISTINCT:
            style_obj = { title: 'Remove Duplicates', bg_color: COLOR_GREEN, color: COLOR_WHITE }
            cols = _.keyBy(task_params.distinct)
            break

          // in flux
          /*
          case TASK_TYPE_FILTER:
            style_obj = { title: 'Filter', bg_color: COLOR_GREEN, color: COLOR_WHITE }
            cols = _.map(task_params.condition.items, 'left')
            cols = _.flatten(cols)
            cols = _.keyBy(cols)
            break
          */

          case TASK_TYPE_FIND_REPLACE:
            style_obj = { title: 'Replace', bg_color: COLOR_ORANGE, color: COLOR_WHITE }
            cols = _.keyBy(task_params.columns)
            break

          /*
          // this works, but because we don't have an edit mode yet,
          // we can't see anything we're doing because
          // the output column names are different
          case TASK_TYPE_GROUP:
            cols = task_params.columns
            cols = _.map(cols, function(c) {
              return _.map(c.columns, function(c2) {
                return {
                  'name': c2,
                  'function': c['function']
                }
              })
            })
            cols = _.flatten(cols)
            cols = _.keyBy(cols, 'name')
            cols = _.mapObject(cols, function(c) {
              return {
                title: c['function'],
                bg_color: COLOR_ORANGE,
                color: COLOR_WHITE
              }
            })
            break
          */

          case TASK_TYPE_RENAME:
            style_obj = { title: 'Rename', bg_color: COLOR_ORANGE, color: COLOR_WHITE }
            cols = _.keyBy(task_params.columns, 'name')
            break

          case TASK_TYPE_SEARCH:
            style_obj = { title: 'Search', bg_color: COLOR_GREEN, color: COLOR_WHITE }
            cols = _.keyBy(task_params.columns)
            break

          case TASK_TYPE_SELECT:
            // no styling
            break

          case TASK_TYPE_SORT:
            style_obj = { title: 'Sort', bg_color: COLOR_GREEN, color: COLOR_WHITE }
            cols = _.keyBy(task_params.order, 'expression')
            break

          case TASK_TYPE_TRANSFORM:
            style_obj = { title: 'Transform', bg_color: COLOR_ORANGE, color: COLOR_WHITE }
            cols = _.keyBy(task_params.columns)
            break
        }

        if (_.isEmpty(cols))
        {
          if (this.grid.hasPlugin('columnhighlight'))
            this.grid.unregisterPlugin('columnhighlight')
        }
         else
        {
          if (!this.grid.hasPlugin('columnhighlight'))
            this.grid.registerPlugin('columnhighlight', new biggrid.plugin.columnhighlight())

          // map the style object to each column
          _.each(cols, function(col, key, collection) {
            collection[key] = style_obj
          })

          this.grid.getPlugin('columnhighlight').setColumns(cols)
        }
      }
    }
  }
</script>
