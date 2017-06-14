<template>
  <div class="flex flex-row items-start min-w5">
    <router-link class="pv1 dark-gray hover-blue css-back-button" to="/home/pipes">
      <div class="hint--bottom-right" aria-label="Back to pipe list">
        <i class="material-icons md-24">chevron_left</i>
      </div>
    </router-link>
    <div class="flex-fill mb1 mr2">
      <div class="flex flex-column flex-row-l items-center-l">
        <inline-edit-text
          class="dib f3 lh-title v-mid dark-gray mb1 mb0-l mr2-l"
          input-key="name"
          :val="pipe_name"
          @save="editPipeName">
        </inline-edit-text>
        <div class="flex flex-row items-center">
          <inline-edit-text
            class="dib f7 v-mid silver bg-black-05 pv1 ph2 mr1"
            placeholder="Add an alias"
            input-key="ename"
            :val="pipe_ename"
            :show-edit-button="false"
            @save="editPipeAlias">
          </inline-edit-text>
          <div
            class="hint--bottom hint--large cursor-default"
            aria-label="When using the Flex.io command line interface (CLI) or API, pipes may be referenced either via their object ID or via an alias created here. Aliases are unique across the app, so we recommend prefixing your username to the alias (e.g., username-foo)."
            v-if="pipe_ename.length == 0"
          >
            <i class="material-icons blue md-18">info</i>
          </div>
        </div>
      </div>
      <inline-edit-text
        class="f6 lh-title gray mt1"
        placeholder="Add a description"
        placeholder-cls="fw6 black-20 hover-black-40"
        input-key="description"
        :val="pipe_description"
        @save="editPipeDescription">
      </inline-edit-text>
    </div>
    <div class="flex-none flex flex-column flex-row-ns items-end items-center-ns">
      <div
        class="f6 fw6 dark-gray pointer mr3-ns dn db-ns bb bw1 ttu css-nav-text"
        :class="[pipeView=='overview'?'b--blue':'b--transparent']"
        @click="setPipeView('overview')"
        v-if="!isPrompting && !isProcessRunning"
      >Pipe Overview</div>
      <div
        class="f6 fw6 dark-gray pointer mr3-ns dn db-ns bb bw1 ttu css-nav-text"
        :class="[pipeView=='builder'?'b--blue':'b--transparent']"
        @click="setPipeView('builder')"
        v-if="!isPrompting && !isProcessRunning"
      >Pipe Builder</div>
      <btn
        btn-md
        btn-primary
        class="ttu b mr2"
        @click="openCopyPipeModal"
        v-if="is_superuser"
      >Get Template JSON</btn>
      <btn
        btn-md
        btn-primary
        class="ttu b"
        @click="cancelProcess"
        v-if="isPrompting || isProcessRunning"
      >Cancel</btn>
      <div
        class="hint--bottom-left"
        :aria-label="run_button_tooltip"
        v-else
      >
        <btn
          btn-md
          btn-primary
          class="ttu b"
          :disabled="!is_run_allowed"
          @click="runPipe"
        >Run</btn>
      </div>
      <div
        class="f7 fw6 dark-gray pointer mt2 db dn-ns bb bw1 ttu css-nav-text"
        :class="[pipeView=='overview'?'b--blue':'b--transparent']"
        @click="setPipeView('overview')"
      >Pipe Overview</div>
      <div
        class="f7 fw6 dark-gray pointer mt2 db dn-ns bb bw1 ttu css-nav-text"
        :class="[pipeView=='builder'?'b--blue':'b--transparent']"
        @click="setPipeView('builder')"
      >Pipe Builder</div>
    </div>

    <!-- copy pipe modal -->
    <copy-pipe-modal
      ref="modal-copy-pipe"
      @hide="show_copy_pipe_modal = false"
      v-if="show_copy_pipe_modal"
    ></copy-pipe-modal>

    <!-- alert modal -->
    <alert-modal
      ref="modal-alert"
      title="Error"
      @hide="show_alert_modal = false"
      v-if="show_alert_modal"
    >
      <div class="lh-copy">{{ename_error}}</div>
    </alert-modal>
  </div>
</template>

<script>
  import { mapGetters } from 'vuex'
  import { PIPEHOME_VIEW_BUILDER } from '../constants/pipehome'
  import { TASK_TYPE_INPUT } from '../constants/task-type'
  import Btn from './Btn.vue'
  import InlineEditText from './InlineEditText.vue'
  import CopyPipeModal from './CopyPipeModal.vue'
  import AlertModal from './AlertModal.vue'
  import Validation from './mixins/validation'

  export default {
    props: {
      'pipe-eid': {
        type: String,
        required: true
      },
      'pipe-view': {
        type: String
      },
      'is-prompting': {
        type: Boolean,
        default: false
      },
      'is-process-running': {
        type: Boolean
      }
    },
    mixins: [Validation],
    components: {
      Btn,
      InlineEditText,
      CopyPipeModal,
      AlertModal
    },
    data() {
      return {
        show_copy_pipe_modal: false,
        show_alert_modal: false,
        ss_errors: {}
      }
    },
    computed: {
      pipe() {
        return _.get(this.$store, 'state.objects.'+this.pipeEid, {})
      },
      pipe_name() {
        return _.get(this.pipe, 'name', '')
      },
      pipe_ename() {
        return _.get(this.pipe, 'ename', '')
      },
      pipe_description() {
        return _.get(this.pipe, 'description', '')
      },
      tasks() {
        return _.get(this.pipe, 'task', [])
      },
      is_superuser() {
        // limit to @flex.io users for now
        var user_email = _.get(this.getActiveUser(), 'email', '')
        return _.includes(user_email, '@flex.io') && _.get(this.$route, 'query.su', false) !== false
      },
      input_tasks() {
        return _.filter(this.tasks, { type: TASK_TYPE_INPUT })
      },
      is_run_allowed() {
        return this.tasks.length > 0
      },
      run_button_tooltip() {
        return ''
      },
      ename_error() {
        return _.get(this.ss_errors, 'ename.message', '')
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

        this.$store.dispatch('updatePipe', { eid, attrs }).then(response => {
          if (response.ok)
            analytics.track('Updated Pipe: Name', { eid, attrs })
             else
            analytics.track('Updated Pipe: Name (Error)', { eid, attrs })
        })
        input.endEdit()
      },
      editPipeAlias(attrs, input) {
        var eid = this.pipeEid
        var ename = _.get(attrs, 'ename', '')

        this.validateEname(ename, (response, errors) => {
          var errors = _.omitBy(errors, (e) => { return _.get(e, 'valid') })

          this.ss_errors = ename.length > 0 && _.size(errors) > 0
            ? _.assign({}, errors)
            : _.assign({})

          if (ename.length > 0 && _.size(errors) > 0)
          {
            analytics.track('Updated Pipe: Alias (Invalid)', { eid, attrs })

            // show error message
            this.show_alert_modal = true
            this.$nextTick(() => { this.$refs['modal-alert'].open() })
          }
           else
          {
            this.$store.dispatch('updatePipe', { eid, attrs }).then(response => {
              if (response.ok)
                analytics.track('Updated Pipe: Alias', { eid, attrs })
                 else
                analytics.track('Updated Pipe: Alias (Error)', { eid, attrs })
            })

            input.endEdit()
          }
        })
      },
      editPipeDescription(attrs, input) {
        var eid = this.pipeEid

        this.$store.dispatch('updatePipe', { eid, attrs }).then(response => {
          if (response.ok)
            analytics.track('Updated Pipe: Description', { eid, attrs })
             else
            analytics.track('Updated Pipe: Description (Error)', { eid, attrs })
        })

        input.endEdit()
      },
      runPipe() {
        this.setPipeView(PIPEHOME_VIEW_BUILDER)
        this.$emit('run-pipe')
      },
      cancelProcess() {
        this.$emit('cancel-process')
      },
      openCopyPipeModal() {
        this.show_copy_pipe_modal = true
        this.$nextTick(() => { this.$refs['modal-copy-pipe'].open(this.pipe) })
      }
    }
  }
</script>

<style lang="less">
  @import "../stylesheets/variables.less";

  .css-nav-text {
    transition: border 0.3s ease-in-out;
    padding: 3px 2px 2px;

    &:hover,
    &.css-nav-active {
      border-bottom: 2px solid @blue;
    }
  }

  .css-back-button {
    margin-left: -12px;
  }

  @media @breakpoint-large {
    .css-back-button {
      margin-left: -24px;
    }
  }
</style>
