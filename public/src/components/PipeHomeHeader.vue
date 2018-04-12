<template>
  <nav class="z-1" style="box-shadow: 0 1px 4px rgba(0,0,0,0.125)">
    <div class="flex flex-row items-center bg-white pa1 ph3-ns" style="min-height: 54px">
      <div class="flex-fill flex flex-row items-center">
        <router-link to="/pipes" class="flex flex-row items-center link mid-gray hover-black">
          <i class="material-icons md-24 hint--bottom-right" aria-label="Back to pipe list">home</i>
        </router-link>
        <i class="material-icons md-24 black-20 rotate-270" v-if="is_signed_in && !pipeOptions.fetchError">expand_more</i>
        <inline-edit-text
          class="dib lh-title f6 fw6 f4-ns fw4-ns mid-gray hover-black mr3-l"
          input-key="name"
          tooltip-cls="hint--bottom"
          :val="pipe_name"
          :show-edit-button="false"
          @save="editPipeName"
          v-if="is_signed_in && !pipeOptions.fetchError"
        />
        <div v-if="is_signed_in && !pipeOptions.fetchError">
          <div class="flex flex-row items-center">
            <inline-edit-text
              class="dib f7 silver pv1 ph2 mr1 bg-black-05"
              placeholder="Add an alias"
              input-key="alias"
              tooltip-cls="hint--bottom"
              :val="pipe_alias"
              :show-edit-button="false"
              @change="onAliasChange"
              @cancel="cancelEditPipeAlias"
              @save="editPipeAlias"
            />
            <div
              class="hint--bottom hint--large cursor-default"
              aria-label="Pipes can be referenced via an alias in the Flex.io command line interface (CLI), all SDKs as well as the REST API."
              v-if="pipe_alias.length == 0"
            >
              <i class="material-icons blue v-mid" style="font-size: 21px">info</i>
            </div>
          </div>
          <div class="dark-red f7" v-if="alias_error.length > 0">{{alias_error}}</div>
        </div>
      </div>
      <div class="flex-none flex flex-column flex-row-ns items-end items-center-ns" v-if="is_signed_in && !pipeOptions.fetchError">
        <el-button size="small" type="primary" class="ttu b" @click="cancelProcess" v-if="isPrompting || isProcessRunning">Cancel</el-button>
        <el-button size="small" type="primary" class="ttu b" :disabled="tasks.length == 0" @click="runPipe" v-else>Run</el-button>
      </div>
      <div class="dn db-ns flex-none ml3">
        <div v-if="user_fetching"></div>
        <user-dropdown v-else-if="is_signed_in"></user-dropdown>
        <div v-else>
          <router-link to="/signin" class="link underline-hover dib f6 f6-ns ttu b black-60 ph2 pv1 mr1 mr2-ns">Sign in</router-link>
          <router-link to="/signup" class="link no-underline dib f6 f6-ns ttu b br1 white bg-orange darken-10 ph2 ph3-ns pv2 mv1">
            <span class="di dn-ns">Sign up</span>
            <span class="dn di-ns">Sign up for free</span>
          </router-link>
        </div>
      </div>
    </div>
  </nav>
</template>

<script>
  import { mapState, mapGetters } from 'vuex'
  import { OBJECT_TYPE_PIPE } from '../constants/object-type'
  import { PIPEHOME_VIEW_SDK_JS, PIPEHOME_VIEW_BUILDER } from '../constants/pipehome'
  import { TASK_OP_INPUT } from '../constants/task-op'
  import InlineEditText from './InlineEditText.vue'
  import UserDropdown from './UserDropdown.vue'
  import Validation from './mixins/validation'

  const pipe_view_options = [
    { val: PIPEHOME_VIEW_SDK_JS,  label: 'Javascript SDK' },
    { val: PIPEHOME_VIEW_BUILDER, label: 'Builder'        }
  ]

  export default {
    props: {
      'pipe-options': {
        type: Object,
        default: () => { return {} }
      },
      'pipe-view': {
        type: String,
        required: true
      },
      'tasks': {
        type: Array,
        required: true
      },
      'is-prompting': {
        type: Boolean,
        default: false
      },
      'is-process-running': {
        type: Boolean,
        default: false
      }
    },
    mixins: [Validation],
    components: {
      InlineEditText,
      UserDropdown
    },
    inject: ['pipeEid'],
    watch: {
      pipeView(val) {
        this.pipe_view = val
      }
    },
    data() {
      return {
        pipe_view_options,
        pipe_view: this.pipeView,
        ss_errors: {}
      }
    },
    computed: {
      ...mapState([
        'user_fetching'
      ]),
      pipe() {
        return _.get(this.$store, 'state.objects.'+this.pipeEid, {})
      },
      pipe_name() {
        return _.get(this.pipe, 'name', '')
      },
      pipe_alias() {
        return _.get(this.pipe, 'alias', '')
      },
      alias_error() {
        return _.get(this.ss_errors, 'alias.message', '')
      },
      user_eid() {
        return _.get(this.getActiveUser(), 'eid', '')
      },
      is_signed_in() {
        return this.user_eid.length > 0
      }
    },
    methods: {
      ...mapGetters([
        'getActiveUser'
      ]),
      setPipeView(view) {
        this.$emit('set-pipe-view', view)
      },
      editPipeName(attrs, input) {
        var eid = this.pipeEid
        var name = _.get(attrs, 'name', '')

        this.$store.dispatch('updatePipe', { eid, attrs }).then(response => {
          if (response.ok)
          {
            input.endEdit()
            this.$store.track('Updated Pipe: Name', { eid, name })
          }
           else
          {
            input.endEdit(false)
            this.$store.track('Updated Pipe: Name (Error)', { eid, name })
          }
        })
      },
      editPipeAlias(attrs, input) {
        var eid = this.pipeEid
        var alias = _.get(attrs, 'alias', '')

        this.validateAlias(OBJECT_TYPE_PIPE, alias, (response, errors) => {
          var errors = _.omitBy(errors, (e) => { return _.get(e, 'valid') })

          this.ss_errors = alias.length > 0 && _.size(errors) > 0
            ? _.assign({}, errors)
            : _.assign({})

          if (alias.length > 0 && _.size(errors) > 0)
          {
            this.$store.track('Updated Pipe: Alias (Invalid)', { eid, alias })
          }
           else
          {
            this.$store.dispatch('updatePipe', { eid, attrs }).then(response => {
              if (response.ok)
              {
                input.endEdit()
                this.$store.track('Updated Pipe: Alias', { eid, alias })
              }
               else
              {
                input.endEdit(false)
                this.$store.track('Updated Pipe: Alias (Error)', { eid, alias })
              }
            })
          }
        })
      },
      cancelEditPipeAlias() {
        this.ss_errors = _.assign({})
      },
      runPipe() {
        this.$emit('run-pipe')
      },
      cancelProcess() {
        this.$emit('cancel-process')
      },
      onAliasChange(alias) {
        if (alias == this.pipe_alias)
        {
          this.ss_errors = _.omit(this.ss_errors, ['alias'])
          return
        }

        this.validateAlias(OBJECT_TYPE_PIPE, alias, (response, errors) => {
          this.ss_errors = alias.length > 0 && _.size(errors) > 0
            ? _.assign({}, errors)
            : _.assign({})
        })
      }
    }
  }
</script>
