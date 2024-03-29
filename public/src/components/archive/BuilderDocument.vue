<template>
  <!-- fetching -->
  <div class="bg-nearer-white" v-if="is_fetching">
    <div class="flex flex-column justify-center h-100">
      <Spinner size="large" message="Loading..." />
    </div>
  </div>

  <!-- fetched -->
  <div
    class="bg-nearer-white ph4 overflow-y-scroll relative"
    :id="doc_id"
    v-else-if="is_fetched"
  >
    <!-- use `z-7` to ensure the title z-index is greater than the CodeMirror scrollbar -->
    <div class="mt4 mb3 relative z-7 bg-nearer-white">
      <div class="center tc mw-builder-doc" v-if="show_title">
        <h1 class="mv0 pv3 fw4">{{title}}</h1>
      </div>
      <div class="h1" v-else></div>
      <div class="center mw-builder-doc" v-if="show_description">
        <p class="mt0 mb4 pv3 lh-copy">{{description}}</p>
      </div>
    </div>
    <div
      class="center mw-builder-doc"
      style="padding-bottom: 8rem"
    >
      <BuilderList
        class="flex-fill"
        builder-mode="wizard"
        :items="prompts"
        :container-id="doc_id"
        :active-item-idx.sync="active_prompt_idx"
        :show-numbers="true"
        :show-icons="false"
        :show-insert-buttons="false"
        :show-edit-buttons="false"
        :show-delete-buttons="false"
        @item-prev="onPrev"
        @item-next="onNext"
        @item-change="updateItemState"
        @create-pipe="createPipe"
        @open-pipe="openPipe"
      />

      <!-- output panel for dev testing only -->
      <div
        class="fixed z-8 dn db-l ml4 pa3 bg-white br2 css-white-box css-output"
        v-if="false"
      >
        <div class="flex-none pb2 mb2 bb b--black-10">
          <div class="fw6">Output</div>
        </div>
        <CodeEditor
          style="height: 24rem"
          lang="javascript"
          :options="{
            lineNumbers: false,
            readOnly: true
          }"
          v-model="task_str"
        />
      </div>
    </div>
  </div>
</template>

<script>
  import axios from 'axios'
  import yaml from 'js-yaml'
  import stickybits from 'stickybits'
  import { mapState, mapGetters } from 'vuex'
  import { ROUTE_APP_BUILDER, ROUTE_APP_PIPES } from '../constants/route'
  import { PROCESS_MODE_BUILD } from '../constants/process'
  import Flexio from 'flexio-sdk-js'
  import Spinner from 'vue-simple-spinner'
  import BuilderList from '@comp/BuilderList'
  import CodeEditor from '@comp/CodeEditor'
  import test_def from '../data/builder/test-def.yml'

  const buildTestDefinition = () => {
    var def = _.cloneDeep(test_def)

    // easy way to get rid of a bunch of elements for quick testing
    //def.ui.prompts = _.filter(def.ui.prompts, { element: 'form' })

    // builder up mock task array for variable replacement
    var task_obj = {
      op: 'sequence',
      items: []
    }

    var buildPipeCode = (arr) => {
      _.each(arr, p => {
        if (p.element == 'form') {
          buildPipeCode(p.form_items)
        } else if (p.variable) {
          var echo_obj = {
             op: 'echo',
             msg: '${' + p.variable + '}',
             variable_name: p.variable
          }
          task_obj.items.push(echo_obj)
        }
      })
    }
    buildPipeCode(def.ui.prompts)

    def.task = task_obj
    return def
  }

  export default {
    metaInfo() {
      return {
        title: _.get(this.def, 'name', 'Builder')
      }
    },
    props: {
      definition: {
        type: Object,
        required: false
      }
    },
    components: {
      Spinner,
      BuilderList,
      CodeEditor
    },
    watch: {
      slug: {
        handler: 'loadTemplate',
        immediate: true
      },
      definition: {
        handler: 'initFromDefiniton',
        deep: true
      },
      active_prompt_idx: {
        handler: 'updateTask',
        immediate: true
      },
      is_fetched: {
        handler: 'initSticky',
        immediate: true
      }
    },
    data() {
      return {
        doc_id: _.uniqueId('builder-doc-')
      }
    },
    computed: {
      ...mapState({
        def: state => state.builderdoc.def,
        task: state => state.builderdoc.task,
        attrs: state => state.builderdoc.attrs,
        title: state => state.builderdoc.def.name,
        description: state => state.builderdoc.def.description,
        active_prompt: state => state.builderdoc.active_prompt,
        is_fetching: state => state.builderdoc.fetching,
        is_fetched: state => state.builderdoc.fetched,
        prompts: state => state.builderdoc.prompts,
        pipe: state => state.builderdoc.pipe,
        active_team_name: state => state.active_team_name
      }),
      is_builder_document() {
        return this.$route.name == ROUTE_APP_BUILDER
      },
      slug() {
        if (this.is_builder_document) {
          return _.get(this.$route, 'params.template', undefined)
        } else {
          return undefined
        }
      },
      // NOTE: this needs to be a computed value with a getter/setter since we're using '.sync'
      active_prompt_idx: {
        get() {
          return this.$store.state.builderdoc.active_prompt_idx
        },
        set(value) {
          // read only
        }
      },
      task_str: {
        get() {
          try {
            return JSON.stringify(this.task, null, 2)
          }
          catch (e) {
            return ''
          }
        },
        set(value) {
          // read only
        }
      },
      save_attrs() {
        return _.assign({
          short_description: _.get(this.def, 'name', 'Untitled Pipe'),
          description: _.get(this.def, 'description', ''),
          ui: _.get(this.def, 'ui', {}),
          task: this.task,
        }, _.get(this.attrs, 'pipe', {}))
      },
      api_key() {
        return this.getFirstToken()
      },
      sdk_options() {
        return this.getSdkOptions()
      },
      show_title() {
        return _.get(this.def, 'name', '').length > 0 && _.get(this.def, 'ui.settings.show_title', true)
      },
      show_description() {
        return _.get(this.def, 'description', '').length > 0 && _.get(this.def, 'ui.settings.show_description', false)
      }
    },
    methods: {
      ...mapGetters([
        'getFirstToken',
        'getSdkOptions'
      ]),
      loadTemplate() {
        this.$store.commit('builderdoc/FETCHING_DEF', true)

        if (!_.isNil(this.definition)) {
          // definition initialization handled in watcher
          this.initFromDefiniton()
          this.$store.commit('builderdoc/FETCHING_DEF', false)
          this.initSticky()
        } else if (this.slug == 'test') {
          this.$store.commit('builderdoc/INIT_ROUTE', this.$route.name)
          this.$store.commit('builderdoc/INIT_DEF', buildTestDefinition())
          this.$store.commit('builderdoc/FETCHING_DEF', false)
          this.initSticky()
        } else {
          var template_url

          if (this.slug == 'external') {
            template_url = _.get(this.$route.query, 'url', '')
          } else {
            template_url = 'https://static.flex.io/templates/builder/' + this.slug + '.yml'
          }

          if (template_url.length > 0) {
            axios.get(template_url).then(response => {
              var yaml_def = response.data
              var def = yaml.safeLoad(yaml_def)
              debugger
              this.$store.commit('builderdoc/INIT_ROUTE', this.$route.name)
              this.$store.commit('builderdoc/INIT_DEF', def)
              this.$store.commit('builderdoc/FETCHING_DEF', false)
              this.$store.track('Started Template', {
                title: def.name
              })
            }).catch(error => {
              this.$store.commit('builderdoc/FETCHING_DEF', false)
            })
          }
        }
      },
      updateTask() {
        this.$store.commit('builderdoc/UPDATE_TASK')
      },
      onPrev() {
        this.$store.commit('builderdoc/GO_PREV_ITEM')
      },
      onNext() {
        var actions = _.get(this.active_prompt, 'next_button.actions', [])
        var create_pipe = actions.indexOf('create_pipe') != -1
        var run_process = actions.indexOf('run_process') != -1

        // only create pipes in builder document
        if (create_pipe && this.is_builder_document) {
          this.createPipe(run_process)
        } else if (run_process) {
          this.runProcess(this.save_attrs)
        } else if (actions.indexOf('open_pipe') != -1) {
          this.openPipe()
        }

        this.$store.commit('builderdoc/GO_NEXT_ITEM')
      },
      createPipe(run_process) {
        var attrs = this.save_attrs
        this.$store.dispatch('v2_action_createPipe', { attrs }).then(response => {
          var pipe = response.data
          this.$store.commit('builderdoc/CREATE_PIPE', pipe)
          this.$store.track('Created Pipe From Template', {
            title: this.def.name
          })

          if (run_process === true) {
            this.$nextTick(() => { this.runProcess(attrs, pipe.eid) })
          }
        }).catch(error => {
          // TODO: add error handling?
        })
      },
      runProcess(attrs, parent_eid) {
        _.assign(attrs, {
          parent_eid,
          process_mode: PROCESS_MODE_BUILD
        })

        this.$store.dispatch('v2_action_createProcess', { attrs }).then(response => {
          var process = response.data
          this.$store.commit('builderdoc/CREATE_PROCESS', process)
          this.$store.track('Ran Process From Template', {
            title: this.def.name
          })

          var eid = process.eid
          this.$store.dispatch('v2_action_runProcess', { eid }).then(response => {
            this.$store.commit('builderdoc/UPDATE_PROCESS_RESULT', response.data)
          }).catch(error => {
            // TODO: add error handling?
          })
        }).catch(error => {
          // TODO: add error handling?
        })
      },
      openPipe() {
        // TODO: this component shouldn't have anything to do with the route or store state
        var ru = this.active_team_name
        var team_name = ru && ru.length > 0 ? ru : null
        var object_name = this.pipe.name

        this.$router.push({ name: ROUTE_APP_PIPES, params: { team_name, object_name } })
      },
      updateItemState(values, index) {
        this.$store.commit('builderdoc/UPDATE_ATTRS', values)
        this.$store.commit('builderdoc/UPDATE_TASK')
      },
      initFromDefiniton() {
        this.$store.commit('builderdoc/INIT_ROUTE', this.$route.name)
        this.$store.commit('builderdoc/INIT_DEF', this.definition)
      },
      initSticky() {
        /*
        setTimeout(() => {
          stickybits('.sticky', {
            scrollEl: '#' + this.doc_id,
            useStickyClasses: true,
            stickyBitStickyOffset: 32
          })
        }, 100)
        */
      }
    }
  }
</script>

<style lang="stylus" scoped>
  .css-output
    top: 66px
    right: 28px
    min-width: 20rem
    max-width: 10%
    opacity: 0.5
    transition: all 0.3s ease-in-out
    &:hover
      max-width: 60rem
      max-height: 60rem
      opacity: 1
</style>
