<template>
  <div
    class="flex flex-column justify-center h-100"
    v-if="is_fetching && !has_fetched"
  >
    <Spinner size="large" :message="loading_text" />
  </div>

  <CodeEditor
    class="h-100 overflow-auto"
    lang="text/html"
    :options="{
      lineNumbers: false,
      readOnly: true
    }"
    v-model="text"
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
    v-model="text"
    v-else-if="isJson"
  />

  <pre
    class="monospace ma0 pa2 h-100 overflow-auto"
    @scroll="onScroll"
    v-else
  >{{text}}</pre>
</template>

<script>
  import axios from 'axios'
  import Spinner from 'vue-simple-spinner'
  import CodeEditor from '@/components/CodeEditor'

  const getInitialState = () => {
    return {
      is_fetching: false,
      has_fetched: false,
      loading_text: 'Loading text...',
      total_fetched: 0,
      chunk_size: 100000,
      text_scroll_top: 0,
      eof: false,
      text: ''
    }
  }

  export default {
    props: {
      streamEid: {
        type: String,
        required: true
      },
      contentUrl: {
        type: String,
        required: true
      },
      isHtml: {
        type: Boolean,
        default: false
      },
      isJson: {
        type: Boolean,
        default: false
      },
      queryParams: {
        type: Object
      }
    },
    components: {
      Spinner,
      CodeEditor
    },
    data() {
      return getInitialState()
    },
    watch: {
      streamEid: {
        immediate: true,
        handler: 'initialFetch'
      }
    },
    methods: {
      initialFetch() {
        // reset our local component data
        _.assign(this.$data, getInitialState())

        if (this.isJson) {
          this.fetchAll()
        } else {
          this.fetchChunk()
        }
      },
      fetchAll() {
        return this.fetchChunk(true)
      },
      fetchChunk(fetch_all) {
        var fetch_url = this.contentUrl
        var params = _.assign({}, this.queryParams)

        if (fetch_all !== true) {
          _.assign(params, {
            start: this.total_fetched,
            limit: this.chunk_size,
            format: 'data'
          })
        }

        this.is_fetching = true

        axios.get(fetch_url, { params }).then(response => {
          var str = response.data

          if (fetch_all === true || str.length < this.chunk_size) {
            this.eof = true
          }

          this.total_fetched += str.length

          if (_.isObject(str)) {
            str = JSON.stringify(str, null, 2)
          } else {
            // force string
            if (!_.isString(str)) {
              str = ''+str
            }

            // since we're outputting raw text in a <pre> tag,
            // I don't think we want to escape the text here
            //str = _.escape(str)
            str = str.replace(/\r/g, '\r\n')
            str = str.replace(/\r\n\n/g, '\r\n')
          }

          this.text += str

          this.is_fetching = false
          this.has_fetched = true
        }).catch(error => {
          this.is_fetching = false
          this.has_fetched = true
        })
      },
      onScroll(evt) {
        if (this.is_fetching) {
          return
        }

        var scroll_top = evt.target.scrollTop
        var scroll_height = evt.target.scrollHeight
        var cli_height = evt.target.clientHeight

        // only handle vertical scrolling
        if (scroll_top == this.text_scroll_top)
          return

        this.text_scroll_top = scroll_top

        if (this.eof === true) {
          return
        }

        if (scroll_top+cli_height > scroll_height*0.75) {
          this.fetchChunk()
        }
      }
    }

  }
</script>

<style lang="stylus" scoped>
  pre
    line-height: 1.3
</style>
