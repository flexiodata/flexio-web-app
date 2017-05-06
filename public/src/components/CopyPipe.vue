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
  import { mapState, mapGetters } from 'vuex'
  import { ROUTE_PIPEHOME } from '../constants/route'
  import { PIPEHOME_VIEW_BUILDER, PIPEHOME_STATUS_CONFIGURE } from '../constants/pipehome'
  import Spinner from 'vue-simple-spinner'
  import SetActiveProject from './mixins/set-active-project'

  export default {
    mixins: [SetActiveProject],
    components: {
      Spinner
    },
    watch: {
      active_user_eid: function(val, old_val) {
        this.tryFetchProjects()
      }
    },
    data() {
      return {
        is_loading: true,
        is_pipe_created: false,
        is_project_fetched: false,
        pipe_json: {},
        error_markdown: ''
      }
    },
    computed: {
      ...mapState([
        'active_user_eid'
      ]),
      json_filename() {
        return _.get(this.$route, 'query.path', '')
      },
      short_json_filename() {
        var idx = this.json_filename.lastIndexOf('/')
        return this.json_filename.substring(idx > 0 ? idx+1 : 0)
      },
      default_project() {
        return _
          .chain(this.getActiveUserProjects())
          .sortBy([ function(p) { return new Date(p.created) } ])
          .first()
          .value()
      },
      loading_message() {
        return 'Loading '+this.short_json_filename+'...'
      },
      error_markup() {
        return marked(this.error_markdown)
      }
    },
    mounted() {
      if (this.active_user_eid.length > 0)
        this.tryFetchProjects()
    },
    methods: {
      ...mapGetters([
        'hasProjects',
        'getActiveUserProjects'
      ]),
      tryFetchProjects() {
        // only fetch projects once
        if (this.is_project_fetched)
          return

        if (!this.hasProjects())
        {
          this.$store.dispatch('fetchProjects').then(response => {
            if (response.ok)
            {
              this.is_project_fetched = true
              this.loadPipeFromJsonFile()
            }
          })
        }
         else
        {
          this.is_project_fetched = true
          this.loadPipeFromJsonFile()
        }
      },
      tryCreatePipe(attrs) {
        // only create the pipe once
        if (this.is_pipe_created)
          return

        var parent_eid = _.get(this.default_project, 'eid', '')

        if (parent_eid.length == 0 || _.size(attrs) == 0)
          return

        // start in the target project
        this.setActiveProject(parent_eid)

        // add (project) parent eid to the create attributes
        _.assign(attrs, { parent_eid })

        this.$store.dispatch('createPipe', { attrs }).then(response => {
          if (response.ok)
          {
            this.is_pipe_created = true
            this.openPipe(response.body.eid)
          }
           else
          {
            // TODO: add error handling
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
          name: ROUTE_PIPEHOME,
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
