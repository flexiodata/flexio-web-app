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
        this.tryInitialFetch()
      }
    },
    data() {
      return {
        start: 0,
        limit: 100,
        columns: [],
        rows: []
      }
    },
    computed: {
      fetch_url() {
        return this.contentUrl+'?metadata=true&start='+this.start+'&limit=+'+this.limit
      }
    },
    mounted() {
      this.tryInitialFetch()
    },
    methods: {
      tryInitialFetch() {
        var me = this
        $.getJSON(this.fetch_url).then(response => {
          me.columns = response.columns
          me.rows = response.rows
        }, response => {

        })
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
