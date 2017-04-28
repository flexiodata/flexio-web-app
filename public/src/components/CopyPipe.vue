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
  import { ROUTE_PIPEHOME } from '../constants/route'
  import { PIPEHOME_VIEW_BUILDER, PIPEHOME_STATUS_CONFIGURE } from '../constants/pipehome'
  import { mapGetters } from 'vuex'
  import Spinner from 'vue-simple-spinner'
  import axios from 'axios'
  import marked from 'marked'
  import setActiveProject from './mixins/set-active-project'

  export default {
    mixins: [setActiveProject],
    components: {
      Spinner
    },
    watch: {
      default_project(val, old_val) {
        this.tryCreatePipe(this.pipe_json)
      }
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
    created() {
      this.tryFetchProjects()
    },
    mounted() {
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
    methods: {
      ...mapGetters([
        'hasProjects',
        'getActiveUserProjects'
      ]),
      tryFetchProjects() {
        if (!this.hasProjects())
          this.$store.dispatch('fetchProjects')
      },
      tryCreatePipe(attrs) {
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
            this.openPipe(response.body.eid)
          }
           else
          {
            // TODO: add error handling
          }
        })
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

<style lang="less">
  .marked {
    h1 {
      font-weight: normal;
      margin-top: 0;
    }

    h2,
    h3,
    h4 {
      font-weight: 600;
    }

    p {
      line-height: 1.5;
    }

    p + h3,
    ul + h3 {
      margin-top: 3rem;
    }

    ul > li {
      line-height: 1.5;
    }

    code {
      padding: 0;
      padding-top: 0.2rem;
      padding-bottom: 0.2rem;
      margin: 0;
      font-size: 85%;
      background-color: rgba(27,31,35,0.05);
      border-radius: 3px;

      &::before,
      &::after {
        letter-spacing: -0.2em;
        content: "\00a0";
      }
    }
  }
</style>
