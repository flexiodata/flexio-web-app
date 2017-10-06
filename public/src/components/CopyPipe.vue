<template>
  <div class="flex flex-row items-center justify-center">
    <div v-if="is_loading">
      <div class="flex flex-column justify-center h-100">
        <spinner size="large" :message="loading_message"></spinner>
      </div>
    </div>
    <div class="mw8 mt4 self-start">
      <div class="dark-gray marked" v-html="error_markup" v-if="error_markdown.length > 0"></div>
    </div>
  </div>
</template>

<script>
  import axios from 'axios'
  import marked from 'marked'
  import { ROUTE_PIPES } from '../constants/route'
  import { PIPEHOME_VIEW_BUILDER, PIPEHOME_STATUS_CONFIGURE } from '../constants/pipehome'
  import Spinner from 'vue-simple-spinner'

  export default {
    components: {
      Spinner
    },
    data() {
      return {
        is_loading: true,
        pipe_json: {},
        error_markdown: ''
      }
    },
    computed: {
      json_filename() {
        return _.get(this.$route, 'query.path', '')
      },
      short_json_filename() {
        var idx = this.json_filename.lastIndexOf('/')
        return this.json_filename.substring(idx > 0 ? idx+1 : 0)
      },
      loading_message() {
        return 'Loading '+this.short_json_filename+'...'
      },
      error_markup() {
        return marked(this.error_markdown)
      }
    },
    mounted() {
      this.loadPipeFromJsonFile()
    },
    methods: {
      tryCreatePipe(attrs) {
        this.$store.dispatch('createPipe', { attrs }).then(response => {
          if (response.ok)
          {
            var pipe = response.body
            var analytics_payload = _.pick(pipe, ['eid', 'name', 'description', 'ename'])

            // add Segment-friendly keys
            _.assign(analytics_payload, {
              createdAt: _.get(pipe, 'created')
            })

            // add template file
            _.assign(analytics_payload, {
              template: this.json_filename
            })

            analytics.track('Created Pipe: Template', analytics_payload)
            this.openPipe(response.body.eid)
          }
           else
          {
            analytics.track('Created Pipe: Template (Error)', { template: this.json_filename })
          }
        })
      },
      loadPipeFromJsonFile() {
        if (this.json_filename.length > 0)
        {
          axios.get(this.json_filename).then(response => {
            this.pipe_json = _.assign({}, response.data)
            this.tryCreatePipe(this.pipe_json)
          }).catch(response => {
            analytics.track('Created Pipe: Template (File Not Found)', { template: this.json_filename })
            this.is_loading = false
            this.error_markdown =
              '# File not found\n\n' +
              'We were unable to load the pipe from the following file:\n' +
              '##### '+this.json_filename+'\n' +
              'To copy the pipe to your project, make sure the file exists and that you have access to it.'
          })
        }
         else
        {
          this.is_loading = false
          this.error_markdown =
            '# No file specified\n\n' +
            'We were unable to load the pipe because no file was specified.\n\n' +
            'To copy the pipe to your project, make sure a filename is specified in the `path` query parameter.'
        }
      },
      openPipe(eid) {
        this.$router.replace({
          name: ROUTE_PIPES,
          params: {
            eid,
            view: PIPEHOME_VIEW_BUILDER,
            state: PIPEHOME_STATUS_CONFIGURE // jump right into configuration
          }
        })
      }
    }
  }
</script>
