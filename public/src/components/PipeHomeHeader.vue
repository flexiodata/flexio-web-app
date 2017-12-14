<template>
  <nav class="z-1" style="box-shadow: 0 1px 4px rgba(0,0,0,0.125)">
    <div class="flex flex-row bg-white pa2 pl3-ns pr2-ns items-start" style="min-height: 54px">
      <router-link
        to="/pipes"
        class="flex flex-row items-center link mid-gray hover-black pv1"
      >
        <div class="hint--bottom-right" style="margin-top: -1px" aria-label="Back to pipe list">
          <i class="material-icons md-24">home</i>
        </div>
      </router-link>
      <i class="material-icons md-24 black-20 rotate-270 pv1 nl1 ml0-ns" style="margin-top: -1px">arrow_drop_down</i>
      <div class="flex-fill mb1 mr2">
        <div class="flex flex-column flex-row-l items-center-l">
          <inline-edit-text
            class="dib f3 lh-title v-mid dark-gray mb1 mb0-l mr3-l"
            input-key="name"
            tooltip-cls="hint--bottom"
            :val="pipe_name"
            :show-edit-button="false"
            @save="editPipeName">
          </inline-edit-text>
          <div class="flex flex-row items-center">
            <inline-edit-text
              class="dib f7 v-mid silver pv1 ph2 mr1 bg-black-05"
              placeholder="Add an alias"
              input-key="ename"
              tooltip-cls="hint--bottom"
              :val="pipe_ename"
              :show-edit-button="false"
              @save="editPipeAlias">
            </inline-edit-text>
            <div
              class="hint--bottom hint--large cursor-default"
              aria-label="When using the Flex.io command line interface (CLI) or API, pipes may be referenced either via their object ID or via an alias created here. Aliases are unique across the app, so we recommend prefixing your username to the alias (e.g., username-foo)."
              v-if="pipe_ename.length == 0"
            >
              <i class="material-icons blue v-mid" style="font-size: 21px">info</i>
            </div>
          </div>
        </div>
        <inline-edit-text
          class="f6 lh-copy gray mw7 mt1 dn db-l"
          placeholder="Add a description"
          placeholder-cls="fw6 black-20 hover-black-40"
          input-key="description"
          tooltip-cls="hint--bottom"
          :val="pipe_description"
          :show-edit-button="false"
          @save="editPipeDescription">
        </inline-edit-text>
      </div>
      <div class="flex-none flex flex-column flex-row-ns items-end items-center-ns">
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
        <div class="dn db-ns flex-none mh2">
          <div v-if="user_fetching"></div>
          <user-dropdown v-else-if="logged_in"></user-dropdown>
          <div v-else>
            <router-link to="/signin" class="link underline-hover dib f6 f6-ns ttu b black-60 ph2 pv1 mr1 mr2-ns">Sign in</router-link>
            <router-link to="/signup" class="link no-underline dib f6 f6-ns ttu b br1 white bg-orange darken-10 ph2 ph3-ns pv2 mv1">
              <span class="di dn-ns">Sign up</span>
              <span class="dn di-ns">Sign up for free</span>
            </router-link>
          </div>
        </div>
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
  </nav>
</template>

<script>
  import { mapState, mapGetters } from 'vuex'
  import { PIPEHOME_VIEW_BUILDER } from '../constants/pipehome'
  import { TASK_TYPE_INPUT } from '../constants/task-type'
  import Btn from './Btn.vue'
  import InlineEditText from './InlineEditText.vue'
  import UserDropdown from './UserDropdown.vue'
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
      UserDropdown,
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
      ...mapState([
        'user_fetching'
      ]),
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
      },
      user_eid() {
        return _.get(this.getActiveUser(), 'eid', '')
      },
      logged_in() {
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
            analytics.track('Updated Pipe: Name', { eid, name })
          }
           else
          {
            input.endEdit(false)
            analytics.track('Updated Pipe: Name (Error)', { eid, name })
          }
        })
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
            analytics.track('Updated Pipe: Alias (Invalid)', { eid, ename })

            // show error message
            this.show_alert_modal = true
            this.$nextTick(() => { this.$refs['modal-alert'].open() })
          }
           else
          {
            this.$store.dispatch('updatePipe', { eid, attrs }).then(response => {
              if (response.ok)
              {
                input.endEdit()
                analytics.track('Updated Pipe: Alias', { eid, ename })
              }
               else
              {
                input.endEdit(false)
                analytics.track('Updated Pipe: Alias (Error)', { eid, ename })
              }
            })
          }
        })
      },
      editPipeDescription(attrs, input) {
        var eid = this.pipeEid
        var description = _.get(attrs, 'description', '')

        this.$store.dispatch('updatePipe', { eid, attrs }).then(response => {
          if (response.ok)
          {
            input.endEdit()
            analytics.track('Updated Pipe: Description', { eid, description })
          }
           else
          {
            input.endEdit(false)
            analytics.track('Updated Pipe: Description (Error)', { eid, description })
          }
        })
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
