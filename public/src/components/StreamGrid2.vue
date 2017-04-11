<template>
  <div class="flex flex-column relative h-100 sg-container">
    <div class="flex-none overflow-hidden bg-near-white">
      <div
        ref="thead-tr"
        class="flex flex-row nowrap relative"
      >
        <div class="flex-none db overflow-hidden ba ph1 bg-near-white tc sg-th" v-for="c in columns">
          {{c.name}}
        </div>
      </div>
    </div>
    <div
      ref="tbody"
      class="flex-fill overflow-auto"
      @scroll="onScroll"
    >
      <div class="flex flex-row nowrap" v-for="r in rows">
        <div class="flex-none db overflow-hidden ba ph1 sg-td" v-for="c in columns">
          {{r[c.name]}}
        </div>
      </div>
    </div>
  </div>
</template>

<script>
  import $ from 'jquery'

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
        rows: [],
        cached_rows: {},

        scroll_top: 0,
        scroll_left: 0
      }
    },
    computed: {
      fetch_url() {
        var url = this.contentUrl+'?start='+this.start+'&limit=+'+this.limit
        return this.inited ? url : url + '&metadata=true'
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
            me.columns = [].concat(response.columns)

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

        console.log(this.$refs)
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
      }, 10)
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
    width: 120px;
    height: 24px;
    padding: 5px 4px 4px;
    margin-top: -1px;
    margin-left: -1px;
    border-color: #ddd;
  }
</style>
