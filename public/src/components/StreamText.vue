<template>
  <div v-if="state.is_fetching && !state.has_fetched">
    <spinner size="medium" show-text :loading-text="state.loading_text"></spinner>
  </div>
  <code-editor
    class="h-100 overflow-y-auto"
    lang="application/json"
    :val="state.text"
    :options="{ lineNumbers: false }"
    v-else-if="isJson"
  ></code-editor>
  <pre v-else class="monospace ma0 pa2 h-100 overflow-auto" @scroll="onScroll">{{state.text}}</pre>
</template>

<script>
  import $ from 'jquery'
  import Spinner from './Spinner.vue'
  import CodeEditor from './CodeEditor.vue'

  var initial_data = {
    is_fetching: false,
    has_fetched: false,
    loading_text: 'Loading text...',
    total_fetched: 0,
    chunk_size: 100000,
    text_scroll_top: 0,
    eof: false,
    text: ''
  }

  export default {
    props: ['stream-eid', 'content-url', 'is-json'],
    components: {
      Spinner,
      CodeEditor
    },
    data() {
      return {
        state: _.extend({}, initial_data)
      }
    },
    watch: {
      streamEid() {
        this.initialFetch()
      }
    },
    mounted() {
      this.initialFetch()
    },
    methods: {
      initialFetch() {
        this.state = _.extend({}, initial_data)

        if (this.isJson)
          this.fetchAll()
           else
          this.fetchChunk()
      },
      fetchAll() {
        return this.fetchChunk(true)
      },
      fetchChunk(fetch_all) {
        var me = this
        var state = this.state

        var fetch_url = this.contentUrl
        var fetch_opts = {}

        if (fetch_all !== true)
        {
          fetch_opts = {
            start: state.total_fetched,
            limit: state.chunk_size,
            format: 'data'
          }
        }

        state.is_fetching = true

        $.get(fetch_url, fetch_opts).then(response => {
          var str = response

          if (fetch_all === true || str.length < me.chunk_size)
              me.state.eof = true

          me.state.total_fetched += str.length

          if (_.isObject(str))
          {
            str = JSON.stringify(str, null, 2)
          }
           else
          {
            // since we're outputting raw text in a <pre> tag,
            // I don't think we want to escape the text here
            //str = _.escape(str)
            str = str.replace(/\r/g, '\r\n');
            str = str.replace(/\r\n\n/g, '\r\n');
          }

          state.text += str

          state.is_fetching = false
          state.has_fetched = true
        }, response => {
          state.is_fetching = false
          state.has_fetched = true
        })
      },
      onScroll(evt) {
        var state = this.state

        if (state.is_fetching)
          return

        var scroll_top = evt.target.scrollTop
        var scroll_height = evt.target.scrollHeight
        var cli_height = evt.target.clientHeight

        // only handle vertical scrolling
        if (scroll_top == state.text_scroll_top)
          return

        state.text_scroll_top = scroll_top

        if (state.eof === true)
          return

        if (scroll_top+cli_height > scroll_height*0.75)
          this.fetchChunk()
      }
    }

  }
</script>

<style lang="less" scoped>
  pre {
    line-height: 1.3;
  }
</style>
