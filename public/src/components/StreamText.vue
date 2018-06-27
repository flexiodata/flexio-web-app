<template>
  <div
    class="flex flex-column justify-center h-100"
    v-if="state.is_fetching && !state.has_fetched"
  >
    <Spinner size="large" :message="state.loading_text" />
  </div>
  <CodeEditor
    class="h-100 overflow-auto"
    lang="text/html"
    :options="{
      lineNumbers: false,
      readOnly: true
    }"
    v-model="state.text"
    v-else-if="isHtml"
  />
  <CodeEditor
    class="h-100 overflow-auto"
    lang="json"
    :show-json-view-toggle="false"
    :options="{
      lineNumbers: false,
      readOnly: true
    }"
    v-model="state.text"
    v-else-if="isJson"
  />
  <pre
    class="monospace ma0 pa2 h-100 overflow-auto"
    @scroll="onScroll"
    v-else
  >{{state.text}}</pre>
</template>

<script>
  import axios from 'axios'
  import Spinner from 'vue-simple-spinner'
  import CodeEditor from './CodeEditor.vue'

  const INITIAL_DATA = {
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
    props: {
      'stream-eid': {
        type: String,
        required: true
      },
      'content-url': {
        type: String,
        required: true
      },
      'is-html': {
        type: Boolean,
        default: false
      },
      'is-json': {
        type: Boolean,
        default: false
      },
      'query-params': {
        type: Object
      }
    },
    components: {
      Spinner,
      CodeEditor
    },
    data() {
      return {
        state: _.assign({}, INITIAL_DATA)
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
        this.state = _.assign({}, INITIAL_DATA)

        if (this.isJson)
          this.fetchAll()
           else
          this.fetchChunk()
      },
      fetchAll() {
        return this.fetchChunk(true)
      },
      fetchChunk(fetch_all) {
        var state = this.state

        var fetch_url = this.contentUrl
        var params = _.assign({}, this.queryParams)

        if (fetch_all !== true)
        {
          _.assign(params, {
            start: state.total_fetched,
            limit: state.chunk_size,
            format: 'data'
          })
        }

        state.is_fetching = true

        axios.get(fetch_url, { params }).then(response => {
          var str = response.data

          if (fetch_all === true || str.length < this.chunk_size)
            state.eof = true

          state.total_fetched += str.length

          if (_.isObject(str))
          {
            str = JSON.stringify(str, null, 2)
          }
           else
          {
            // force string
            if (!_.isString(str))
              str = ''+str

            // since we're outputting raw text in a <pre> tag,
            // I don't think we want to escape the text here
            //str = _.escape(str)
            str = str.replace(/\r/g, '\r\n')
            str = str.replace(/\r\n\n/g, '\r\n')
          }

          state.text += str

          state.is_fetching = false
          state.has_fetched = true
        }).catch(response => {
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

<style lang="stylus" scoped>
  pre
    line-height: 1.3
</style>
