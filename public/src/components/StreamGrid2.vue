<template>
  <div class="flex flex-column relative sg-container h-100">
    <div class="flex-none overflow-hidden sg-thead">
      <div class="flex flex-row nowrap sg-tr">
        <div class="flex-none db overflow-hidden ba pa1 bg-near-white tc sg-th" v-for="c in columns">
          {{c.name}}
        </div>
      </div>
    </div>
    <div class="flex-fill overflow-auto sg-tbody">
      <div class="flex flex-row nowrap sg-tr" v-for="r in rows">
        <div class="flex-none db overflow-hidden ba pa1 sg-td" v-for="c in columns">
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
        cached_rows: {}
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
      }
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
    margin-top: -1px;
    margin-left: -1px;
    border-color: #ddd;
  }
</style>
