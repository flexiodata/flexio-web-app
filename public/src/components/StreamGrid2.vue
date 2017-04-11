<template>
  <div
    class="flex flex-column relative h-100 sg-container"
    @mousedown="onMousedown"
    @mouseup="onMouseup"
    @mousemove="onMousemove"
  >
    <div class="flex-none overflow-hidden bg-near-white">
      <div
        ref="thead-tr"
        class="flex flex-row nowrap relative"
      >
        <div class="flex-none db overflow-hidden ba bg-near-white tc relative sg-th" v-for="c in columns">
          <div class="db lh-1 sg-th-inner" :style="'width: '+c.pixel_width+'px'">{{c.name}}</div>
          <div
            class="absolute top-0 bottom-0 right-0 cursor-resize-ew"
            style="width: 4px"
            @mousedown="onColumnResizerMousedown(c)"
          ></div>
        </div>
      </div>
    </div>
    <div
      ref="tbody"
      class="flex-fill overflow-auto"
      @scroll="onScroll"
    >
      <div class="flex flex-row nowrap" v-for="r in rows">
        <div class="flex-none db overflow-hidden ba sg-td" v-for="c in columns">
          <div class="db lh-1 sg-td-inner" :style="'width: '+c.pixel_width+'px'">{{r[c.name]}}</div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
  import $ from 'jquery'

  const default_column_info = {
    pixel_width: 130
  }

  export default {
    props: ['stream-eid', 'content-url', 'task-json'],
    watch: {
      streamEid() {
        this.tryFetch()
      }
    },
    data() {
      return {
        inited: false,

        start: 0,
        limit: 100,
        total_row_count: 0,

        columns: [],
        resize_col: null,
        rows: [],
        cached_rows: {},

        scroll_top: 0,
        scroll_left: 0,

        mousedown_x: 0,
        mousedown_y: 0,
        mouse_x: 0,
        mouse_y: 0
      }
    },
    computed: {
      fetch_url() {
        var url = this.contentUrl+'?start='+this.start+'&limit=+'+this.limit
        return this.inited ? url : url + '&metadata=true'
      },
      resize_delta() {
        return this.mousedown_x == -1 ? 0 : this.mouse_x - this.mousedown_x
      }
    },
    mounted() {
      this.tryFetch()
    },
    methods: {
      tryFetch() {
        if (this.tryRowCache())
          return

        var me = this
        $.getJSON(this.fetch_url).then(response => {
          me.total_row_count = response.total_count

          // store our column info
          if (!me.inited && _.isArray(response.columns))
          {
            // include default column info with each column
            var temp_cols = _.map(response.columns, (col) => {
              return _.assign({}, default_column_info, col)
            })

            me.columns = [].concat(temp_cols)
          }

          // store the current set of rows
          me.rows = [].concat(response.rows)

          // cache the current set of rows
          var start = me.start
          var limit = me.limit
          var row_count = me.total_row_count
          var temp_cached_rows = {}
          var idx = 0

          for (var r = start; r < start+limit && r < row_count; ++r)
          {
            temp_cached_rows[r] = me.rows[idx]
            idx++
          }

          // add the temporary cached rows to our stored cached rows
          me.cached_rows = _.assign({}, me.cached_rows, temp_cached_rows)

          // set our init flag to true so we don't get columns after this
          me.inited = true
        }, response => {

        })
      },

      tryRowCache() {
        if (!this.inited)
          return false

        var missing_rows = false
        var start = this.start
        var limit = this.limit
        var row_count = this.total_row_count
        var temp_rows = []

        for (var r = start; r < start+limit && r < row_count; ++r)
        {
          if (this.cached_rows.hasOwnProperty(r))
          {
            temp_rows.push(this.cached_rows[r])
          }
           else
          {
            missing_rows = true
            break
          }
        }

        // let the callee know that we were missing at least one row
        if (missing_rows)
          return false

        // update our current set of rows
        this.rows = [].concat(temp_rows)

        // let the callee know we found all of the rows in the row cache
        return true
      },

      onMousedown(evt) {
        this.mousedown_x = evt.pageX
        this.mousedown_y = evt.pageY
      },

      onColumnResizerMousedown(col) {
        this.resize_col = _.cloneDeep(col)
      },

      onMouseup(evt) {
        this.mousedown_x = -1
        this.mousedown_y = -1
        this.resize_col = null
      },

      onMousemove(evt) {
        this.mouse_x = evt.pageX
        this.mouse_y = evt.pageY

        if (!_.isNil(this.resize_col))
          this.resizeColumn()
      },

      onScroll: _.throttle(function(evt) {
        // vertical scrolls
        if (this.scroll_top != this.$refs['tbody'].scrollTop)
        {
          this.scroll_top = this.$refs['tbody'].scrollTop
        }

        // horizontal scrolls
        if (this.scroll_left != this.$refs['tbody'].scrollLeft)
        {
          this.scroll_left = this.$refs['tbody'].scrollLeft

          // sync up fixed header with content horizontal scroll offset
          this.$refs['thead-tr'].style = 'left: -'+this.scroll_left+'px'
        }
      }, 10),

      resizeColumn: _.throttle(function(evt) {
        var lookup_col = _.find(this.columns, { name: _.get(this.resize_col, 'name') })
        if (!_.isNil(lookup_col))
        {
          var temp_cols = _.map(this.columns, (col) => {
            if (_.get(col, 'name') == _.get(lookup_col, 'name'))
            {
              var old_width = _.get(this.resize_col, 'pixel_width', 120)
              return _.assign({}, lookup_col, { pixel_width: old_width + this.resize_delta })
            }

            return col
          })

          this.columns = [].concat(temp_cols)
        }
      }, 20)
    }
  }
</script>

<style lang="less">
  .sg-container {
    font-family: Arial, sans-serif;
    font-size: 13px;
    color: #333;
  }

  .sg-th,
  .sg-td {
    height: 24px;
    margin-top: -1px;
    margin-left: -1px;
    border-color: #ddd;
  }

  .sg-th-inner,
  .sg-td-inner {
    min-width: 30px;
    max-width: 1200px;
    padding: 5px 5px 4px;
  }
</style>
